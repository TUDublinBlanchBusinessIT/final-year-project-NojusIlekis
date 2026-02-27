<x-app-layout>

<div class="max-w-5xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-6 text-white">
        Daily Child Updates
    </h2>

    {{-- success message --}}
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

    {{-- Child Selection --}}
    <label style="font-weight:600;">Select Child</label>
    <select name="child_id" required
            style="width:100%;padding:10px;margin-bottom:15px;border-radius:8px;border:1px solid #ccc;">
        <option value="">Choose child...</option>
        @foreach($children as $child)
            <option value="{{ $child->id }}">
                {{ $child->first_name }} {{ $child->last_name }}
            </option>
        @endforeach
    </select>

    {{-- Daily Report Text --}}
    <label style="font-weight:600;">Daily Behaviour & Wellbeing Report</label>
    <textarea name="daily_report"
              rows="5"
              required
              style="width:100%;padding:12px;margin-bottom:20px;border-radius:8px;border:1px solid #ccc;"
              placeholder="Write how the child was throughout the day..."></textarea>

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

</x-app-layout>