<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center text-3xl mb-4">
            ⏳
        </div>

        <h1 class="text-2xl font-semibold text-white mb-2">
            {{ __('auth.pending_title') }}
        </h1>

        <p class="text-gray-200 mb-4">
            {{ __('auth.pending_message') }}
        </p>

        <p class="text-sm text-gray-300 mb-6">
            {{ __('auth.pending_timeline') }}
        </p>

        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 font-semibold text-white
                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                  shadow-sm shadow-blue-500/20
                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                  focus:outline-none focus:ring-4 focus:ring-blue-200">
            {{ __('auth.pending_back_to_login') }}
        </a>
    </div>
</x-guest-layout>
