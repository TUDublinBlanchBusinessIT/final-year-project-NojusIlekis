<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4f46e5">

        <!-- PWA CSS -->
        <link rel="stylesheet" href="/css/pwa.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Chatbot Widget --}}
        @auth
            <x-chatbot-widget />
        @endauth

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
