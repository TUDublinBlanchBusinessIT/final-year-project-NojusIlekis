<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ $parentUser->name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.parent_profile') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $parentUser)
                <a href="{{ route('manager.parents.edit', $parentUser) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                          focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    {{ __('manager.edit') }}
                </a>
                @endcan

                @can('delete', $parentUser)
                <form method="POST" action="{{ route('manager.parents.destroy', $parentUser) }}"
                      onsubmit="return confirm('Delete {{ addslashes($parentUser->name) }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                   bg-red-50 text-red-700 border border-red-200
                                   hover:bg-red-100
                                   dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                   focus:outline-none focus:ring-4 focus:ring-red-200">
                        {{ __('manager.delete') }}
                    </button>
                </form>
                @endcan

                <a href="{{ route('manager.parents.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                    {{ __('manager.back') }}
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

            @if (session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                            dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.details') }}</h3>
                </div>
                <div class="p-6 space-y-3 text-sm text-slate-800 dark:text-slate-200">
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.name') }}:</span>
                        {{ $parentUser->name }}
                    </p>
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.email') }}:</span>
                        {{ $parentUser->email }}
                    </p>
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.registered') }}:</span>
                        {{ $parentUser->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Linked Children --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.linked_children') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.name') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.room') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.relationship') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($parentUser->children as $child)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-3 font-medium text-slate-900 dark:text-slate-100">
                                        <a href="{{ route('manager.children.show', $child) }}"
                                           class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $child->first_name }} {{ $child->last_name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        @if ($child->room)
                                            {{ $child->room->name }}
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                         bg-amber-50 text-amber-700 border border-amber-200
                                                         dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                                                {{ __('manager.unassigned') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        @if ($child->pivot->relationship_type)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                         bg-slate-100 text-slate-700 border border-slate-200
                                                         dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                                {{ ucfirst($child->pivot->relationship_type) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                        {{ __('manager.no_children_linked_parent') }}
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