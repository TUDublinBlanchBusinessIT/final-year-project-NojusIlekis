<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    Invoice Details
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View your invoice breakdown and totals.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('parent.invoices.print', $invoice) }}"
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    Print Invoice
                </a>

                <a href="{{ route('parent.invoices.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                    Back to Invoices
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $displayStatus = strtolower($invoice->status);

        if ($displayStatus === 'sent' && \Carbon\Carbon::parse($invoice->due_date)->isPast()) {
            $displayStatus = 'overdue';
        }

        $statusBadgeClasses = match ($displayStatus) {
            'draft' => 'bg-slate-100 text-slate-700 border border-slate-200',
            'sent' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'paid' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'overdue' => 'bg-red-100 text-red-700 border border-red-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">
                        Invoice Summary
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2 text-slate-800">
                            <p><span class="font-semibold">Invoice ID:</span> #{{ $invoice->id }}</p>
                            <p><span class="font-semibold">Child:</span> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
                            <p>
                                <span class="font-semibold">Status:</span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusBadgeClasses }}">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </p>
                        </div>

                        <div class="space-y-2 text-slate-800">
                            <p><span class="font-semibold">Period:</span> {{ $invoice->period_start }} to {{ $invoice->period_end }}</p>
                            <p><span class="font-semibold">Due Date:</span> {{ $invoice->due_date }}</p>
                            <p><span class="font-semibold">Subtotal:</span> €{{ number_format($subtotal, 2) }}</p>
                            <p><span class="font-semibold">Discount:</span> €{{ number_format($invoice->discount, 2) }}</p>
                            <p><span class="font-semibold">Final Total:</span> €{{ number_format($finalTotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">
                        Payment History
                    </h3>

                    @if (strtolower($invoice->status) === 'paid')
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-slate-800">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase text-slate-500">Payment Status</p>
                                    <p class="mt-1 font-medium text-emerald-700">Paid</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase text-slate-500">Amount Recorded</p>
                                    <p class="mt-1 font-medium">€{{ number_format($finalTotal, 2) }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase text-slate-500">Recorded On</p>
                                    <p class="mt-1 font-medium">{{ $invoice->updated_at?->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            No payment has been recorded for this invoice yet.
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">
                        Line Items
                    </h3>

                    @if ($invoice->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left py-3 font-semibold text-slate-700">Description</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">Qty</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">Unit Price</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach ($invoice->items as $item)
                                        <tr>
                                            <td class="py-3 text-slate-800">{{ $item->description }}</td>
                                            <td class="py-3 text-slate-800">{{ $item->qty }}</td>
                                            <td class="py-3 text-slate-800">€{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="py-3 text-slate-800 font-medium">€{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-600">No line items added yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>