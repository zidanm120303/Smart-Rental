<x-guest-layout>
    <div class="mb-4 text-sm leading-6 text-slate-600">
        Area ini membutuhkan konfirmasi keamanan. Masukkan kata sandi Anda untuk melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Kata Sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
