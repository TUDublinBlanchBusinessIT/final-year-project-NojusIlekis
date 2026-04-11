<x-app-layout>
    <div class="max-w-5xl mx-auto py-6">

        <a href="{{ route('carer.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                  dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
            ← {{ __('carer.back_to_dashboard') }}
        </a>

        <h2 class="text-2xl font-bold mb-6 text-white mt-4">
            {{ __('carer.medication_logs') }}
        </h2>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div x-data="{ childId: null, data: {{ Js::from($allergyData) }} }">

            <template x-if="childId && data[childId]?.has_allergies">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-sm" role="alert">
                    <p class="font-bold text-red-800">⚠️ {{ __('carer.allergy_alert') }} — <span x-text="data[childId].name"></span></p>
                    <p class="text-sm mt-1">{{ __('carer.allergies') }}: <strong x-text="data[childId].allergies"></strong></p>
                </div>
            </template>

            <template x-if="childId && data[childId]?.medical_notes">
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 mb-4 rounded-lg shadow-sm" role="alert">
                    <p class="font-bold">📋 {{ __('carer.medical_notes') }} — <span x-text="data[childId].name"></span></p>
                    <p class="text-sm mt-1" x-text="data[childId].medical_notes"></p>
                </div>
            </template>

        <form method="POST"
              action="{{ route('carer.medication.store') }}"
              style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);margin-bottom:24px;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">{{ __('carer.select_child') }}</label>
                    <select name="child_id" required
                            x-model="childId"
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">{{ __('carer.choose_child') }}</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @selected(old('child_id') == $child->id)>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight:600;">{{ __('carer.medication_name') }}</label>
                    <input type="text" name="medication_name" value="{{ old('medication_name') }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">{{ __('carer.dosage') }}</label>
                    <input type="text" name="dosage" value="{{ old('dosage') }}" required
                           placeholder="{{ __('carer.dosage_placeholder') }}"
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">{{ __('carer.date') }}</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">{{ __('carer.time_given') }}</label>
                    <input type="time" name="time_given" value="{{ old('time_given', now()->format('H:i')) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-weight:600;">{{ __('carer.notes') }}</label>
                <textarea name="notes" rows="4"
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="{{ __('carer.notes_placeholder') }}">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
                {{ __('carer.save_medication_log') }}
            </button>
        </form>
        </div>{{-- end x-data --}}

        <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:15px;">{{ __('carer.recent_medication_logs') }}</h3>

            @if($recentLogs->isEmpty())
                <p>{{ __('carer.no_medication_logs') }}</p>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;">{{ __('carer.child') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.medication') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.dosage') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.date') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                                <tr>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">
                                        {{ $log->child->first_name }} {{ $log->child->last_name }}
                                    </td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $log->medication_name }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $log->dosage }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $log->date }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $log->time_given }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>