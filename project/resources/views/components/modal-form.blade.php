@props([
    'id',
    'title',
    'description' => null,
    'size' => '2xl',
])

@php
    $sizes = [
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
    ];
@endphp

<dialog id="{{ $id }}" class="w-[calc(100vw-2rem)] {{ $sizes[$size] ?? $sizes['2xl'] }} rounded-2xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/50">
    <div class="border-b border-slate-100 px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-950">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
                @endif
            </div>
            <form method="dialog">
                <button class="rounded-xl p-2 text-slate-500 hover:bg-slate-100" aria-label="Tutup">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="max-h-[75vh] overflow-y-auto p-5">
        {{ $slot }}
    </div>
</dialog>
