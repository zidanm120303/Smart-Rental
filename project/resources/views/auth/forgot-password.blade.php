<x-guest-layout>
    <div class="mb-4 text-sm leading-6 text-slate-600">
        Masukkan email akun Anda. Kami akan mengirim tautan atur ulang kata sandi agar Anda bisa membuat kata sandi baru.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" placeholder="Masukkan email akun" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Kirim Tautan Atur Ulang Kata Sandi
        </x-primary-button>
    </form>
</x-guest-layout>
