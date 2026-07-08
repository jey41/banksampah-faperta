@props(['value', 'type' => 'status'])
@php
    $statuses = [
        'super_admin' => 'bg-purple-100 text-purple-700',
        'approved'  => 'bg-green-100 text-green-700',
        'pending'   => 'bg-amber-100 text-amber-700',
        'rejected'  => 'bg-red-100 text-red-700',
        'assigned'  => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-slate-100 text-slate-600',
        'draft'     => 'bg-slate-100 text-slate-600',
        'published' => 'bg-green-100 text-green-700',
        'verified'  => 'bg-green-100 text-green-700',
    ];
    $labels = [
        'approved'   => 'Disetujui',   'pending'    => 'Menunggu',
        'rejected'   => 'Ditolak',      'assigned'   => 'Ditugaskan',
        'completed'  => 'Selesai',      'cancelled'  => 'Dibatalkan',
        'draft'      => 'Draft',        'published'  => 'Terbit',
        'verified'   => 'Terverifikasi','super_admin'=> 'Super Admin',
        'petugas'    => 'Petugas Bank Sampah', 'nasabah' => 'Nasabah',
    ];
    $class = $statuses[$value] ?? 'bg-slate-100 text-slate-600';
    $label = $labels[$value] ?? $value;
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $class }}">
    {{ $label }}
</span>