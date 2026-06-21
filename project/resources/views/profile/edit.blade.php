<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Profil Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi akun, email, dan password Anda.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="sr-card p-5">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="sr-card p-5">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="sr-card p-5">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
