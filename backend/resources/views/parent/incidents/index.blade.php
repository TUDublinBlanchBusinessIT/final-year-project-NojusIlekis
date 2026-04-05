<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Incidents
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg dark:bg-green-900/30 dark:border-green-700 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Incident Reports
                </h3>

                @if ($incidents->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">
                        No incidents found for your linked children.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                            <thead>
                                <tr class="border-b dark:border-gray-700 text-left text-gray-700 dark:text-gray-200">
                                    <th class="py-3 pr-4 font-semibold">Date</th>
                                    <th class="py-3 pr-4 font-semibold">Child</th>
                                    <th class="py-3 pr-4 font-semibold">Title</th>
                                    <th class="py-3 pr-4 font-semibold">Severity</th>
                                    <th class="py-3 pr-4 font-semibold">Status</th>
                                    <th class="py-3 pr-4 font-semibold">Acknowledgement</th>
                                    <th class="py-3 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incidents as $incident)
                                    <tr class="border-b dark:border-gray-700 text-gray-800 dark:text-gray-100">
                                        <td class="py-3 pr-4">
                                            {{ \Carbon\Carbon::parse($incident->incident_date)->format('d M Y') }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            {{ $incident->child->first_name }} {{ $incident->child->last_name }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            {{ $incident->title }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            @php
                                                $severityClasses = match($incident->severity) {
                                                'high' => 'bg-red-900/40 text-red-300 border border-red-700',
                                                'medium' => 'bg-amber-900/40 text-amber-300 border border-amber-700',
                                                default => 'bg-emerald-900/40 text-emerald-300 border border-emerald-700',
                                            };
                                        @endphp

                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $severityClasses }}">
                                            {{ ucfirst($incident->severity) }}
                                        </span>
                                    </td>
                                        <td class="py-3 pr-4">
                                            @php
                                                $statusClasses = match($incident->status) {
                                                    'closed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'reviewed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                                };
                                            @endphp

                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                                {{ ucfirst($incident->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($incident->acknowledgement && $incident->acknowledgement->status === 'acknowledged')
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-emerald-900/40 text-emerald-300 border border-emerald-700">
                                                    Acknowledged
                                                </span>
                                            @elseif ($incident->acknowledgement)
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-amber-900/40 text-amber-300 border border-amber-700">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                                    Not requested
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ route('parent.incidents.show', $incident) }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>