<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
            {{ __('parent.welcome') }}, {{ auth()->user()->name }}
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

            {{-- Pending Acknowledgements --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        Pending Acknowledgements
                    </h3>
                    @if ($acknowledgements->count())
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                     bg-amber-50 text-amber-700 border border-amber-200
                                     dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                            {{ $acknowledgements->count() }} pending
                        </span>
                    @endif
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($acknowledgements as $ack)
                        <div class="p-5 space-y-4">
                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                <span class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ ucfirst($ack->record_type) }}
                                </span>
                                <span class="text-slate-500 dark:text-slate-400 ml-2">Record #{{ $ack->record_id }}</span>
                            </div>

                            <form method="POST" action="{{ route('parent.acknowledgements.sign', $ack) }}"
                                  class="space-y-3">
                                @csrf
                                <input type="text"
                                       name="signature_name"
                                       placeholder="Type your full name to sign"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                       required>

                                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                                    <input type="checkbox" name="confirm_acknowledgement" value="1" required
                                           class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500
                                                  dark:border-slate-600 dark:bg-slate-900">
                                    I confirm I have read and understood this item
                                </label>

                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white
                                               bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                               shadow-sm shadow-blue-500/20
                                               hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                               active:translate-y-[1px]">
                                    Sign &amp; Acknowledge
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-600 dark:text-slate-300">
                            No pending acknowledgements — you're all caught up!
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Outstanding Invoices --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        Outstanding Invoices
                    </h3>
                    @if ($outstandingInvoices->count())
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                     bg-red-50 text-red-700 border border-red-200
                                     dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60">
                            {{ $outstandingInvoices->count() }} unpaid
                        </span>
                    @endif
                </div>

                {{-- Fee Summary Strip --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Total Outstanding</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            €{{ number_format($totalOutstanding ?? 0, 2) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Next Due Date</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            {{ $nextDueInvoice ? \Carbon\Carbon::parse($nextDueInvoice->due_date)->format('d M Y') : 'None' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Unpaid Invoices</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            {{ $outstandingInvoices->count() }}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Child</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Period</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Due Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Total</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($outstandingInvoices as $invoice)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($invoice->period_start)->format('d M Y') }}
                                        –
                                        {{ \Carbon\Carbon::parse($invoice->period_end)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-700 dark:text-slate-200">
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                            </span>
                                            @if (\Carbon\Carbon::parse($invoice->due_date)->isPast())
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                                             bg-red-50 text-red-700 border border-red-200
                                                             dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60">
                                                    Overdue
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                        €{{ number_format($invoice->total, 2) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('parent.invoices.show', $invoice) }}"
                                           class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                  shadow-sm shadow-blue-500/20
                                                  hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                                  active:translate-y-[1px]">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-sm text-slate-600 dark:text-slate-300">
                                        No outstanding invoices — you're all caught up!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recently Paid --}}
            @if ($recentPaidInvoices->count())
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        Recently Paid
                    </h3>
                    <a href="{{ route('parent.invoices.index') }}"
                       class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        View all invoices →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Child</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Period</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($recentPaidInvoices as $invoice)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        <a href="{{ route('parent.invoices.show', $invoice) }}"
                                           class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($invoice->period_start)->format('d M Y') }}
                                        –
                                        {{ \Carbon\Carbon::parse($invoice->period_end)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                        €{{ number_format($invoice->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            {{-- Children Timelines --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm
            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
            Child Timelines
        </h3>
        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
            View your child’s daily timeline.
        </p>
    </div>

    <div class="p-5 space-y-4">
        @forelse($myChildren as $child)
            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800
                        flex items-center justify-between gap-4">
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        {{ $child->first_name }} {{ $child->last_name }}
                    </h4>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        Room: {{ $child->room->name ?? 'Not assigned' }}
                    </p>
                </div>

                <a href="{{ route('parent.children.timeline', $child) }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px] whitespace-nowrap">
                    View Timeline
                </a>
            </div>
        @empty
            <p class="text-sm text-slate-600 dark:text-slate-300">
                No children linked to your account.
            </p>
        @endforelse
    </div>
</div>

            {{-- Contact Centre --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        Contact Centre
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        Send a quick fee enquiry directly to the centre manager.
                    </p>
                </div>

                <div class="p-5">
                    @if(!$centreManager)
                        <p class="text-sm text-slate-500 dark:text-slate-300">
                            No centre manager is currently available to contact.
                        </p>
                    @else
                        <form method="POST" action="{{ route('parent.dashboard.enquiry.store') }}" class="space-y-4">
                            @csrf

                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                                <span class="font-medium">This enquiry will be sent to:</span>
                                {{ $centreManager->name }}
                            </div>

                            <div>
                                <label for="child_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                                    About child (optional)
                                </label>
                                <select name="child_id" id="child_id"
                                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                    <option value="">General fee enquiry</option>
                                    @foreach($myChildren as $child)
                                        <option value="{{ $child->id }}">
                                            {{ $child->first_name }} {{ $child->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="body" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                                    Message
                                </label>
                                <textarea name="body" id="body" rows="4"
                                          class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none
                                                 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                          placeholder="Type your fee enquiry here..."
                                          required>{{ old('body') }}</textarea>

                                @if ($errors->has('body'))
                                    <p class="text-xs text-red-600 mt-1">{{ $errors->first('body') }}</p>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2 text-sm font-semibold text-white
                                               bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                               shadow-sm shadow-blue-500/20 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                               active:translate-y-[1px]">
                                    Send Enquiry
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
