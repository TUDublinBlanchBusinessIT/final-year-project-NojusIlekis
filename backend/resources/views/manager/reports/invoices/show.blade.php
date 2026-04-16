<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.invoice_details') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.invoice_details_desc') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('manager.invoices.print', $invoice) }}"
                   target="_blank"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-700 text-white border border-slate-700
                          hover:bg-slate-800">
                    {{ __('manager.print_invoice') }}
                </a>

                <a href="{{ route('manager.invoices.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200
                          hover:bg-slate-100
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                    {{ __('manager.back_to_invoices') }}
                </a>
            </div>
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
                        {{ __('manager.invoice_summary') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2 text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">{{ __('manager.invoice_id') }}:</span> #{{ $invoice->id }}</p>
                            <p><span class="font-semibold">{{ __('manager.child') }}:</span> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
                            <p><span class="font-semibold">{{ __('manager.parent') }}:</span> {{ $invoice->parent->name ?? __('manager.not_available') }}</p>
                            <p><span class="font-semibold">{{ __('manager.status') }}:</span> {{ ucfirst($invoice->status) }}</p>
                        </div>

                        <div class="space-y-2 text-slate-800 dark:text-slate-200">
                            <p><span class="font-semibold">{{ __('manager.period') }}:</span> {{ $invoice->period_start }} {{ __('manager.to') }} {{ $invoice->period_end }}</p>
                            <p><span class="font-semibold">{{ __('manager.due_date') }}:</span> {{ $invoice->due_date }}</p>
                            <p><span class="font-semibold">{{ __('manager.subtotal') }}:</span> €{{ number_format($subtotal, 2) }}</p>
                            <p><span class="font-semibold">{{ __('manager.discount') }}:</span> €{{ number_format($invoice->discount, 2) }}</p>
                            <p><span class="font-semibold">{{ __('manager.final_total') }}:</span> €{{ number_format($finalTotal, 2) }}</p>
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
                                    {{ __('manager.mark_as_sent') }}
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
                                    {{ __('manager.mark_as_paid') }}
                                </button>
                            </form>
                        @endif

                        @if($invoice->canBeEdited())
                            <a href="{{ route('manager.invoices.items.create', $invoice) }}"
                               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                ✏️ {{ __('manager.edit_invoice') }}
                            </a>
                        @endif

                        @if($invoice->canBeCancelled())
                            <form method="POST" action="{{ route('manager.invoices.cancel', $invoice) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('{{ __('manager.cancel_invoice_confirm') }}')"
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                    🚫 {{ __('manager.cancel_invoice') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment Review Section (Manager) --}}
            @if($invoice->isPaymentPending())
                <div class="bg-amber-50 rounded-2xl border border-amber-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-amber-800 mb-4">⏳ {{ __('manager.payment_awaiting_review') }}</h3>

                    <div class="space-y-3 mb-4">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ __('manager.submitted') }}:</span> {{ $invoice->payment_submitted_at->format('d M Y, H:i') }}
                        </p>
                        @if($invoice->payment_notes)
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">{{ __('manager.parent_note') }}:</span> "{{ $invoice->payment_notes }}"
                            </p>
                        @endif
                    </div>

                    {{-- Payment proof viewer --}}
                    @if($invoice->payment_proof_path)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('manager.payment_proof') }}:</p>
                            @if(\Illuminate\Support\Str::endsWith($invoice->payment_proof_path, '.pdf'))
                                <a href="{{ Storage::url($invoice->payment_proof_path) }}" target="_blank"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 underline text-sm">
                                    📄 {{ __('manager.view_pdf_receipt') }}
                                </a>
                            @else
                                <a href="{{ Storage::url($invoice->payment_proof_path) }}" target="_blank">
                                    <img src="{{ Storage::url($invoice->payment_proof_path) }}"
                                         alt="Payment proof"
                                         class="max-w-md rounded-lg border border-slate-200 shadow-sm cursor-pointer hover:opacity-90">
                                </a>
                                <p class="text-xs text-gray-400 mt-1">{{ __('manager.click_full_size') }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Approve / Reject buttons --}}
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('manager.invoices.approve-payment', $invoice) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('{{ __('manager.approve_payment_confirm') }}')"
                                    class="px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                                ✅ {{ __('manager.approve_payment') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('manager.invoices.reject-payment', $invoice) }}"
                              x-data="{ showReason: false }">
                            @csrf
                            <button type="button" @click="showReason = !showReason"
                                    class="px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                                ❌ {{ __('manager.reject_payment') }}
                            </button>
                            <div x-show="showReason" x-cloak class="mt-3">
                                <textarea name="rejection_reason" rows="2" required
                                          placeholder="{{ __('manager.rejection_reason_placeholder') }}"
                                          class="w-full text-sm rounded-lg border-slate-200 focus:border-red-500 focus:ring-red-500"></textarea>
                                <button type="submit" class="mt-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                                    {{ __('manager.confirm_rejection') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Payment status display for non-pending invoices --}}
            @if($invoice->payment_status === 'approved')
                <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
                    <h3 class="text-lg font-semibold text-green-800">✅ {{ __('manager.payment_approved') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('manager.approved_by') }} {{ $invoice->approvedBy?->first_name ?? $invoice->approvedBy?->name ?? 'Manager' }}
                        {{ __('manager.on') }} {{ $invoice->payment_approved_at?->format('d M Y, H:i') }}
                    </p>
                    @if($invoice->payment_proof_path)
                        <a href="{{ Storage::url($invoice->payment_proof_path) }}" target="_blank" class="text-sm text-blue-600 underline mt-2 inline-block">
                            {{ __('manager.view_payment_proof') }}
                        </a>
                    @endif
                </div>
            @elseif($invoice->payment_status === 'rejected')
                <div class="bg-red-50 rounded-2xl border border-red-200 p-6">
                    <h3 class="text-lg font-semibold text-red-800">❌ {{ __('manager.payment_rejected') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ __('manager.reason') }}: {{ $invoice->rejection_reason }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ __('manager.waiting_parent_resubmit') }}</p>
                </div>
            @endif

            {{-- Payment Processing Timeline --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Processing</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $invoice->status !== 'draft' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-sm">1</div>
                        <div>
                            <p class="font-medium {{ $invoice->status !== 'draft' ? 'text-green-700' : 'text-gray-500' }}">Invoice Sent</p>
                            <p class="text-xs text-gray-400">Manager sends invoice to parent</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ in_array($invoice->payment_status, ['payment_submitted', 'approved']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-sm">2</div>
                        <div>
                            <p class="font-medium {{ in_array($invoice->payment_status, ['payment_submitted', 'approved']) ? 'text-green-700' : 'text-gray-500' }}">Payment Submitted</p>
                            <p class="text-xs text-gray-400">{{ $invoice->payment_submitted_at ? 'Submitted ' . $invoice->payment_submitted_at->format('d M Y, H:i') : 'Waiting for parent to submit payment' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $invoice->payment_status === 'approved' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-sm">3</div>
                        <div>
                            <p class="font-medium {{ $invoice->payment_status === 'approved' ? 'text-green-700' : 'text-gray-500' }}">Payment Verified & Processed</p>
                            <p class="text-xs text-gray-400">{{ $invoice->payment_approved_at ? 'Processed by ' . ($invoice->approvedBy->first_name ?? $invoice->approvedBy->name ?? 'Manager') . ' on ' . $invoice->payment_approved_at->format('d M Y, H:i') : 'Manager reviews and marks as processed' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('manager.line_items') }}
                    </h3>

                    @if ($invoice->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.description') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.qty') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.unit_price') }}</th>
                                        <th class="text-left py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.total') }}</th>
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
                            {{ __('manager.no_line_items') }}
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>