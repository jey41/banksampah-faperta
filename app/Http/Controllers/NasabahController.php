<?php

namespace App\Http\Controllers;

use App\Models\TrashPrice;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\Withdrawal;
use App\Models\PickupRequest;
use App\Services\TransactionService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class NasabahController extends Controller
{
    public function dashboard(): Response
    {
        $user = auth()->user();

        // Get recent deposits & withdrawals
        $deposits = $user->deposits()->orderBy('created_at', 'desc')->take(5)->get();
        $withdrawals = $user->withdrawals()->orderBy('created_at', 'desc')->take(5)->get();

        // Map and combine them
        $transactions = collect()
            ->concat($deposits->map(fn($d) => [
                'id' => $d->id,
                'type' => 'deposit',
                'title' => $d->is_donation ? 'Donasi Sampah' : 'Setoran Sampah',
                'amount' => $d->total_price,
                'weight' => $d->weight_total,
                'status' => $d->status,
                'date' => $d->created_at->toISOString(),
            ]))
            ->concat($withdrawals->map(fn($w) => [
                'id' => $w->id,
                'type' => 'withdrawal',
                'title' => 'Penarikan Saldo',
                'amount' => $w->amount,
                'status' => $w->status,
                'date' => $w->created_at->toISOString(),
            ]))
            ->sortByDesc('date')
            ->values()
            ->take(5);

        // Sum approved transactions for statistics
        $totalDeposited = $user->deposits()->where('status', 'approved')->sum('total_price');
        $totalWithdrawn = $user->withdrawals()->where('status', 'approved')->sum('amount');

        // Get savings targets, updating their is_achieved status dynamically if achieved
        $targets = $user->savingsTargets()->orderBy('created_at', 'desc')->get();
        foreach ($targets as $target) {
            $isAchievedNow = ($user->saldo >= $target->target_amount);
            if ($target->is_achieved !== $isAchievedNow) {
                $target->update(['is_achieved' => $isAchievedNow]);
            }
        }

        // Get pending pickup requests count
        $pendingPickups = $user->pickupRequests()->whereIn('status', ['pending', 'assigned'])->count();

        // Gamification data
        $gamification = app(GamificationService::class);
        $gamification->syncBadges($user);

        $ecoPoints = $gamification->getEcoPoints($user);
        $level = $gamification->getLevel($ecoPoints);
        $badges = $gamification->getBadges($user);
        $pointsBreakdown = $gamification->getEcoPointsBreakdown($user);

        return Inertia::render('Nasabah/Dashboard', [
            'transactions' => $transactions,
            'totalDeposited' => (int)$totalDeposited,
            'totalWithdrawn' => (int)$totalWithdrawn,
            'targets' => $targets,
            'pendingPickups' => $pendingPickups,
            'ecoPoints' => $ecoPoints,
            'level' => $level,
            'badges' => $badges,
            'pointsBreakdown' => $pointsBreakdown,
        ]);
    }

    public function pickupRequest(): Response
    {
        $user = auth()->user();

        $pickupRequests = $user->pickupRequests()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Nasabah/PickupRequest', [
            'pickupRequests' => $pickupRequests,
            'userAddress' => $user->address ?? '',
            'userPhone' => $user->phone ?? '',
        ]);
    }

    public function storePickupRequest(Request $request)
    {
        $request->validate([
            'pickup_address' => 'required|string|max:1000',
            'pickup_phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|string|in:08:00-10:00,10:00-12:00,13:00-15:00',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_distance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->estimated_distance !== null && $request->estimated_distance > 2.0) {
            return redirect()->back()->withErrors([
                'latitude' => 'Jarak lokasi Anda (' . $request->estimated_distance . ' km) melebihi batas maksimal penjemputan (2 km).',
            ])->withInput();
        }

        auth()->user()->pickupRequests()->create([
            'pickup_address' => $request->pickup_address,
            'pickup_phone' => $request->pickup_phone,
            'pickup_date' => $request->pickup_date,
            'pickup_time' => $request->pickup_time,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'estimated_distance' => $request->estimated_distance,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('nasabah.dashboard')->with('success', 'Permintaan jemput sampah berhasil dibuat!');
    }

    public function withdraw(): Response
    {
        return Inertia::render('Nasabah/Withdraw', [
            'saldo' => auth()->user()->saldo,
        ]);
    }

    public function storeWithdraw(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'amount' => 'required|integer|min:10000|max:' . $user->saldo,
            'withdrawal_method' => 'required|in:tunai,transfer_bank',
            'bank_name' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'bank_type' => 'nullable|in:btn,lainnya',
            'account_number' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'account_name' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check operational hours (08:00-16:00)
        if (!TransactionService::isWithinOperationalHours()) {
            return redirect()->back()->withErrors([
                'withdrawal_method' => 'Pengajuan penarikan hanya dapat dilakukan pada jam operasional 08:00 - 16:00.',
            ])->withInput();
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'withdrawal_method' => $request->withdrawal_method,
            'bank_name' => $request->withdrawal_method === 'tunai' ? 'Tunai' : $request->bank_name,
            'bank_type' => $request->withdrawal_method === 'tunai' ? null : $request->bank_type,
            'account_number' => $request->withdrawal_method === 'tunai' ? '-' : $request->account_number,
            'account_name' => $request->withdrawal_method === 'tunai' ? auth()->user()->name : $request->account_name,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('nasabah.dashboard')->with('success', 'Pengajuan penarikan berhasil dibuat!');
    }

    public function history(): Response
    {
        $user = auth()->user();

        $deposits = $user->deposits()->orderBy('created_at', 'desc')->get();
        $withdrawals = $user->withdrawals()->orderBy('created_at', 'desc')->get();

        $transactions = collect()
            ->concat($deposits->map(fn($d) => [
                'id' => $d->id,
                'type' => 'deposit',
                'title' => $d->is_donation ? 'Donasi Sampah' : 'Setoran Sampah',
                'amount' => $d->total_price,
                'weight' => $d->weight_total,
                'status' => $d->status,
                'date' => $d->created_at->toISOString(),
            ]))
            ->concat($withdrawals->map(fn($w) => [
                'id' => $w->id,
                'type' => 'withdrawal',
                'title' => 'Penarikan Saldo',
                'amount' => $w->amount,
                'status' => $w->status,
                'date' => $w->created_at->toISOString(),
            ]))
            ->sortByDesc('date')
            ->values();

        return Inertia::render('Nasabah/History', [
            'transactions' => $transactions,
        ]);
    }

    public function storeTarget(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|integer|min:10000',
        ]);

        auth()->user()->savingsTargets()->create([
            'title' => $request->title,
            'target_amount' => $request->target_amount,
            'is_achieved' => (auth()->user()->saldo >= $request->target_amount),
        ]);

        return redirect()->back()->with('success', 'Target tabungan berhasil ditambahkan!');
    }

    public function deleteTarget(\App\Models\SavingsTarget $target)
    {
        if ($target->user_id !== auth()->id()) {
            abort(403);
        }

        $target->delete();

        return redirect()->back()->with('success', 'Target tabungan berhasil dihapus!');
    }
}
