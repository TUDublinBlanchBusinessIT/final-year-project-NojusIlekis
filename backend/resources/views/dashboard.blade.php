<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    Parent Dashboard
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Welcome, {{ auth()->user()->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Summary Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">
                            Pending Acknowledgements
                        </h3>
                        <p class="text-sm text-slate-600 mt-1">
                            Review and sign any reports waiting for your confirmation.
                        </p>
                    </div>

                    <div class="inline-flex items-center rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800">
                        {{ $acknowledgements->count() }} Pending
                    </div>
                </div>
            </div>

            {{-- Pending Acknowledgements Table --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">
                        Acknowledgement Requests
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">
                                    Type
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">
                                    Record ID
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">
                                    Status
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">
                                    Created
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse($acknowledgements as $ack)
                                <tr class="hover:bg-slate-50 align-top">
                                    <td class="px-5 py-4 text-slate-900">
                                        {{ ucfirst(str_replace('_', ' ', $ack->record_type)) }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $ack->record_id }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ ucfirst($ack->status) }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $ack->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <form method="POST"
                                              action="{{ route('parent.acknowledgements.sign', $ack) }}"
                                              class="space-y-2">
                                            @csrf

                                            <input
                                                type="text"
                                                name="signature_name"
                                                placeholder="Type your full name"
                                                class="w-full rounded-lg border-slate-300 text-sm"
                                                required
                                            >

                                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                                <input type="checkbox"
                                                       name="confirm_acknowledgement"
                                                       value="1"
                                                       class="mt-1"
                                                       required>
                                                <span>I confirm I have read this item</span>
                                            </label>

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                                Acknowledge
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-slate-600">
                                        No pending acknowledgements.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>