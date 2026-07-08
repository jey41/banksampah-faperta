@props(['label', 'labelClass' => ''])

<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @if ($label)
        <h4 class="{{ $labelClass ?: 'mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500' }}">{{ $label }}</h4>
    @endif
    {{ $slot }}
</div>