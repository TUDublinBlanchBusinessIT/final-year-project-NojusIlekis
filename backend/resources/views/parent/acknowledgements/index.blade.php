<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    Pending Acknowledgements
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View items waiting for your acknowledgement.
                </p>
            </div>

            <a href="{{ route('parent.dashboard') }}"
               class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Acknowledgements</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Type</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Record ID</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Created</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($acknowledgements as $ack)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 text-slate-900 dark:text-slate-100">
                                        {{ ucfirst(str_replace('_', ' ', $ack->record_type)) }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $ack->record_id }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                     bg-amber-50 text-amber-700 border border-amber-200
                                                     dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-900/60">
                                            {{ ucfirst($ack->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $ack->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-slate-600 dark:text-slate-300">
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