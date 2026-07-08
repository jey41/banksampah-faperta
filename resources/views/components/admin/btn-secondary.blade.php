@props(['href', 'class' => ''])

<a href="{{ $href }}" {{ $attributes->merge(['class' =>
    'inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors ' . $class]) }}>
    {{ $slot }}
</a>