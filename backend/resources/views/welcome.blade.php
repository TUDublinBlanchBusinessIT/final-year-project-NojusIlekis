<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SnugBug | Childcare Updates</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-r from-blue-700 to-blue-500 min-h-screen flex items-center">

<div class="container mx-auto px-24 grid md:grid-cols-2 gap-12 items-center">

    <!-- LEFT SIDE -->
    <div class="text-white space-y-6">
        <div class="flex items-start space-x-3">
    
    <img src="{{ asset('images/image.png') }}" 
         alt="SnugBug Logo"
         class="w-20 h-20 object-contain">

    <div>
        <h1 class="text-2xl font-semibold">SnugBug</h1>
        <p class="text-sm text-blue-100 italic font-semibold">
            Snug updates for Bug size humans.
        </p>
    </div>

</div>

        <h2 class="text-5xl font-bold leading-tight">
            Childcare updates,<br> made simple.
        </h2>

        <p class="text-lg text-blue-100 max-w-lg">
            Calm, secure access for Parents, Carers and Managers —
            keeping everyone connected throughout the day.
        </p>

        <ul class="space-y-2 text-blue-100">
            <li>• Role-based dashboards</li>
            <li>• Live daily updates</li>
            <li>• Attendance tracking</li>
            <li>• Secure & privacy-focused</li>
        </ul>

        <div class="pt-6 flex space-x-4">
            <a href="{{ route('login') }}"
               class="bg-white text-blue-700 px-6 py-3 rounded-xl font-semibold shadow hover:shadow-lg transition">
                Log In
            </a>

            <a href="{{ route('register') }}"
               class="bg-blue-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-800 transition">
                Create Account
            </a>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="hidden md:flex justify-center">
        <div class="bg-white/10 backdrop-blur-lg p-10 rounded-3xl shadow-xl w-full max-w-md text-white">
            <h3 class="text-2xl font-semibold mb-4">Why SnugBug?</h3>

            <div class="space-y-4">
                <div>
                    <h4 class="font-semibold">For Parents</h4>
                    <p class="text-sm text-blue-100">
                        Real-time updates, photos and reports.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold">For Carers</h4>
                    <p class="text-sm text-blue-100">
                        Simple tools to log daily progress.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold">For Managers</h4>
                    <p class="text-sm text-blue-100">
                        Clear oversight and attendance tracking.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>