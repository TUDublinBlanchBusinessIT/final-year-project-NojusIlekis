<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                {{ __('manager.children.link_parent_title', [
                    'first_name' => $child->first_name,
                    'last_name' => $child->last_name,
                ]) }}
            </h2>

            <a href="{{ route('manager.children.show', $child) }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                {{ __('manager.children.back_to_child', [
                    'first_name' => $child->first_name,
                    'last_name' => $child->last_name,
                ]) }}
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
                            {{ __('manager.children.no_available_parents') }}
                            <a href="{{ route('manager.parents.create') }}"
                               class="ml-1 text-blue-600 hover:underline dark:text-blue-400">
                                {{ __('manager.parents.create_first') }}
                            </a>
                        </p>
                    @else
                        <form method="POST"
                              action="{{ route('manager.children.link-parent.store', $child) }}"
                              class="space-y-5">
                            @csrf

                            <div x-data="{
                                search: '',
                                parents: @js($availableParents->map(fn($p) => [
                                    'id' => $p->id,
                                    'label' => $p->name . ' (' . $p->email . ')'
                                ])),
                                get filtered() {
                                    if (!this.search) return this.parents;
                                    const q = this.search.toLowerCase();
                                    return this.parents.filter(p => p.label.toLowerCase().includes(q));
                                }
                            }">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.children.parent') }} <span class="text-red-500">*</span>
                                </label>

                                <input type="text"
                                       x-model="search"
                                       placeholder="{{ __('manager.children.search_parent_placeholder') }}"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 mb-2
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">

                                <select name="parent_id" id="parent_id"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                               @error('parent_id') border-red-400 @enderror">
                                    <option value="">{{ __('manager.children.select_parent') }}</option>
                                    <template x-for="p in filtered" :key="p.id">
                                        <option :value="p.id" :selected="p.id == {{ old('parent_id', 'null') }}" x-text="p.label"></option>
                                    </template>
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="relationship_type" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.children.relationship_type') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="relationship_type" id="relationship_type"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                               @error('relationship_type') border-red-400 @enderror">
                                    <option value="">{{ __('manager.children.select_relationship') }}</option>

                                    @foreach (__('manager.relationship_types') as $value => $label)
                                        <option value="{{ $value }}" @selected(old('relationship_type') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
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
                                    {{ __('manager.children.legal_guardian') }}
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
                                    {{ __('manager.children.link_parent_button') }}
                                </button>

                                <a href="{{ route('manager.children.show', $child) }}"
                                   class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium
                                          bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200
                                          dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                          focus:outline-none focus:ring-4 focus:ring-slate-200">
                                    {{ __('manager.actions.cancel') }}
                                </a>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>