<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ __('parent.invoice_details') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('parent.invoice_breakdown_description') }}
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
                    {{ __('parent.print_invoice') }}
                </a>

                <a href="{{ route('parent.invoices.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                    {{ __('parent.back_to_invoices') }}
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
                        {{ __('parent.invoice_summary') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2 text-slate-800">
                            <p><span class="font-semibold">{{ __('parent.invoice_id') }}:</span> #{{ $invoice->id }}</p>
                            <p><span class="font-semibold">{{ __('parent.child') }}:</span> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
                            <p>
                                <span class="font-semibold">{{ __('parent.status') }}:</span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusBadgeClasses }}">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </p>
                        </div>

                        <div class="space-y-2 text-slate-800">
                            <p><span class="font-semibold">{{ __('parent.period') }}:</span> {{ $invoice->period_start }} {{ __('parent.to') }} {{ $invoice->period_end }}</p>
                            <p><span class="font-semibold">{{ __('parent.due_date') }}:</span> {{ $invoice->due_date }}</p>
                            <p><span class="font-semibold">{{ __('parent.subtotal') }}:</span> €{{ number_format($subtotal, 2) }}</p>
                            <p><span class="font-semibold">{{ __('parent.discount') }}:</span> €{{ number_format($invoice->discount, 2) }}</p>
                            <p><span class="font-semibold">{{ __('parent.final_total') }}:</span> €{{ number_format($finalTotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('parent.payment') }}</h3>

                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $invoice->paymentStatusColour() }}">
                        {{ $invoice->paymentStatusLabel() }}
                    </span>
                    @if($invoice->payment_submitted_at)
                        <span class="text-sm text-gray-500 ml-2">{{ __('parent.submitted') }} {{ $invoice->payment_submitted_at->format('d M Y, H:i') }}</span>
                    @endif
                    @if($invoice->payment_approved_at)
                        <span class="text-sm text-gray-500 ml-2">{{ __('parent.approved') }} {{ $invoice->payment_approved_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>

                @if($invoice->payment_status === 'rejected')
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
                        <p class="font-bold">{{ __('parent.payment_rejected') }}</p>
                        <p class="text-sm mt-1">{{ __('parent.reason') }} {{ $invoice->rejection_reason }}</p>
                        <p class="text-sm mt-1">{{ __('parent.resubmit_payment_message') }}</p>
                    </div>
                @endif

                @if($invoice->canSubmitPayment())
                    <form method="POST" action="{{ route('parent.invoices.pay', $invoice) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('parent.upload_payment_proof') }} <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">{{ __('parent.payment_proof_help') }}</p>
                            <input type="file" name="payment_proof" id="payment_proof" required accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full text-sm border border-slate-200 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('payment_proof')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('parent.payment_reference_notes') }}
                            </label>
                            <input type="text" name="payment_notes" id="payment_notes"
                                   placeholder="{{ __('parent.payment_reference_placeholder') }}"
                                   value="{{ old('payment_notes') }}"
                                   class="w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition">
                            {{ __('parent.submit_payment') }}
                        </button>
                    </form>
                @endif

                @if($invoice->payment_status === 'payment_submitted')
                    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded-lg">
                        <p class="font-bold">{{ __('parent.payment_under_review') }}</p>
                        <p class="text-sm mt-1">{{ __('parent.payment_waiting_approval') }}</p>
                        @if($invoice->payment_notes)
                            <p class="text-sm mt-1">{{ __('parent.your_note') }} "{{ $invoice->payment_notes }}"</p>
                        @endif
                    </div>
                @endif

                @if($invoice->payment_status === 'approved')
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                        <p class="font-bold">{{ __('parent.payment_confirmed') }}</p>
                        <p class="text-sm mt-1">{{ __('parent.payment_verified_approved') }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">
                        {{ __('parent.line_items') }}
                    </h3>

                    @if ($invoice->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left py-3 font-semibold text-slate-700">{{ __('parent.description') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">{{ __('parent.qty') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">{{ __('parent.unit_price') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700">{{ __('parent.total') }}</th>
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
                        <p class="text-sm text-slate-600">{{ __('parent.no_line_items') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>