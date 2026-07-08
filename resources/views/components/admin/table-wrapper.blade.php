@props(['label' => ''])

<div class="rounded-lg border border-slate-200 bg-white shadow-sm">
    @if ($label)
        <div class="border-b border-slate-100 px-5 py-3">
            <h4 class="text-sm font-semibold text-slate-700">{{ $label }}</h4>
        </div>
    @endif
    <div class="p-1">
        {{ $slot }}
    </div>
</div>