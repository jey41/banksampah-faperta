<?php

namespace App\Http\Controllers;

use App\Http\Requests\Nasabah\StorePickupRequestRequest;
use App\Http\Requests\Nasabah\StoreTargetRequest;
use App\Http\Requests\Nasabah\StoreWithdrawRequest;
use App\Models\SavingsTarget;
use App\Models\Withdrawal;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $transactions = $this->mapTransactions($deposits, $withdrawals, 5);

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
            'totalDeposited' => (int) $totalDeposited,
            'totalWithdrawn' => (int) $totalWithdrawn,
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

    public function storePickupRequest(StorePickupRequestRequest $request)
    {
        auth()->user()->pickupRequests()->create([
            'pickup_address' => $request->validated('pickup_address'),
            'pickup_phone' => $request->validated('pickup_phone'),
            'pickup_date' => $request->validated('pickup_date'),
            'pickup_time' => $request->validated('pickup_time'),
            'latitude' => $request->validated('latitude'),
            'longitude' => $request->validated('longitude'),
            'estimated_distance' => $request->validated('estimated_distance'),
            'notes' => $request->validated('notes'),
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

    public function storeWithdraw(StoreWithdrawRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'withdrawal_method' => $validated['withdrawal_method'],
            'bank_name' => $validated['withdrawal_method'] === 'tunai' ? 'Tunai' : $validated['bank_name'],
            'bank_type' => $validated['withdrawal_method'] === 'tunai' ? null : ($validated['bank_type'] ?? null),
            'account_number' => $validated['withdrawal_method'] === 'tunai' ? '-' : $validated['account_number'],
            'account_name' => $validated['withdrawal_method'] === 'tunai' ? $user->name : $validated['account_name'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('nasabah.dashboard')->with('success', 'Pengajuan penarikan berhasil dibuat!');
    }

    public function history(): Response
    {
        $user = auth()->user();

        $deposits = $user->deposits()->orderBy('created_at', 'desc')->get();
        $withdrawals = $user->withdrawals()->orderBy('created_at', 'desc')->get();

        $transactions = $this->mapTransactions($deposits, $withdrawals);

        return Inertia::render('Nasabah/History', [
            'transactions' => $transactions,
        ]);
    }

    public function storeTarget(StoreTargetRequest $request)
    {
        $validated = $request->validated();

        auth()->user()->savingsTargets()->create([
            'title' => $validated['title'],
            'target_amount' => $validated['target_amount'],
            'is_achieved' => (auth()->user()->saldo >= $validated['target_amount']),
        ]);

        return redirect()->back()->with('success', 'Target tabungan berhasil ditambahkan!');
    }

    public function deleteTarget(Request $request, SavingsTarget $target)
    {
        if ($target->user_id !== auth()->id()) {
            abort(403);
        }
        $target->delete();

        return redirect()->back()->with('success', 'Target tabungan berhasil dihapus!');
    }

    /**
     * Map deposits and withdrawals into a unified transaction list.
     * Shared between dashboard() and history() to avoid duplication.
     */
    private function mapTransactions($deposits, $withdrawals, ?int $limit = null): Collection
    {
        $transactions = collect()
            ->concat($deposits->map(fn ($d) => [
                'id' => $d->id,
                'type' => 'deposit',
                'title' => $d->is_donation ? 'Donasi Sampah' : 'Setoran Sampah',
                'amount' => $d->total_price,
                'weight' => $d->weight_total,
                'status' => $d->status,
                'date' => $d->created_at->toISOString(),
            ]))
            ->concat($withdrawals->map(fn ($w) => [
                'id' => $w->id,
                'type' => 'withdrawal',
                'title' => 'Penarikan Saldo',
                'amount' => $w->amount,
                'status' => $w->status,
                'date' => $w->created_at->toISOString(),
            ]))
            ->sortByDesc('date')
            ->values();

        return $limit ? $transactions->take($limit) : $transactions;
    }
}
