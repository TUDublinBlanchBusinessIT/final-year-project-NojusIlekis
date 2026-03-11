<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
            Create Invoice
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('manager.invoices.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                Select Child
                            </label>

                            <select name="child_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">Choose a child</option>

                                @foreach($children as $child)
                                    <option value="{{ $child->id }}">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                        @if($child->parent)
                                            - Parent: {{ $child->parent->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Period Start
                                </label>

                                <input type="date"
                                       name="period_start"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Period End
                                </label>

                                <input type="date"
                                       name="period_end"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Due Date
                                </label>

                                <input type="date"
                                       name="due_date"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </div>

                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110">
                            Create Invoice Draft
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>