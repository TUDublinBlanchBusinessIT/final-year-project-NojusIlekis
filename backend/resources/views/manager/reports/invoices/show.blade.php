<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    Invoice Details
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View invoice breakdown, discount, and final total.
                </p>
            </div>

            <a href="{{ route('manager.invoices.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200
                      hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                Back to Invoices
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                            dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        Invoice Summary
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2 text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">Invoice ID:</span> #{{ $invoice->id }}</p>
                            <p><span class="font-semibold">Child:</span> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
                            <p><span class="font-semibold">Parent:</span> {{ $invoice->parent->name ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Status:</span> {{ ucfirst($invoice->status) }}</p>
                        </div>

                        <div class="space-y-2 text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">Period:</span> {{ $invoice->period_start }} to {{ $invoice->period_end }}</p>
                            <p><span class="font-semibold">Due Date:</span> {{ $invoice->due_date }}</p>
                            <p><span class="font-semibold">Subtotal:</span> €{{ number_format($subtotal, 2) }}</p>
                            <p><span class="font-semibold">Discount:</span> €{{ number_format($invoice->discount, 2) }}</p>
                            <p><span class="font-semibold">Final Total:</span> €{{ number_format($finalTotal, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($invoice->status === 'draft')
                            <form method="POST" action="{{ route('manager.invoices.status.update', $invoice) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="sent">

                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                           bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                           shadow-sm shadow-blue-500/20
                                           hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                           focus:outline-none focus:ring-4 focus:ring-blue-200
                                           active:translate-y-[1px]
                                           dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                                    Mark as Sent
                                </button>
                            </form>
                        @endif

                        @if($invoice->status === 'sent')
                            <form method="POST" action="{{ route('manager.invoices.status.update', $invoice) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="paid">

                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                           bg-emerald-600 hover:bg-emerald-700
                                           focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                    Mark as Paid
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        Line Items
                    </h3>

                    @if ($invoice->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">Description</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">Qty</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">Unit Price</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach ($invoice->items as $item)
                                        <tr>
                                            <td class="py-3 text-slate-800 dark:text-slate-200">
                                                {{ $item->description }}
                                            </td>
                                            <td class="py-3 text-slate-800 dark:text-slate-200">
                                                {{ $item->qty }}
                                            </td>
                                            <td class="py-3 text-slate-800 dark:text-slate-200">
                                                €{{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="py-3 text-slate-800 dark:text-slate-200 font-medium">
                                                €{{ number_format($item->total, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            No line items added yet.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>