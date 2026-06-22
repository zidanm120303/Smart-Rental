<x-guest-layout>
    <div style="margin-bottom: 2rem; text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">Minta Tautan</h2>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0; line-height: 1.5;">
            Masukkan email akun Anda. Kami akan mengirim tautan atur ulang kata sandi agar Anda bisa membuat kata sandi baru.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
        @csrf

        <div>
            <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email akun" required autofocus
                style="width: 100%; box-sizing: border-box; padding: 0.85rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.9rem; color: #0f172a; background-color: #ffffff; outline: none; transition: all 0.2s;"
                onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 4px rgba(37, 99, 235, 0.1)';"
                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div style="padding-top: 0.5rem;">
            <button type="submit" style="width: 100%; padding: 0.85rem; background-color: #0f172a; color: #ffffff; border: none; border-radius: 0.5rem; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#1e293b';"
                onmouseout="this.style.backgroundColor='#0f172a';">
                Kirim Tautan Atur Ulang
            </button>
        </div>

        <div style="border-top: 1px solid #f1f5f9; margin-top: 1rem; padding-top: 1.25rem; text-align: center;">
            <a href="{{ route('login') }}" style="color: #2563eb; text-decoration: none; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'">
                <span>←</span> Kembali ke Halaman Login
            </a>
        </div>
    </form>
</x-guest-layout>