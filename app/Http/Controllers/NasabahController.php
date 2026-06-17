<?php

namespace App\Http\Controllers;

use App\Models\TrashPrice;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\Withdrawal;
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
                'title' => 'Setor Sampah',
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

        return Inertia::render('Nasabah/Dashboard', [
            'transactions' => $transactions,
            'totalDeposited' => (int)$totalDeposited,
            'totalWithdrawn' => (int)$totalWithdrawn,
            'targets' => $targets,
        ]);
    }

    public function deposit(): Response
    {
        $prices = TrashPrice::orderBy('category')->orderBy('name')->get();

        return Inertia::render('Nasabah/Deposit', [
            'prices' => $prices,
        ]);
    }

    public function storeDeposit(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.trash_price_id' => 'required|exists:trash_prices,id',
            'items.*.weight' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string|max:1000',
            'is_donation' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $totalPrice = 0;
            $weightTotal = 0;
            $itemsToCreate = [];

            foreach ($request->items as $itemData) {
                $trashPrice = TrashPrice::findOrFail($itemData['trash_price_id']);
                $itemWeight = $itemData['weight'];
                $itemPrice = $itemWeight * $trashPrice->price_buy;

                $weightTotal += $itemWeight;
                $totalPrice += $itemPrice;

                $itemsToCreate[] = [
                    'trash_price_id' => $trashPrice->id,
                    'weight' => $itemWeight,
                    'price_per_unit' => $trashPrice->price_buy,
                    'total_price' => $itemPrice,
                    'total_carbon' => $itemWeight * $trashPrice->carbon_factor,
                ];
            }

            $deposit = Deposit::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'weight_total' => $weightTotal,
                'status' => 'pending',
                'is_donation' => $request->boolean('is_donation', false),
                'notes' => $request->notes,
            ]);

            foreach ($itemsToCreate as $item) {
                $deposit->items()->create($item);
            }
        });

        return redirect()->route('nasabah.dashboard')->with('success', 'Pengajuan setoran berhasil dibuat!');
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
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
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
                'title' => 'Setor Sampah',
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
