<x-app-layout>

<div class="max-w-6xl mx-auto py-6">

<h2 class="text-2xl font-bold text-white mb-6">
{{ __('manager.incident_reports') }}
</h2>

<form method="GET" class="mb-6">

<label class="text-white mr-2">{{ __('manager.filter_by_date') }}</label>

<input type="date"
       name="date"
       value="{{ $date }}"
       class="rounded-lg px-3 py-2">

<button class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
{{ __('manager.filter') }}
</button>

</form>

<div class="bg-white rounded-xl shadow overflow-hidden">

<table class="w-full">

<thead class="bg-gray-100">
    <tr>
        <th class="p-3 text-left">{{ __('manager.child') }}</th>
        <th class="p-3 text-left">{{ __('manager.title') }}</th>
        <th class="p-3 text-left">{{ __('manager.severity') }}</th>
        <th class="p-3 text-left">{{ __('manager.date') }}</th>
        <th class="p-3 text-left">{{ __('manager.carer') }}</th>
        <th class="p-3 text-left">{{ __('manager.parent_contact') }}</th>
        <th class="p-3 text-left">{{ __('manager.status') }}</th>
        <th class="p-3 text-left">{{ __('manager.acknowledgement') }}</th>
    </tr>
</thead>

<tbody>

@forelse($reports as $report)

<tr class="border-t">

    <td class="p-3">
        {{ $report->child->first_name }} {{ $report->child->last_name }}
    </td>

    <td class="p-3">
        {{ $report->title }}
    </td>

    <td class="p-3">
        {{ ucfirst($report->severity) }}
    </td>

    <td class="p-3">
        {{ $report->incident_date }}
    </td>

    <td class="p-3">
        {{ $report->carer->name }}
    </td>

    <td class="p-3">
        @if($report->parent_contact_required)
            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                {{ __('manager.required') }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                {{ __('manager.not_required') }}
            </span>
        @endif
    </td>

    <td class="p-3">
        <form method="POST" action="{{ route('manager.reports.incidents.status', $report) }}">
        @csrf
        @method('PATCH')

        <select name="status"
            onchange="this.form.submit()"
            class="text-xs rounded-lg border-gray-300">

            <option value="open" {{ $report->status == 'open' ? 'selected' : '' }}>
                {{ __('manager.open') }}
            </option>

            <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>
                {{ __('manager.reviewed') }}
            </option>

            <option value="closed" {{ $report->status == 'closed' ? 'selected' : '' }}>
                {{ __('manager.closed') }}
            </option>

        </select>
    </form>
    </td>

    <td class="p-3">
        @if($report->acknowledgement)
            @if($report->acknowledgement->status === 'acknowledged')
                <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('manager.acknowledged') }}
                </span>
                <span class="text-xs text-gray-500 ml-2">
                    {{ __('manager.by') }} {{ $report->acknowledgement->signature_name }}
                    {{ __('manager.on') }} {{ $report->acknowledgement->signed_at->format('d M Y, H:i') }}
                </span>
            @else
                <span class="inline-flex items-center bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('manager.pending') }}
                </span>
            @endif
        @else
            <span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                {{ __('manager.not_requested') }}
            </span>
        @endif
    </td>

</tr>

@empty

<tr>
    <td colspan="8" class="p-6 text-center text-gray-500">
        {{ __('manager.no_incident_reports') }}
    </td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</x-app-layout>