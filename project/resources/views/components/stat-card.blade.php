@props([
    'title',
    'value',
    'trend' => null,
    'icon' => 'activity',
    'tone' => 'blue',
])

@php
    $tones = [
        'blue' => 'bg-blue-100 text-blue-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'violet' => 'bg-violet-100 text-violet-700',
        'slate' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<div class="flex min-h-[8.75rem] rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex w-full items-center gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $tones[$tone] ?? $tones['blue'] }}">
            <i data-lucide="{{ $icon }}" class="h-7 w-7"></i>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-slate-500">{{ $title }}</p>
            <p class="mt-1 text-[1.45rem] font-bold leading-tight tracking-normal text-slate-950 2xl:text-[1.6rem]">{{ $value }}</p>
            @if ($trend)
                <p class="mt-1 text-xs font-medium text-slate-500">{{ $trend }}</p>
            @endif
        </div>
    </div>
</div>
