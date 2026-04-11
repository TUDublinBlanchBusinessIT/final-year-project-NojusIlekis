<x-app-layout>
    <div class="max-w-6xl mx-auto py-6">

        <a href="{{ route('carer.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                  dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
            ← {{ __('carer.back_to_dashboard') }}
        </a>

        <h2 class="text-2xl font-bold mb-6 text-white mt-4">
            {{ __('carer.incident_reports') }}
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
                    <label style="font-weight:600;">{{ __('carer.select_child') }}</label>
                    <select name="child_id" required
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">{{ __('carer.choose_child') }}</option>
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
                    <label style="font-weight:600;">{{ __('carer.room_id') }}</label>
                    <select name="room_id" required
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">{{ __('carer.select_room') }}</option>
                        @foreach($children->unique('room_id') as $child)
                            <option value="{{ $child->room_id }}" @selected(old('room_id') == $child->room_id)>
                                {{ $child->room->name ?? __('carer.room_fallback') . ' ' . $child->room_id }}
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
                    <label style="font-weight:600;">{{ __('carer.incident_date') }}</label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', now()->toDateString()) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">{{ __('carer.incident_time') }}</label>
                    <input type="time" name="incident_time" value="{{ old('incident_time', now()->format('H:i')) }}" required
                           style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                </div>

                <div>
                    <label style="font-weight:600;">{{ __('carer.severity') }}</label>
                    <select name="severity" required
                            style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                        <option value="">{{ __('carer.select_severity') }}</option>
                        <option value="low" @selected(old('severity') === 'low')>{{ __('carer.low') }}</option>
                        <option value="medium" @selected(old('severity') === 'medium')>{{ __('carer.medium') }}</option>
                        <option value="high" @selected(old('severity') === 'high')>{{ __('carer.high') }}</option>

                        @error('severity')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </select>
                </div>
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">{{ __('carer.title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="{{ __('carer.incident_title_placeholder') }}"
                       style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">

                        @error('title')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">{{ __('carer.description') }}</label>
                <textarea name="description" rows="4" required
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="{{ __('carer.incident_description_placeholder') }}">{{ old('description') }}</textarea>

                        @error('description')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:15px;">
                <label style="font-weight:600;">{{ __('carer.action_taken') }}</label>
                <textarea name="action_taken" rows="3"
                          style="width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid #ccc;"
                          placeholder="{{ __('carer.action_taken_placeholder') }}">{{ old('action_taken') }}</textarea>

                        @error('action_taken')
                            <p style="color:#dc2626;font-size:14px;margin-top:6px;">{{ $message }}</p>
                        @enderror
            </div>

            <div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="parent_contact_required" value="1" id="parent_contact_required"
                       {{ old('parent_contact_required') ? 'checked' : '' }}>
                <label for="parent_contact_required" style="font-weight:600;">{{ __('carer.parent_contact_required') }}</label>
            </div>

            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
                {{ __('carer.submit_incident_report') }}
            </button>
        </form>

        <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:15px;">{{ __('carer.recent_incident_reports') }}</h3>

            @if($recentIncidents->isEmpty())
                <p>{{ __('carer.no_incident_reports') }}</p>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;">{{ __('carer.child') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.title') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.severity') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.date') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.time') }}</th>
                                <th style="text-align:left;padding:10px;">{{ __('carer.status') }}</th>
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