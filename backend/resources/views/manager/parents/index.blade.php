<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.parents') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.manage_parents') }}
                </p>
            </div>

            @can('create', App\Models\User::class)
            <a href="{{ route('manager.parents.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20
                      hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                      focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]
                      dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                {{ __('manager.add_parent') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-5">
                    <form method="GET" action="{{ route('manager.parents.index') }}"
                          class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('manager.search_parent_placeholder') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200
                                       active:translate-y-[1px]">
                            {{ __('manager.search') }}
                        </button>

                        @if (request('search'))
                            <a href="{{ route('manager.parents.index') }}"
                               class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium
                                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                {{ __('manager.clear') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('manager.all_parents') }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $parents->total() }})
                        </span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.name') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.email') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.children') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.actions') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($parents as $parentUser)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $parentUser->name }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $parentUser->email }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        @if ($parentUser->children_count > 0)
                                            {{ $parentUser->children_count }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">{{ __('manager.none') }}</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('manager.parents.show', $parentUser) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                      shadow-sm shadow-blue-500/20
                                                      hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                                      active:translate-y-[1px]">
                                                {{ __('manager.view') }}
                                            </a>

                                            @can('update', $parentUser)
                                            <a href="{{ route('manager.parents.edit', $parentUser) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                      bg-slate-100 text-slate-700 border border-slate-200
                                                      hover:bg-slate-200
                                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                {{ __('manager.edit') }}
                                            </a>
                                            @endcan

                                            @can('delete', $parentUser)
                                            <form method="POST" action="{{ route('manager.parents.destroy', $parentUser) }}"
                                                  onsubmit="return confirm('{{ __('manager.delete_confirm') }} {{ addslashes($parentUser->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                               bg-red-50 text-red-700 border border-red-200
                                                               hover:bg-red-100
                                                               dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                                               focus:outline-none focus:ring-4 focus:ring-red-200">
                                                    {{ __('manager.delete') }}
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-600 dark:text-slate-300">
                                        {{ __('manager.no_parents_found') }}
                                        @if (request('search'))
                                            <a href="{{ route('manager.parents.index') }}" class="ml-1 text-blue-600 hover:underline dark:text-blue-400">
                                                {{ __('manager.clear') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($parents->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                        {{ $parents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>