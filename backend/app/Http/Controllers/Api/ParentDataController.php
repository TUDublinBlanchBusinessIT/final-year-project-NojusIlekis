<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ParentDataController extends Controller
{
    // -----------------------------------------------------------------------
    // Children
    // -----------------------------------------------------------------------

    public function children(Request $request): JsonResponse
    {
        $children = $request->user()
            ->children()
            ->with('room')
            ->get()
            ->map(fn ($child) => $this->childSummary($child));

        return response()->json($children);
    }

    public function showChild(Request $request, Child $child): JsonResponse
    {
        $this->authoriseChild($request, $child);

        $child->load([
            'room',
            'attendances' => fn ($q) => $q->latest('date')->limit(10),
            'dailyReports' => fn ($q) => $q->latest('date')->limit(5),
            'medicationLogs' => fn ($q) => $q->latest('date')->limit(5),
        ]);

        return response()->json($child);
    }

    // -----------------------------------------------------------------------
    // Child sub-resources
    // -----------------------------------------------------------------------

    public function childAttendance(Request $request, Child $child): JsonResponse
    {
        $this->authoriseChild($request, $child);

        $from = $request->query('from_date', now()->subDays(30)->toDateString());
        $to   = $request->query('to_date', now()->toDateString());

        $attendance = $child->attendances()
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($attendance);
    }

    public function childDailyUpdates(Request $request, Child $child): JsonResponse
    {
        $this->authoriseChild($request, $child);

        $from = $request->query('from_date', now()->subDays(30)->toDateString());
        $to   = $request->query('to_date', now()->toDateString());

        $updates = $child->dailyUpdates()
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($updates);
    }

    // -----------------------------------------------------------------------
    // Invoices
    // -----------------------------------------------------------------------

    public function invoices(Request $request): JsonResponse
    {
        $invoices = Invoice::where('parent_id', $request->user()->id)
            ->with(['child', 'items'])
            ->latest()
            ->get();

        return response()->json($invoices);
    }

    public function showInvoice(Request $request, Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->parent_id === $request->user()->id, 403);

        $invoice->load(['child', 'items']);

        return response()->json($invoice);
    }

    // -----------------------------------------------------------------------
    // Timeline
    // -----------------------------------------------------------------------

    /**
     * Get a child's daily timeline — all events merged and sorted chronologically.
     * GET /api/parent/children/{child}/timeline?date=YYYY-MM-DD
     */
    public function timeline(Request $request, Child $child): JsonResponse
    {
        $this->authoriseChild($request, $child);

        $date = $request->query('date', today()->toDateString());

        $events = collect()
            ->merge($this->timelineAttendanceEvents($child, $date))
            ->merge($this->timelineDailyUpdateEvents($child, $date))
            ->merge($this->timelineDailyReportEvents($child, $date))
            ->merge($this->timelineMedicationEvents($child, $date))
            ->merge($this->timelineIncidentEvents($child, $date))
            ->sortBy('timestamp')
            ->values();

        return response()->json([
            'child_id'    => $child->id,
            'child_name'  => $child->first_name . ' ' . $child->last_name,
            'date'        => $date,
            'event_count' => $events->count(),
            'events'      => $events->map(fn ($e) => [
                'type'        => $e['type'],
                'icon'        => $e['icon'],
                'title'       => $e['title'],
                'description' => $e['details'],
                'time'        => $e['timestamp']->format('g:i A'),
                'severity'    => $e['severity'] ?? null,
            ]),
        ]);
    }

    private function timelineAttendanceEvents(Child $child, string $date): \Illuminate\Support\Collection
    {
        return $child->attendances()
            ->whereDate('date', $date)
            ->get()
            ->flatMap(function ($attendance) use ($date) {
                $events = collect();

                if ($attendance->check_in_at) {
                    $events->push([
                        'type'      => 'attendance',
                        'icon'      => '👋',
                        'title'     => 'Checked In',
                        'details'   => 'Status: ' . ucfirst($attendance->status ?? 'present'),
                        'timestamp' => Carbon::parse($attendance->check_in_at),
                    ]);
                }

                if ($attendance->check_out_at) {
                    $events->push([
                        'type'      => 'attendance',
                        'icon'      => '👋',
                        'title'     => 'Checked Out',
                        'details'   => 'Child checked out for the day.',
                        'timestamp' => Carbon::parse($attendance->check_out_at),
                    ]);
                }

                if (!$attendance->check_in_at && !$attendance->check_out_at) {
                    $events->push([
                        'type'      => 'attendance',
                        'icon'      => '👋',
                        'title'     => 'Attendance Recorded',
                        'details'   => 'Status: ' . ucfirst($attendance->status ?? 'recorded'),
                        'timestamp' => Carbon::parse($date . ' 09:00:00'),
                    ]);
                }

                return $events;
            });
    }

    private function timelineDailyUpdateEvents(Child $child, string $date): \Illuminate\Support\Collection
    {
        return $child->dailyUpdates()
            ->whereDate('date', $date)
            ->get()
            ->flatMap(function ($update) use ($date) {
                $events = collect();

                if (!empty($update->meals)) {
                    $events->push([
                        'type'      => 'meal',
                        'icon'      => '🍽️',
                        'title'     => 'Meal Update',
                        'details'   => $update->meals,
                        'timestamp' => Carbon::parse($date . ' 12:00:00'),
                    ]);
                }

                if (!empty($update->sleep)) {
                    $events->push([
                        'type'      => 'sleep',
                        'icon'      => '😴',
                        'title'     => 'Sleep Update',
                        'details'   => $update->sleep,
                        'timestamp' => Carbon::parse($date . ' 13:00:00'),
                    ]);
                }

                if (!empty($update->notes)) {
                    $events->push([
                        'type'      => 'note',
                        'icon'      => '📋',
                        'title'     => 'Daily Note',
                        'details'   => $update->notes,
                        'timestamp' => Carbon::parse($date . ' 15:00:00'),
                    ]);
                }

                return $events;
            });
    }

    private function timelineDailyReportEvents(Child $child, string $date): \Illuminate\Support\Collection
    {
        return $child->dailyReports()
            ->whereDate('date', $date)
            ->get()
            ->map(fn ($report) => [
                'type'      => 'report',
                'icon'      => '📝',
                'title'     => 'Daily Report',
                'details'   => $report->daily_report ?: 'Daily report submitted.',
                'timestamp' => $report->created_at
                    ? Carbon::parse($report->created_at)
                    : Carbon::parse($date . ' 16:00:00'),
            ]);
    }

    private function timelineMedicationEvents(Child $child, string $date): \Illuminate\Support\Collection
    {
        return $child->medicationLogs()
            ->whereDate('date', $date)
            ->get()
            ->map(fn ($medication) => [
                'type'      => 'medication',
                'icon'      => '💊',
                'title'     => 'Medication Given',
                'details'   => trim(
                    $medication->medication_name .
                    ($medication->dosage ? ' (' . $medication->dosage . ')' : '') .
                    ($medication->notes ? ' — ' . $medication->notes : '')
                ),
                'timestamp' => Carbon::parse($date . ' ' . ($medication->time_given ?: '11:00:00')),
            ]);
    }

    private function timelineIncidentEvents(Child $child, string $date): \Illuminate\Support\Collection
    {
        return $child->incidentReports()
            ->whereDate('incident_date', $date)
            ->get()
            ->map(fn ($incident) => [
                'type'      => 'incident',
                'icon'      => '🚨',
                'title'     => $incident->title ?: 'Incident Report',
                'details'   => trim(
                    ($incident->description ?: 'Incident recorded.') .
                    ($incident->action_taken ? ' Action taken: ' . $incident->action_taken : '')
                ),
                'timestamp' => Carbon::parse($date . ' ' . ($incident->incident_time ?: '14:00:00')),
                'severity'  => $incident->severity,
            ]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Submit payment proof for an invoice.
     * POST /api/parent/invoices/{invoice}/pay
     */
    public function submitPayment(Request $request, \App\Models\Invoice $invoice)
    {
        $parent = $request->user();
        abort_unless($invoice->parent_id === $parent->id, 403);
        abort_unless($invoice->canSubmitPayment(), 422, 'Payment cannot be submitted for this invoice.');

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');
        $invoice->markPaymentSubmitted($path, $request->payment_notes);

        return response()->json([
            'message' => 'Payment submitted successfully.',
            'invoice_id' => $invoice->id,
            'payment_status' => $invoice->payment_status,
            'payment_submitted_at' => $invoice->payment_submitted_at,
        ]);
    }

    private function authoriseChild(Request $request, Child $child): void
    {
        $linked = $request->user()->children()->where('children.id', $child->id)->exists();
        abort_unless($linked, 403);
    }

    private function childSummary(Child $child): array
    {
        return [
            'id'            => $child->id,
            'first_name'    => $child->first_name,
            'last_name'     => $child->last_name,
            'dob'           => $child->dob,
            'allergies'     => $child->allergies,
            'medical_notes' => $child->medical_notes,
            'room'          => $child->room,
        ];
    }
}
