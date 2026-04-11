<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- PWA / Mobile meta tags -->
    <meta name="application-name" content="{{ config('app.name', 'SnugBug') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'SnugBug') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#4f46e5">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/icons/icon-192x192.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- PWA CSS -->
    <link rel="stylesheet" href="/css/pwa.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900 dark:text-slate-100">
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
    <div class="min-h-screen flex">

        <!-- Left panel (desktop) -->
        <div class="hidden lg:flex w-1/2 relative overflow-hidden">
            <!-- Softer blue panel (less distracting) -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700 to-sky-600 opacity-80"></div>

            <!-- Subtle decorative blobs -->
            <div class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-white/15 blur-2xl"></div>
            <div class="absolute -bottom-24 -right-24 h-64 w-64 rounded-full bg-white/15 blur-2xl"></div>

            <div class="relative z-10 flex flex-col justify-between p-12 text-white">
                <div>
                    <div class="flex items-start gap-3">

    <img src="{{ asset('images/image.png') }}" 
         alt="SnugBug Logo"
         class="h-20 w-20 object-contain">

    <div>
        <div class="text-xl font-semibold">
            {{ config('app.name', 'SnugBug') }}
        </div>

        <p class="text-sm text-white/80 italic font-semibold">
            Snug updates for Bug size humans.
        </p>
    </div>

</div>

                    <h1 class="mt-10 text-4xl font-bold leading-tight">
                        Childcare updates,<br class="hidden xl:block">
                        made simple.
                    </h1>

                    <p class="mt-4 text-white/85 max-w-md">
                        Calm, secure access for Parents, Carers and Managers — without distraction.
                    </p>

                    <div class="mt-10 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-white/90"></div>
                            <p class="text-white/85">Role dashboards + protected routes</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-white/90"></div>
                            <p class="text-white/85">Updates that are easy to read</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-white/90"></div>
                            <p class="text-white/85">Privacy-first approach (GDPR mindful)</p>
                        </div>
                    </div>
                </div>

                <p class="text-white/70 text-sm">
                    © {{ date('Y') }} {{ config('app.name', 'SnugBug') }}. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right panel (form) -->
        <div class="flex w-full lg:w-1/2 items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

    </div>
</div>

        <!-- PWA Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then((registration) => {
                            console.log('[PWA] Service Worker registered:', registration.scope);
                        })
                        .catch((error) => {
                            console.log('[PWA] Service Worker registration failed:', error);
                        });
                });
            }
        </script>
</body>
</html>


