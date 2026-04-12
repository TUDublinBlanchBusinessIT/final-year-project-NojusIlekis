<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
            {{ __('manager.add_invoice_items') }}
        </h2>
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
                        {{ __('manager.invoice_details') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">{{ __('manager.child') }}:</span> {{ $invoice->child->first_name }} {{ $invoice->child->last_name }}</p>
                            <p><span class="font-semibold">{{ __('manager.parent') }}:</span> {{ $invoice->parent->name ?? __('manager.not_available') }}</p>
                        </div>

                        <div class="text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">{{ __('manager.period') }}:</span> {{ $invoice->period_start }} {{ __('manager.to') }} {{ $invoice->period_end }}</p>
                            <p><span class="font-semibold">{{ __('manager.due_date') }}:</span> {{ $invoice->due_date }}</p>
                            <p><span class="font-semibold">{{ __('manager.status') }}:</span> {{ ucfirst($invoice->status) }}</p>
                            <p><span class="font-semibold">{{ __('manager.subtotal') }}:</span> €{{ number_format($subtotal, 2) }}</p>
                            <p><span class="font-semibold">{{ __('manager.discount') }}:</span> €{{ number_format($invoice->discount, 2) }}</p>
                            <p><span class="font-semibold">{{ __('manager.final_total') }}:</span> €{{ number_format($finalTotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('manager.add_line_item') }}
                    </h3>

                    <form method="POST" action="{{ route('manager.invoices.items.store', $invoice) }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                {{ __('manager.description') }}
                            </label>
                            <input type="text" name="description" id="description" value="{{ old('description') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                   placeholder="{{ __('manager.invoice_item_description_placeholder') }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="qty" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.quantity') }}
                                </label>
                                <input type="number" name="qty" id="qty" value="{{ old('qty', 1) }}"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                       min="1">
                            </div>

                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.unit_price') }}
                                </label>
                                <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                       min="0">
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200">
                            {{ __('manager.add_item') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('manager.apply_discount') }}
                    </h3>

                    <form method="POST" action="{{ route('manager.invoices.discount.update', $invoice) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="max-w-sm">
                            <label for="discount" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                {{ __('manager.discount_amount') }}
                            </label>
                            <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', $invoice->discount) }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                   min="0">
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200">
                            {{ __('manager.update_discount') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('manager.current_line_items') }}
                    </h3>

                    @if ($invoice->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-slate-800 dark:text-slate-200">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-2 font-semibold">{{ __('manager.description') }}</th>
                                        <th class="text-left py-2 font-semibold">{{ __('manager.qty') }}</th>
                                        <th class="text-left py-2 font-semibold">{{ __('manager.unit_price') }}</th>
                                        <th class="text-left py-2 font-semibold">{{ __('manager.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $item)
                                        <tr class="border-b border-slate-100 dark:border-slate-700">
                                            <td class="py-2">{{ $item->description }}</td>
                                            <td class="py-2">{{ $item->qty }}</td>
                                            <td class="py-2">€{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="py-2">€{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.no_line_items') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>