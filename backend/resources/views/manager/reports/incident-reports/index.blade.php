<x-app-layout>

<div class="max-w-6xl mx-auto py-6">

<h2 class="text-2xl font-bold text-white mb-6">
Incident Reports
</h2>

<form method="GET" class="mb-6">

<label class="text-white mr-2">Filter by date</label>

<input type="date"
       name="date"
       value="{{ $date }}"
       class="rounded-lg px-3 py-2">

<button class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
Filter
</button>

</form>

<div class="bg-white rounded-xl shadow overflow-hidden">

<table class="w-full">

<thead class="bg-gray-100">
    <tr>
        <th class="p-3 text-left">Child</th>
        <th class="p-3 text-left">Title</th>
        <th class="p-3 text-left">Severity</th>
        <th class="p-3 text-left">Date</th>
        <th class="p-3 text-left">Carer</th>
        <th class="p-3 text-left">Parent Contact</th>
        <th class="p-3 text-left">Status</th>
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
                Required
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                Not Required
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
                Open
            </option>

            <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>
                Reviewed
            </option>

            <option value="closed" {{ $report->status == 'closed' ? 'selected' : '' }}>
                Closed
            </option>

        </select>
    </form>
    </td>

</tr>

@empty

<tr>
    <td colspan="7" class="p-6 text-center text-gray-500">
        No incident reports found
    </td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</x-app-layout>