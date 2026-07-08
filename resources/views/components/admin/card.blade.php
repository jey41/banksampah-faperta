@props(['title' => null, 'icon' => null, 'actions' => null, 'class' => ''])

<div class="rounded-xl border border-slate-200 bg-white shadow-sm {{ $class }}">
    @if ($title || $actions)
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div class="flex items-center gap-3">
                @if ($icon)
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        {!! $icon !!}
                    </span>
                @endif
                <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
            </div>
            @if ($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>