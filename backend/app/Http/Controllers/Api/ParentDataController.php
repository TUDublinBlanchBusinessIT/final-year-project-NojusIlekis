<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    // Helpers
    // -----------------------------------------------------------------------

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
