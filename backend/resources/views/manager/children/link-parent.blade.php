<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                Link Parent to {{ $child->first_name }} {{ $child->last_name }}
            </h2>

            <a href="{{ route('manager.children.show', $child) }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                Back to {{ $child->first_name }} {{ $child->last_name }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                                    dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                                    dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($availableParents->isEmpty())
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            All parents are already linked or no parents exist.
                            <a href="{{ route('manager.parents.create') }}"
                               class="ml-1 text-blue-600 hover:underline dark:text-blue-400">
                                Create a parent first.
                            </a>
                        </p>
                    @else
                        <form method="POST"
                              action="{{ route('manager.children.link-parent.store', $child) }}"
                              class="space-y-5">
                            @csrf

                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Parent <span class="text-red-500">*</span>
                                </label>
                                <select name="parent_id" id="parent_id"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                               @error('parent_id') border-red-400 @enderror">
                                    <option value="">Select a parent…</option>
                                    @foreach ($availableParents as $parentUser)
                                        <option value="{{ $parentUser->id }}" @selected(old('parent_id') == $parentUser->id)>
                                            {{ $parentUser->name }} ({{ $parentUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="relationship_type" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Relationship Type <span class="text-red-500">*</span>
                                </label>
                                <select name="relationship_type" id="relationship_type"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                               @error('relationship_type') border-red-400 @enderror">
                                    <option value="">Select relationship…</option>
                                    <option value="mother"      @selected(old('relationship_type') === 'mother')>Mother</option>
                                    <option value="father"      @selected(old('relationship_type') === 'father')>Father</option>
                                    <option value="guardian"    @selected(old('relationship_type') === 'guardian')>Guardian</option>
                                    <option value="grandparent" @selected(old('relationship_type') === 'grandparent')>Grandparent</option>
                                    <option value="other"       @selected(old('relationship_type') === 'other')>Other</option>
                                </select>
                                @error('relationship_type')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox"
                                       name="legal_guardian"
                                       id="legal_guardian"
                                       value="1"
                                       @checked(old('legal_guardian'))
                                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500
                                              dark:border-slate-600 dark:bg-slate-900">
                                <label for="legal_guardian" class="text-sm text-slate-700 dark:text-slate-200">
                                    This person is a legal guardian
                                </label>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 font-semibold text-white
                                               bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                               shadow-sm shadow-blue-500/20
                                               hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                               focus:outline-none focus:ring-4 focus:ring-blue-200
                                               active:translate-y-[1px]">
                                    Link Parent
                                </button>

                                <a href="{{ route('manager.children.show', $child) }}"
                                   class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium
                                          bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200
                                          dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                          focus:outline-none focus:ring-4 focus:ring-slate-200">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
