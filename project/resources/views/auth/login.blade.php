<x-guest-layout>
    <div style="margin-bottom: 2rem; text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">Selamat Datang</h2>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0; line-height: 1.5;">Silakan masuk kredensial akun Anda untuk melanjutkan ke dasbor.</p>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
        @csrf

        <div>
            <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">Email Perusahaan</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus autocomplete="username"
                style="width: 100%; box-sizing: border-box; padding: 0.85rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.9rem; color: #0f172a; background-color: #ffffff; outline: none; transition: all 0.2s;"
                onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 4px rgba(37, 99, 235, 0.1)';"
                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <div style="display: flex; align-items: center; margin-bottom: 0.4rem;">
                <label for="password" style="font-size: 0.85rem; font-weight: 600; color: #334155;">Kata Sandi</label>
            </div>
            <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password"
                style="width: 100%; box-sizing: border-box; padding: 0.85rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.9rem; color: #0f172a; background-color: #ffffff; outline: none; transition: all 0.2s;"
                onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 4px rgba(37, 99, 235, 0.1)';"
                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <div style="padding-top: 0.25rem;">
            <button type="submit" style="width: 100%; padding: 0.85rem; background-color: #2563eb; color: #ffffff; border: none; border-radius: 0.5rem; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background-color 0.2s; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);"
                onmouseover="this.style.backgroundColor='#1d4ed8';"
                onmouseout="this.style.backgroundColor='#2563eb';">
                Masuk
            </button>
        </div>

        <div style="margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #94a3b8; font-size: 0.75rem; font-weight: 500;">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Secured End-to-End SSL Encryption</span>
        </div>
    </form>
</x-guest-layout>