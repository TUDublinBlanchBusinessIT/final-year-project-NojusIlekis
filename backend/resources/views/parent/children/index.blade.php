<x-app-layout>
    <x-slot name="header">
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