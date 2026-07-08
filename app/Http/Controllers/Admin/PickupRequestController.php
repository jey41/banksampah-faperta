<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePickupRequestRequest;
use App\Models\ActivityLog;
use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Http\Request;

class PickupRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PickupRequest::class);

        $query = PickupRequest::with(['user', 'assignedPetugas'])->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $pickups = $query->paginate(15)->withQueryString();

        return view('admin.pickup-requests.index', compact('pickups'));
    }

    public function show(PickupRequest $pickupRequest)
    {
        $this->authorize('view', $pickupRequest);

        $pickupRequest->load(['user', 'assignedPetugas']);
        $petugas = User::whereIn('role', ['super_admin', 'petugas'])->orderBy('name')->get();

        return view('admin.pickup-requests.show', compact('pickupRequest', 'petugas'));
    }

    public function edit(PickupRequest $pickupRequest)
    {
        $this->authorize('update', $pickupRequest);

        $pickupRequest->load(['user', 'assignedPetugas']);
        $petugas = User::whereIn('role', ['super_admin', 'petugas'])->orderBy('name')->get();

        return view('admin.pickup-requests.edit', compact('pickupRequest', 'petugas'));
    }

    public function update(UpdatePickupRequestRequest $request, PickupRequest $pickupRequest)
    {
        $data = $request->validated();

        // Jika petugas ditugaskan tetapi status masih pending, naikkan ke "assigned".
        if (! empty($data['assigned_to']) && $pickupRequest->status === 'pending' && $data['status'] === 'pending') {
            $data['status'] = 'assigned';
        }

        $pickupRequest->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_pickup',
            'description' => auth()->user()->name." memperbarui permintaan jemput #{$pickupRequest->id} menjadi status '{$pickupRequest->status}'.",
        ]);

        return redirect()->route('cms.pickup-requests.show', $pickupRequest)
            ->with('success', 'Permintaan jemput berhasil diperbarui.');
    }
}
