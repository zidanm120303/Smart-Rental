<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart Rental Pro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; background-color: #ffffff; height: 100vh; max-height: 100vh; display: flex; overflow: hidden; box-sizing: border-box;">

    <div class="hidden md:flex" style="width: 50%; height: 100%; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 5rem; flex-direction: column; justify-content: center; box-sizing: border-box; position: relative;">

        <div style="position: absolute; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 70%); top: -10%; left: -10%; pointer-events: none;"></div>
        <div style="position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 70%); bottom: -5%; right: -5%; pointer-events: none;"></div>

        <div style="z-index: 10;">
            <div style="color: #93c5fd; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 0.75rem;">Cloud-Based Platform</div>
            <h1 style="color: #ffffff; font-size: 3.5rem; font-weight: 800; line-height: 1.15; margin: 0; letter-spacing: -0.03em;">
                Kelola Rental <br>Jadi Lebih Smart.
            </h1>
            <p style="color: rgba(255, 255, 255, 0.75); font-size: 1.05rem; line-height: 1.6; margin: 1.25rem 0 0 0; max-width: 460px; font-weight: 400;">
                Pantau ketersediaan alat, kelola transaksi cepat, dan pantau performa bisnis persewaan Anda dalam satu dasbor terintegrasi.
            </p>

            <div style="margin-top: 2rem; display: inline-flex; background: rgba(255, 255, 255, 0.07); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 1rem; padding: 0.75rem 1.25rem; align-items: center; gap: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                <div style="width: 0.5rem; height: 0.5rem; background-color: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981;"></div>
                <div style="color: #ffffff; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.02em;">ALL SYSTEMS OPERATIONAL</div>
            </div>
        </div>

        <div style="position: absolute; bottom: 4rem; left: 5rem; z-index: 10; color: rgba(255,255,255,0.4); font-size: 0.8rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
            <span>&copy; 2026 Smart Rental Pro.</span>
            <span style="color: rgba(255,255,255,0.25);">&bull;</span>
            <!-- <span>v2.4.0</span> -->
        </div>
    </div>

    <div class="w-full md:w-[50%]" style="height: 100%; display: flex; align-items: center; justify-content: center; padding: 2rem; box-sizing: border-box; background-color: #ffffff; overflow: hidden;">
        <div style="width: 100%; max-width: 440px; box-sizing: border-box; display: flex; flex-direction: column;">

            <div style="display: flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; background-color: #f1f5f9; border-radius: 0.75rem; margin: 0 auto 2rem auto; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                <x-application-logo style="height: 1.35rem; width: 1.35rem; color: #2563eb;" />
            </div>

            {{ $slot }}

        </div>
    </div>

</body>

</html>
