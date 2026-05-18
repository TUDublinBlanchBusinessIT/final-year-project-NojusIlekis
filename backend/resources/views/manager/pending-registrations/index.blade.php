<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
            {{ __('manager.pending_registrations') }}
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
            {{ __('manager.pending_registrations_desc') }}
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($pendingParents->isEmpty() && $pendingCarers->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-8 text-center
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <p class="text-slate-600 dark:text-slate-300">{{ __('manager.no_pending_registrations') }}</p>
                </div>
            @endif

            {{-- Pending Parents --}}
            @if ($pendingParents->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                            👨‍👩‍👧 {{ __('manager.pending_parents') }}
                            <span class="ml-2 inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white text-xs font-bold rounded-full">
                                {{ $pendingParents->count() }}
                            </span>
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($pendingParents as $parent)
                            @php($child = $parent->children->first())
                            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $parent->name }}
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $parent->email }} · {{ $parent->phone }}
                                    </p>
                                    @if ($child)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ __('manager.child') }}: <strong>{{ $child->first_name }} {{ $child->last_name }}</strong>
                                            ({{ \Carbon\Carbon::parse($child->dob)->age }} {{ __('manager.years_old') }})
                                        </p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ __('manager.submitted') }} {{ $parent->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @include('manager.pending-registrations._actions', ['user' => $parent])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pending Carers --}}
            @if ($pendingCarers->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                            🧑‍🏫 {{ __('manager.pending_carers') }}
                            <span class="ml-2 inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white text-xs font-bold rounded-full">
                                {{ $pendingCarers->count() }}
                            </span>
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($pendingCarers as $carer)
                            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $carer->name }}
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $carer->email }} · {{ $carer->phone }}
                                    </p>
                                    @if ($carer->registration_notes)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit($carer->registration_notes, 140) }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ __('manager.submitted') }} {{ $carer->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @include('manager.pending-registrations._actions', ['user' => $carer])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
