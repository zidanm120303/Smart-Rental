<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart Rental Pro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 flex flex-col items-center text-center">
                <a href="/" class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-600 text-white shadow-sm shadow-blue-200">
                    <x-application-logo class="h-9 w-9" />
                </a>
                <h1 class="mt-4 text-2xl font-bold text-slate-950">Smart Rental Pro</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk untuk mengelola rental peralatan.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
