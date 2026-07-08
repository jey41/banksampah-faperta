@props(['href' => null, 'type' => 'button', 'class' => '', 'color' => 'primary', 'disabled' => false])
@php
    $colors = [
        'primary'   => 'bg-primary-600 hover:bg-primary-700 text-white',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
        'warning'   => 'bg-amber-500 hover:bg-amber-600 text-white',
        'success'   => 'bg-green-600 hover:bg-green-700 text-white',
        'info'      => 'bg-sky-600 hover:bg-sky-700 text-white',
    ];
    $base = 'inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none shadow-sm';
    $cls = $base . ' ' . ($colors[$color] ?? $colors['primary']) . ' ' . $class;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }} @disabled($disabled)>{{ $slot }}</button>
@endif