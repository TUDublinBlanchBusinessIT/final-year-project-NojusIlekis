<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('auth.app_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-r from-blue-700 to-blue-500 min-h-screen flex items-center">

<div class="container mx-auto px-24 grid md:grid-cols-2 gap-12 items-center">

    <!-- LEFT SIDE -->
    <div class="text-white space-y-6">
        <div class="flex items-start space-x-3">
    
    <img src="{{ asset('images/image.png') }}" 
         alt="{{ __('auth.logo_alt') }}"
         class="w-20 h-20 object-contain">

    <div>
        <h1 class="text-2xl font-semibold">{{ __('auth.app_name') }}</h1>
        <p class="text-sm text-blue-100 italic font-semibold">
            {{ __('auth.app_tagline') }}
        </p>
    </div>

</div>

        <h2 class="text-5xl font-bold leading-tight">
            {!! nl2br(e(__('auth.hero_title'))) !!}
        </h2>

        <p class="text-lg text-blue-100 max-w-lg">
            {{ __('auth.hero_subtitle') }}
        </p>

        <ul class="space-y-2 text-blue-100">
            <li>• {{ __('auth.feature_1') }}</li>
            <li>• {{ __('auth.feature_2') }}</li>
            <li>• {{ __('auth.feature_3') }}</li>
            <li>• {{ __('auth.feature_4') }}</li>
        </ul>

        <div class="pt-6 flex space-x-4">
            <a href="{{ route('login') }}"
               class="bg-white text-blue-700 px-6 py-3 rounded-xl font-semibold shadow hover:shadow-lg transition">
                {{ __('auth.log_in') }}
            </a>

            <a href="{{ route('register') }}"
               class="bg-blue-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-800 transition">
                {{ __('auth.create_account') }}
            </a>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="hidden md:flex justify-center">
        <div class="bg-white/10 backdrop-blur-lg p-10 rounded-3xl shadow-xl w-full max-w-md text-white">
            <h3 class="text-2xl font-semibold mb-4">{{ __('auth.why_snugbug') }}</h3>

            <div class="space-y-4">
                <div>
                    <h4 class="font-semibold">{{ __('auth.for_parents') }}</h4>
                    <p class="text-sm text-blue-100">
                        {{ __('auth.for_parents_desc') }}
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold">{{ __('auth.for_carers') }}</h4>
                    <p class="text-sm text-blue-100">
                        {{ __('auth.for_carers_desc') }}
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold">{{ __('auth.for_managers') }}</h4>
                    <p class="text-sm text-blue-100">
                        {{ __('auth.for_managers_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>