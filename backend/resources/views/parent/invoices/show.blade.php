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

            <a href="{{ route('parent.invoices.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                Back to Invoices
            </a>
        </div>
    </x-slot>

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
                            <p><span class="font-semibold">Status:</span> {{ ucfirst($invoice->status) }}</p>
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