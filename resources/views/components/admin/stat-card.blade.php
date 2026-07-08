@props(['label', 'value', 'description' => null, 'icon' => null, 'color' => 'primary', 'trend' => null])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $value }}</p>
            @if ($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-{{ $color }}-100 text-{{ $color }}-600">
                {!! $icon !!}
            </div>
        @endif
    </div>
    @if ($trend)
        <div class="mt-3 flex items-center gap-1 text-sm font-medium text-{{ $trend['color'] }}-600">
            {!! $trend['icon'] !!}
            <span>{{ $trend['text'] }}</span>
        </div>
    @endif
</div>