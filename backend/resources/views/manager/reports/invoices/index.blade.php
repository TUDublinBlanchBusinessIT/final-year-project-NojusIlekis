<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.invoices') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.invoices_index_desc') }}
                </p>
            </div>

            <a href="{{ route('manager.invoices.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20
                      hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                      focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]
                      dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                {{ __('manager.create_invoice') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('manager.all_invoices') }}
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.invoice_id') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.child') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.parent') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.period') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.due_date') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.status') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.total') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.action') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($invoices as $invoice)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 {{ $invoice->status === 'cancelled' ? 'opacity-50' : '' }}">
                                    <td class="px-5 py-4 text-slate-900 dark:text-slate-100 font-medium">
                                        #{{ $invoice->id }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $invoice->parent->name ?? __('manager.not_available') }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $invoice->period_start }} {{ __('manager.to') }} {{ $invoice->period_end }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $invoice->due_date }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @if($invoice->status === 'cancelled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                                {{ __('manager.cancelled') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                         bg-slate-100 text-slate-700 border border-slate-200
                                                         dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $invoice->paymentStatusColour() }} ml-1">
                                                {{ $invoice->paymentStatusLabel() }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-slate-900 dark:text-slate-100 font-medium">
                                        €{{ number_format($invoice->total, 2) }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <a href="{{ route('manager.invoices.show', $invoice) }}"
                                           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                  shadow-sm shadow-blue-500/20
                                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                                  active:translate-y-[1px]
                                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                                            {{ __('manager.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-6 text-slate-600 dark:text-slate-300">
                                        {{ __('manager.no_invoices_found') }}
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