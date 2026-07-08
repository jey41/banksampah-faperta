<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function __construct(private TransactionService $tx)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Withdrawal::class);

        $query = Withdrawal::with(['user', 'validator'])->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($method = $request->get('withdrawal_method')) {
            $query->where('withdrawal_method', $method);
        }
        if ($search = $request->get('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $withdrawals = $query->paginate(15)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $this->authorize('view', $withdrawal);

        $withdrawal->load(['user', 'validator', 'history.processor']);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        $this->authorize('approve', $withdrawal);

        try {
            $this->tx->approveWithdrawal($withdrawal, auth()->id());

            return redirect()->route('cms.withdrawals.show', $withdrawal)
                ->with('success', 'Penarikan disetujui dan saldo nasabah telah dipotong.');
        } catch (\Throwable $e) {
            Log::error('Failed to approve withdrawal', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = str_contains($e->getMessage(), 'sudah tidak berstatus pending') || str_contains($e->getMessage(), 'tidak mencukupi')
                ? $e->getMessage()
                : 'Gagal menyetujui penarikan. Silakan coba lagi atau hubungi administrator.';

            return back()->with('error', $message);
        }
    }

    public function reject(Withdrawal $withdrawal)
    {
        $this->authorize('reject', $withdrawal);

        try {
            $this->tx->rejectWithdrawal($withdrawal, auth()->id());

            return redirect()->route('cms.withdrawals.show', $withdrawal)
                ->with('success', 'Permohonan penarikan telah ditolak.');
        } catch (\Throwable $e) {
            Log::error('Failed to reject withdrawal', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal menolak penarikan. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
