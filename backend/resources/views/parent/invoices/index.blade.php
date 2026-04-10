<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    My Invoices
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View invoices for your children.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">
                        Invoice List
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Invoice ID</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Child</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Period</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Due Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Total</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse($invoices as $invoice)
                                @php
                                    $status = strtolower($invoice->status);

                                    $badgeClasses = match ($status) {
                                        'draft' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                        'sent' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                        'paid' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                        'overdue' => 'bg-red-100 text-red-700 border border-red-200',
                                        default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    };
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 text-slate-900 font-medium">#{{ $invoice->id }}</td>
                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $invoice->period_start }} to {{ $invoice->period_end }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $invoice->due_date }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClasses }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $invoice->paymentStatusColour() }} ml-1">
                                            {{ $invoice->paymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-900 font-medium">
                                        €{{ number_format($invoice->total, 2) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('parent.invoices.show', $invoice) }}"
                                           class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-6 text-slate-600">
                                        No invoices available.
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