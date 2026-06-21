<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Rental Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-6">
        <section class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white">
                <i data-lucide="camera" class="h-7 w-7"></i>
            </div>
            <h1 class="mt-4 text-2xl font-bold">Smart Rental Pro</h1>
            <p class="mt-2 text-sm text-slate-500">Sistem manajemen rental peralatan.</p>
            <div class="mt-5 flex justify-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="sr-button-primary">Buka Dasbor</a>
                @else
                    <a href="{{ route('login') }}" class="sr-button-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="sr-button-secondary">Daftar</a>
                @endauth
            </div>
        </section>
    </main>
</body>
</html>
