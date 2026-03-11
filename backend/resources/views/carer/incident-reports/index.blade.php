<x-app-layout>
    <div class="max-w-6xl mx-auto py-6">

        <a href="{{ route('carer.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                  dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
            ← Back to Dashboard
        </a>

        <h2 class="text-2xl font-bold mb-6 text-white mt-4">
            Incident Reports
        </h2>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('carer.incident-reports.store') }}"
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

                        @error('child_id')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </select>
                </div>

                <div>
                    <label style="font-weight:600;">Room ID</label>
                    <select name="room_id" required
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">Select room...</option>
                        @foreach($children->unique('room_id') as $child)
                            <option value="{{ $child->room_id }}" @selected(old('room_id') == $child->room_id)>
                                {{ $child->room->name ?? 'Room '.$child->room_id }}
                            </option>
                        @endforeach

                        @error('room_id')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;margin-bottom:15px;">
                <div>
                    <label style="font-weight:600;">Incident Date</label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', now()->toDateString()) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">Incident Time</label>
                    <input type="time" name="incident_time" value="{{ old('incident_time', now()->format('H:i')) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">Severity</label>
                    <select name="severity" required
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">Select severity...</option>
                        <option value="low" @selected(old('severity') === 'low')>Low</option>
                        <option value="medium" @selected(old('severity') === 'medium')>Medium</option>
                        <option value="high" @selected(old('severity') === 'high')>High</option>

                        @error('severity')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </select>
                </div>
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Fall during outdoor play"
                       style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">

                        @error('title')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">Description</label>
                <textarea name="description" rows="4" required
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="Describe what happened...">{{ old('description') }}</textarea>

                        @error('description')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">Action Taken</label>
                <textarea name="action_taken" rows="3"
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="Describe what action was taken...">{{ old('action_taken') }}</textarea>

                        @error('action_taken')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="parent_contact_required" value="1" id="parent_contact_required"
                       {{ old('parent_contact_required') ? 'checked' : '' }}>
                <label for="parent_contact_required" style="font-weight:600;">Parent contact required</label>
            </div>

            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
                Submit Incident Report
            </button>
        </form>

        <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:15px;">Recent Incident Reports</h3>

            @if($recentIncidents->isEmpty())
                <p>No incident reports yet.</p>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;">Child</th>
                                <th style="text-align:left;padding:10px;">Title</th>
                                <th style="text-align:left;padding:10px;">Severity</th>
                                <th style="text-align:left;padding:10px;">Date</th>
                                <th style="text-align:left;padding:10px;">Time</th>
                                <th style="text-align:left;padding:10px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentIncidents as $incident)
                                <tr>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">
                                        {{ $incident->child->first_name }} {{ $incident->child->last_name }}
                                    </td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $incident->title }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ ucfirst($incident->severity) }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $incident->incident_date }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ $incident->incident_time }}</td>
                                    <td style="padding:10px;border-top:1px solid #e5e7eb;">{{ ucfirst($incident->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>