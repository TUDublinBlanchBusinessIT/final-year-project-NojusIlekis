<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
            Add Invoice Items
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                    Invoice Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="text-slate-800 dark:text-slate-200">
                        <p><span class="font-semibold">Child:</span> {{ $invoice->child->first_name }} {{ $invoice->child->last_name }}</p>
                        <p><span class="font-semibold">Parent:</span> {{ $invoice->parent->name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-slate-800 dark:text-slate-200">
                        <p><span class="font-semibold">Period:</span> {{ $invoice->period_start }} to {{ $invoice->period_end }}</p>
                        <p><span class="font-semibold">Due Date:</span> {{ $invoice->due_date }}</p>
                        <p><span class="font-semibold">Status:</span> {{ ucfirst($invoice->status) }}</p>
                        <p><span class="font-semibold">Total:</span> €{{ number_format($invoice->total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                    Add Line Item
                </h3>

                <form method="POST" action="{{ route('manager.invoices.items.store', $invoice) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
                            Description
                        </label>
                        <input
                            type="text"
                            name="description"
                            id="description"
                            value="{{ old('description') }}"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm text-slate-900"
                            placeholder="e.g. Monthly childcare fee"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="qty" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
                                Quantity
                            </label>
                            <input
                                type="number"
                                name="qty"
                                id="qty"
                                value="{{ old('qty', 1) }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm text-slate-900"
                                min="1"
                            >
                        </div>

                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
                                Unit Price
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                name="unit_price"
                                id="unit_price"
                                value="{{ old('unit_price') }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm text-slate-900"
                                min="0"
                            >
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        >
                            Add Item
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                    Current Line Items
                </h3>

                @if ($invoice->items->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-800 dark:text-slate-200">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-2 font-semibold">Description</th>
                                    <th class="text-left py-2 font-semibold">Qty</th>
                                    <th class="text-left py-2 font-semibold">Unit Price</th>
                                    <th class="text-left py-2 font-semibold">Total</th>
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
                    <p class="text-sm text-slate-600 dark:text-slate-300">No line items added yet.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>