<x-app-layout>

<div class="max-w-5xl mx-auto py-6">

    {{-- Back Button --}}
    <a href="{{ route('carer.dashboard') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
              dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
        ← Back to Dashboard
    </a>

    <h2 class="text-2xl font-bold mb-2 text-white mt-4">
        Daily Child Updates
    </h2>

    {{-- Current Time (Live) --}}
    <div class="mb-6 text-slate-300 text-sm">
        Current Time: <span id="currentTime" class="font-semibold text-white"></span>
    </div>

    {{-- success / error messages --}}
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:10px;margin-bottom:15px;border-radius:8px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:10px;margin-bottom:15px;border-radius:8px;">
            {{ session('error') }}
        </div>
    @endif


    <form method="POST"
          action="{{ route('carer.daily-reports.store') }}"
          enctype="multipart/form-data"
          style="background:white;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.05);">

        @csrf

        {{-- Hidden field to submit current time --}}
        <input type="hidden" name="report_time" id="reportTimeInput">

        {{-- Date + Class Row --}}
        <div style="display:flex;gap:15px;flex-wrap:wrap;margin-bottom:15px;">

            {{-- Select Date --}}
            <div style="flex:1;min-width:220px;">
                <label style="font-weight:600;">Select Date</label>
                <input type="date"
                       name="date"
                       value="{{ old('date', now()->toDateString()) }}"
                       required
                       style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
            </div>

            {{-- Class Dropdown --}}
            <div style="flex:1;min-width:220px;">
                <label style="font-weight:600;">Class</label>
                <select name="room"
                        required
                        style="width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;">
                    <option value="">Select class...</option>
                    <option value="Ladybirds"  @selected(old('room') === 'Ladybirds')>Ladybirds</option>
                    <option value="Bumblebees" @selected(old('room') === 'Bumblebees')>Bumblebees</option>
                </select>
            </div>

        </div>


        {{-- Child Selection --}}
        <label style="font-weight:600;">Select Child</label>
        <select name="child_id" required
                style="width:100%;padding:10px;margin-bottom:15px;border-radius:8px;border:1px solid #ccc;">
            <option value="">Choose child...</option>
            @foreach($children as $child)
                <option value="{{ $child->id }}" @selected(old('child_id') == $child->id)>
                    {{ $child->first_name }} {{ $child->last_name }}
                </option>
            @endforeach
        </select>


        {{-- Daily Report Label + Edit Button --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <label style="font-weight:600;">Daily Behaviour & Wellbeing Report</label>

            <button type="button"
                    id="editReportBtn"
                    style="background:#f1f5f9;border:1px solid #cbd5e1;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:600;">
                ✏️ Edit
            </button>
        </div>

        {{-- Daily Report Text --}}
        <textarea id="dailyReportTextarea"
                  name="daily_report"
                  rows="5"
                  required
                  readonly
                  style="width:100%;padding:12px;margin-bottom:10px;border-radius:8px;border:1px solid #ccc;background:#f8fafc;"
                  placeholder="Write how the child was throughout the day...">{{ old('daily_report') }}</textarea>

        <p id="editHint" style="margin-bottom:20px;color:#64748b;font-size:14px;">
            Click <strong>Edit</strong> to update this report.
        </p>


        {{-- Media Upload --}}
        <label style="font-weight:600;">Upload Photos / Videos</label>
        <input type="file"
               name="media[]"
               id="mediaInput"
               multiple
               accept="image/*,video/*"
               style="display:block;margin-top:10px;margin-bottom:15px;">

        {{-- Preview Area --}}
        <div id="previewContainer"
             style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;"></div>

        <button type="submit"
                style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
            Save Daily Report
        </button>

    </form>

</div>


{{-- Live Time Script --}}
<script>
function updateTime() {
    const now = new Date();

    const formatted = now.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    const display = document.getElementById('currentTime');
    const hiddenInput = document.getElementById('reportTimeInput');

    if (display) display.textContent = formatted;
    if (hiddenInput) hiddenInput.value = formatted;
}

document.addEventListener('DOMContentLoaded', () => {
    updateTime();
    setInterval(updateTime, 1000);
});
</script>


{{-- Edit Button Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('editReportBtn');
    const textarea = document.getElementById('dailyReportTextarea');
    const hint = document.getElementById('editHint');

    if (!btn || !textarea) return;

    btn.addEventListener('click', () => {
        const isReadonly = textarea.hasAttribute('readonly');

        if (isReadonly) {
            textarea.removeAttribute('readonly');
            textarea.style.background = '#ffffff';
            textarea.focus();
            btn.textContent = '✅ Done';
            if (hint) hint.textContent = 'You can now edit the report.';
        } else {
            textarea.setAttribute('readonly', 'readonly');
            textarea.style.background = '#f8fafc';
            btn.textContent = '✏️ Edit';
            if (hint) hint.innerHTML = 'Click <strong>Edit</strong> to update this report.';
        }
    });
});
</script>

</x-app-layout>