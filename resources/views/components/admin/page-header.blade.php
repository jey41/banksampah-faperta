@props(['title', 'subtitle' => null, 'actions' => null])
@section('title', $title)

@section('breadcrumbs')
    @isset($subtitle)
        <svg class="mx-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600">{{ $subtitle }}</span>
    @endisset
@stop

@php
    // $actions dapat berupa: string HTML, array of string HTML, atau slot.
    $actionsHtml = is_array($actions) ? implode(' ', array_filter($actions)) : ($actions ?? '');
@endphp

<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ $title }}</h2>
        @isset($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endisset
    </div>
    <div class="flex items-center gap-2">
        {!! $actionsHtml !!}
    </div>
</div>