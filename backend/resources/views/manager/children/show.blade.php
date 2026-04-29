<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ $child->first_name }} {{ $child->last_name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.child_profile') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $child)
                <a href="{{ route('manager.children.edit', $child) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                          focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    {{ __('manager.edit') }}
                </a>
                @endcan

                @can('delete', $child)
                <form method="POST" action="{{ route('manager.children.destroy', $child) }}"
                      onsubmit="return confirm('{{ __('manager.delete_child_confirm', ['name' => addslashes($child->first_name . ' ' . $child->last_name)]) }}')">
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

                <a href="{{ route('manager.children.index') }}"
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

            {{-- Allergy banner --}}
            <x-allergy-alert :child="$child" />
            <x-medical-alert :child="$child" />

            {{-- Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.details') }}</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <p>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.full_name') }}:</span>
                            {{ $child->first_name }} {{ $child->last_name }}
                        </p>
                        <p>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.date_of_birth') }}:</span>
                            {{ $child->dob ? $child->dob->format('d M Y') : '—' }}
                        </p>
                        <div>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.room') }}:</span>
                            @can('assignRoom', $child)
                            <form method="POST"
                                  action="{{ route('manager.children.assign-room', $child) }}"
                                  class="mt-2 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="room_id"
                                        class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900
                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                    <option value="">{{ __('manager.unassigned_option') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" @selected($child->room_id == $room->id)>
                                            {{ $room->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                               bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200
                                               dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                               focus:outline-none focus:ring-4 focus:ring-slate-200">
                                    {{ __('manager.update') }}
                                </button>
                            </form>
                            @else
                            <span class="ml-2 text-slate-800 dark:text-slate-200">
                                {{ $child->room?->name ?? __('manager.unassigned_option') }}
                            </span>
                            @endcan
                        </div>
                    </div>
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <div>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.allergies') }}:</span>
                            @if($child->hasAllergies())
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($child->allergyArray() as $allergy)
                                        <span class="inline-flex items-center bg-red-100 text-red-800 text-sm font-medium px-3 py-1.5 rounded-full">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $allergy }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-slate-400 mt-2 text-sm">{{ __('manager.none_recorded') }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('manager.medical_notes') }}:</span>
                            <p class="mt-1">{{ $child->medical_notes ?: __('manager.none') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Linked Parents --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.linked_parents') }}</h3>
                    @can('linkParent', $child)
                        @if ($child->parents->count() < 2)
                            <a href="{{ route('manager.children.link-parent', $child) }}"
                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                      shadow-sm shadow-blue-500/20
                                      hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                      active:translate-y-[1px]">
                                {{ __('manager.link_parent_button') }}
                            </a>
                        @else
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ __('manager.max_parents_reached') }}</span>
                        @endif
                    @endcan
                </div>
                <div class="p-6">
                    @forelse ($child->parents as $parent)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                            <div class="text-sm text-slate-800 dark:text-slate-200">
                                <a href="{{ route('manager.parents.show', $parent) }}"
                                   class="font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $parent->name }}
                                </a>
                                <span class="text-slate-500 dark:text-slate-400 ml-2">{{ $parent->email }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($parent->pivot->relationship_type)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                 bg-slate-100 text-slate-700 border border-slate-200
                                                 dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                        {{ __('manager.relationship_types.' . $parent->pivot->relationship_type) }}
                                    </span>
                                @endif
                                @if ($parent->pivot->legal_guardian)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                 bg-indigo-50 text-indigo-700 border border-indigo-200
                                                 dark:bg-indigo-950/30 dark:text-indigo-300 dark:border-indigo-800/60">
                                        {{ __('manager.legal_guardian_badge') }}
                                    </span>
                                @endif
                                @can('linkParent', $child)
                                <form method="POST"
                                      action="{{ route('manager.children.unlink-parent', [$child, $parent]) }}"
                                      onsubmit="return confirm('{{ __('manager.unlink_parent_confirm', ['name' => addslashes($parent->name)]) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl px-2 py-0.5 text-xs font-semibold
                                                   bg-red-50 text-red-700 border border-red-200 hover:bg-red-100
                                                   dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                                   focus:outline-none focus:ring-4 focus:ring-red-200">
                                        {{ __('manager.unlink') }}
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.no_parents_linked') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Attendance --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.recent_attendance') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.date') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.status') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.room') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentAttendances as $attendance)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ $attendance->date->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                     {{ $attendance->status === 'present'
                                                        ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/30 dark:text-green-300 dark:border-green-800/60'
                                                        : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60' }}">
                                            {{ __('manager.' . $attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $attendance->room->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ __('manager.no_attendance_records_short') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Daily Reports --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.recent_daily_reports') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.date') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.carer') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.excerpt') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentDailyReports as $report)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($report->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $report->carer->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ Str::limit($report->daily_report, 80) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ __('manager.no_daily_reports') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Medication Logs --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.recent_medication_logs') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.date') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.medication') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.dosage') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.carer') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentMedicationLogs as $log)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->medication_name }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->dosage }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->carer->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ __('manager.no_medication_logs') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>