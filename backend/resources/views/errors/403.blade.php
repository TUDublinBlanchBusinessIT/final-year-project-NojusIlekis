<!doctype html>
<html lang="en">
<head>
    <h1>MY CUSTOM 403</h1>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Access denied</title>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white shadow rounded-2xl p-6">
        <h1 class="text-2xl font-bold">403 — Access denied</h1>
        <p class="mt-2 text-gray-600">
            You don’t have permission to view this page.
        </p>

        <div class="mt-6 flex gap-3">
            <a href="{{ url()->previous() }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                Go back
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Go to dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Login
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
