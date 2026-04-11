<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('parent.incident_details') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $incident->title }}
                </p>
            </div>

            <a href="{{ route('parent.incidents.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                {{ __('parent.back_to_incidents') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg dark:bg-green-900/30 dark:border-green-700 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('parent.incident_summary') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.child') }}:</span>
                            {{ $incident->child->first_name }} {{ $incident->child->last_name }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.title') }}:</span>
                            {{ $incident->title }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.date') }}:</span>
                            {{ \Carbon\Carbon::parse($incident->incident_date)->format('d M Y') }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.time') }}:</span>
                            {{ \Carbon\Carbon::parse($incident->incident_time)->format('H:i') }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.severity') }}:</span>
                            {{ ucfirst($incident->severity) }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.status') }}:</span>
                            {{ ucfirst($incident->status) }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.room') }}:</span>
                            {{ $incident->room?->name ?? $incident->room?->room_name ?? '—' }}
                        </p>

                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-semibold">{{ __('parent.carer') }}:</span>
                            {{ $incident->carer?->name ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('parent.incident_details') }}
                </h3>

                <div class="space-y-5 text-sm">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ __('parent.description') }}</p>
                        <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ $incident->description }}
                        </p>
                    </div>

                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ __('parent.action_taken') }}</p>
                        <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ $incident->action_taken ?: __('parent.no_action_recorded') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('parent.parent_acknowledgement') }}
                </h3>

                @if ($incident->acknowledgement && $incident->acknowledgement->status === 'acknowledged')
                    <div class="rounded-xl border border-emerald-700 bg-emerald-900/30 p-4">
                        <p class="text-green-700 dark:text-green-300">
                            {{ __('parent.incident_acknowledged') }}
                        </p>
                        <p class="text-green-700 dark:text-green-300">
                            {{ __('parent.signed_by') }} {{ $incident->acknowledgement->signature_name }}
                            {{ __('parent.on') }} {{ $incident->acknowledgement->signed_at?->format('d M Y H:i') }}
                        </p>
                    </div>
                @else
                    <div class="mb-4 rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                        <p class="text-sm font-semibold text-slate-100">
                            {{ __('parent.pending_acknowledgement') }}
                        </p>
                        <p class="text-sm text-slate-400 mt-1">
                            {{ __('parent.review_and_sign_incident') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('parent.incidents.sign', $incident) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="signature_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('parent.signature_name') }}
                            </label>
                            <input type="text"
                                   id="signature_name"
                                   name="signature_name"
                                   value="{{ old('signature_name', auth()->user()->name) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                   required>
                            @error('signature_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2 text-slate-300">
                            <input type="checkbox" id="confirm_acknowledgement" required>
                            <label for="confirm_acknowledgement" class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('parent.confirm_incident_read') }}
                            </label>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            {{ __('parent.sign_and_acknowledge') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>