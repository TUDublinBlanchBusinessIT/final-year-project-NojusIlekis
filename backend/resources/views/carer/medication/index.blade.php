<x-app-layout>
    <div class="max-w-5xl mx-auto py-6">

        <a href="{{ route('carer.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                  dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
            ← Back to Dashboard
        </a>

        <h2 class="text-2xl font-bold mb-6 text-white mt-4">
            Medication Logs
        </h2>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @php
            $childMap = $children->keyBy('id')->map(fn($c) => [
                'name'          => $c->first_name . ' ' . $c->last_name,
                'hasAllergies'  => $c->hasAllergies(),
                'allergies'     => $c->allergyList(),
                'medical_notes' => $c->medical_notes,
            ]);
        @endphp

        <div x-data="{
                childMap: @json($childMap),
                selectedId: '{{ old('child_id', '') }}',
                get child() { return this.childMap[this.selectedId] ?? null; }
             }">

            {{-- Allergy alert (red) --}}
            <template x-if="child && child.hasAllergies">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold text-red-800">⚠️ Allergy Alert — <span x-text="child.name"></span></p>
                            <p class="text-sm mt-1">Allergies: <strong x-text="child.allergies"></strong></p>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Medical notes alert (amber) --}}
            <template x-if="child && child.medical_notes">
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 mb-4 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold">📋 Medical Notes — <span x-text="child.name"></span></p>
                            <p class="text-sm mt-1" x-text="child.medical_notes"></p>
                        </div>
                    </div>
                </div>
            </template>

        <form method="POST"
              action="{{ route('carer.medication.store') }}"
              style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);margin-bottom:24px;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">Select Child</label>
                    <select name="child_id" required
                            @change="selectedId = $event.target.value"
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">Choose child...</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @selected(old('child_id') == $child->id)>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight:600;">Medication Name</label>
                    <input type="text" name="medication_name" value="{{ old('medication_name') }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">Dosage</label>
                    <input type="text" name="dosage" value="{{ old('dosage') }}" required
                           placeholder="e.g. 5ml"
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">Date</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">Time Given</label>
                    <input type="time" name="time_given" value="{{ old('time_given', now()->format('H:i')) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-weight:600;">Notes</label>
                <textarea name="notes" rows="4"
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="Any extra notes...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
                Save Medication Log
            </button>
        </form>
        </div>{{-- end x-data --}}

        <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:15px;">Recent Medication Logs</h3>

            @if($recentLogs->isEmpty())
                <p>No medication logs yet.</p>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;">Child</th>
                                <th style="text-align:left;padding:10px;">Medication</th>
                                <th style="text-align:left;padding:10px;">Dosage</th>
                                <th style="text-align:left;padding:10px;">Date</th>
                                <th style="text-align:left;padding:10px;">Time</th>
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