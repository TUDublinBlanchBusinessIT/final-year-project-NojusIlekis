<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Children
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($children->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="text-gray-700 dark:text-gray-300">
                        No children are linked to your account yet.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($children as $child)
                        @php
                            $initials = strtoupper(
                                mb_substr((string) $child->first_name, 0, 1) .
                                mb_substr((string) $child->last_name, 0, 1)
                            );

                            $roomLabel = $child->room?->name
                                ?? $child->room?->room_name
                                ?? ($child->room ? 'Assigned room' : 'No room assigned');
                        @endphp

                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-lg font-bold text-gray-700 dark:text-gray-200">
                                        {{ $initials }}
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $child->first_name }} {{ $child->last_name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Room: {{ $roomLabel }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <a href="{{ route('parent.children.show', $child) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                        View profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
=======
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">My Children</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                View profiles, daily updates, and milestone progress for each child.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($children as $child)
                        @php $mp = $child->milestoneProgress(); @endphp
                        <a href="{{ route('parent.children.show', $child) }}"
                           class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700
                                            flex items-center justify-center text-sm font-semibold text-white shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}{{ strtoupper(substr($child->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $child->age_in_months }} months
                                        @if($child->room)
                                            · {{ $child->room->name }}
                                        @endif
                                        @if($mp['total'] > 0)
                                            · {{ $mp['percentage'] }}% milestones
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            No children linked to your account yet.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
>>>>>>> cf022aeca2727a2cf4f4d1f657d7c8f60f10ca7c
