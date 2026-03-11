<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
            Create Invoice
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('manager.invoices.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="child_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Select Child
                        </label>
                        <select name="child_id" id="child_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            <option value="">Choose a child</option>
                            @foreach ($children as $child)
                                <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>
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
                            <label for="period_start" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Period Start
                            </label>
                            <input type="date" name="period_start" id="period_start" value="{{ old('period_start') }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                        </div>

                        <div>
                            <label for="period_end" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Period End
                            </label>
                            <input type="date" name="period_end" id="period_end" value="{{ old('period_end') }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Due Date
                            </label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Create Invoice Draft
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>