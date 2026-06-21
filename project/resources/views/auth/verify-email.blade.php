<x-guest-layout>
    <div class="mb-4 text-sm leading-6 text-slate-600">
        Terima kasih sudah mendaftar. Verifikasi alamat email dengan membuka tautan yang kami kirimkan. Jika belum menerima email, kirim ulang tautan verifikasi.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            Tautan verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Kirim Ulang Email
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
