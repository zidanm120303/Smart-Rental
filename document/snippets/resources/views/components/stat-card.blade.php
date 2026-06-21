@props(['title', 'value', 'trend' => null])
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $value }}</p>
    @if ($trend)<p class="mt-1 text-xs text-slate-500">{{ $trend }}</p>@endif
</div>
