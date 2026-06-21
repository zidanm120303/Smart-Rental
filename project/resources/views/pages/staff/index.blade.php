@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Staf</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola pengguna, peran, dan status tim operasional.</p>
        </div>
        <button type="button" class="sr-button-primary" onclick="document.getElementById('create-staff-modal').showModal()"><i data-lucide="user-plus" class="h-4 w-4"></i> Tambah Staf</button>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($roles as $role)
            <div class="sr-card p-5">
                <p class="text-sm font-semibold text-slate-500">{{ $role->display_name }}</p>
                <p class="mt-2 text-3xl font-bold text-slate-950">{{ $role->users_count }}</p>
            </div>
        @endforeach
    </section>

    <section class="sr-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sr-table min-w-[68rem]">
                <thead class="bg-slate-50"><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Peran</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach ($staff as $user)
                        <tr>
                            <td class="font-bold text-slate-950">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>{{ $user->roles->pluck('display_name')->join(', ') }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ optional($user->last_login_at)->translatedFormat('d M Y H:i') ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="document.getElementById('edit-staff-{{ $user->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit staf"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                    @unless ($user->is(auth()->user()))
                                        <form method="POST" action="{{ route('staff.destroy', $user) }}" onsubmit="return confirm('Hapus staf {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus staf"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<x-modal-form id="create-staff-modal" title="Tambah Staf" description="Buat pengguna operasional baru." size="2xl">
    @include('pages.staff._form', ['action' => route('staff.store'), 'method' => 'POST', 'roles' => $roles])
</x-modal-form>

@foreach ($staff as $userItem)
    <x-modal-form id="edit-staff-{{ $userItem->id }}" title="Edit Staf" description="{{ $userItem->name }} - {{ $userItem->email }}" size="2xl">
        @include('pages.staff._form', ['userItem' => $userItem, 'action' => route('staff.update', $userItem), 'method' => 'PUT', 'roles' => $roles])
    </x-modal-form>
@endforeach
@endsection
