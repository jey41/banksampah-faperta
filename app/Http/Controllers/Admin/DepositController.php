<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveDepositRequest;
use App\Models\Deposit;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    public function __construct(private TransactionService $tx) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Deposit::class);

        $query = Deposit::with(['user', 'validator'])->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($category = $request->get('donation_category')) {
            $query->where('donation_category', $category);
        }
        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $deposits = $query->paginate(15)->withQueryString();

        return view('admin.deposits.index', compact('deposits'));
    }

    public function show(Deposit $deposit)
    {
        $this->authorize('view', $deposit);

        $deposit->load(['user', 'validator', 'items.trashPrice']);

        return view('admin.deposits.show', compact('deposit'));
    }

    public function approve(ApproveDepositRequest $request, Deposit $deposit)
    {
        try {
            $this->tx->approveDeposit($deposit, $request->validated()['items'], auth()->id());

            return redirect()->route('cms.deposits.show', $deposit)
                ->with('success', 'Setoran berhasil ditimbang, disetujui, dan saldo ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Failed to approve deposit', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = str_contains($e->getMessage(), 'sudah tidak berstatus pending')
                ? $e->getMessage()
                : 'Gagal memproses setoran. Silakan coba lagi atau hubungi administrator.';

            return back()->with('error', $message);
        }
    }

    public function reject(Deposit $deposit)
    {
        $this->authorize('reject', $deposit);

        try {
            $this->tx->rejectDeposit($deposit, auth()->id());

            return redirect()->route('cms.deposits.show', $deposit)
                ->with('success', 'Permohonan setoran sampah telah ditolak.');
        } catch (\Throwable $e) {
            Log::error('Failed to reject deposit', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal menolak setoran. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
