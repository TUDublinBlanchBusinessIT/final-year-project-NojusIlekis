<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    Children
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Manage all enrolled children.
                </p>
            </div>

            @can('create', App\Models\Child::class)
            <a href="{{ route('manager.children.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20
                      hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                      focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]
                      dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                Add Child
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

            {{-- Search & Filter --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-5">
                    <form method="GET" action="{{ route('manager.children.index') }}"
                          class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name…"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
                        </div>

                        <div class="sm:w-48">
                            <select name="room"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">All Rooms</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" @selected(request('room') == $room->id)>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200
                                       active:translate-y-[1px]">
                            Search
                        </button>

                        @if (request('search') || request('room'))
                            <a href="{{ route('manager.children.index') }}"
                               class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium
                                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                Clear
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
                        All Children
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $children->total() }})
                        </span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Name
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Date of Birth
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Room
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Parents
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($children as $child)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                        @if($child->hasAllergies())
                                            <span class="ml-1.5 bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">⚠️ Allergies</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $child->dob ? $child->dob->format('d M Y') : '—' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($child->room)
                                            <span class="text-slate-700 dark:text-slate-200">{{ $child->room->name }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                         bg-amber-50 text-amber-700 border border-amber-200
                                                         dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        @if ($child->parents->count())
                                            {{ $child->parents->map(fn($p) => $p->name)->join(', ') }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">None</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('manager.children.show', $child) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                      shadow-sm shadow-blue-500/20
                                                      hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                                      active:translate-y-[1px]">
                                                View
                                            </a>

                                            @can('update', $child)
                                            <a href="{{ route('manager.children.edit', $child) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                      bg-slate-100 text-slate-700 border border-slate-200
                                                      hover:bg-slate-200
                                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                Edit
                                            </a>
                                            @endcan

                                            @can('delete', $child)
                                            <form method="POST" action="{{ route('manager.children.destroy', $child) }}"
                                                  onsubmit="return confirm('Delete {{ addslashes($child->first_name . ' ' . $child->last_name) }}? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                               bg-red-50 text-red-700 border border-red-200
                                                               hover:bg-red-100
                                                               dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                                               focus:outline-none focus:ring-4 focus:ring-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-600 dark:text-slate-300">
                                        No children found.
                                        @if (request('search') || request('room'))
                                            <a href="{{ route('manager.children.index') }}" class="ml-1 text-blue-600 hover:underline dark:text-blue-400">
                                                Clear filters
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($children->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                        {{ $children->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
