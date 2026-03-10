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

        <form method="POST"
              action="{{ route('carer.medication.store') }}"
              style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);margin-bottom:24px;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">Select Child</label>
                    <select name="child_id" required
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