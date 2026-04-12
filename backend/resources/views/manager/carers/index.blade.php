<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.carers') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.manage_carers') }}
                </p>
            </div>

            @can('create', App\Models\User::class)
            <a href="{{ route('manager.carers.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20
                      hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                      focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]
                      dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                {{ __('manager.add_carer') }}
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
                    <form method="GET" action="{{ route('manager.carers.index') }}"
                          class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('manager.search_carers') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
                        </div>

                        <div class="sm:w-48">
                            <select name="room"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">{{ __('manager.all_rooms') }}</option>
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
                            {{ __('manager.search') }}
                        </button>

                        @if (request('search') || request('room'))
                            <a href="{{ route('manager.carers.index') }}"
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
                        {{ __('manager.all_carers') }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $carers->total() }})
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
                                    {{ __('manager.current_room') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.actions') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($carers as $carerUser)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $carerUser->name }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $carerUser->email }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @php($activeRoom = $carerUser->activeRooms->first())
                                        @if ($activeRoom)
                                            <span class="text-slate-700 dark:text-slate-200">{{ $activeRoom->name }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                         bg-amber-50 text-amber-700 border border-amber-200
                                                         dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                                                {{ __('manager.unassigned') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('manager.carers.show', $carerUser) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                      shadow-sm shadow-blue-500/20
                                                      hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                                      active:translate-y-[1px]">
                                                {{ __('manager.view') }}
                                            </a>

                                            @can('update', $carerUser)
                                            <a href="{{ route('manager.carers.edit', $carerUser) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                      bg-slate-100 text-slate-700 border border-slate-200
                                                      hover:bg-slate-200
                                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                {{ __('manager.edit') }}
                                            </a>
                                            @endcan

                                            @can('delete', $carerUser)
                                            <form method="POST" action="{{ route('manager.carers.destroy', $carerUser) }}"
                                                  onsubmit="return confirm('Delete {{ addslashes($carerUser->name) }}? This cannot be undone.')">
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
                                        {{ __('manager.no_carers') }}
                                        @if (request('search') || request('room'))
                                            <a href="{{ route('manager.carers.index') }}" class="ml-1 text-blue-600 hover:underline dark:text-blue-400">
                                                {{ __('manager.clear_filters') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($carers->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                        {{ $carers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>