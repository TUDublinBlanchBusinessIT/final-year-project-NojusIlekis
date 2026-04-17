<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\IncidentReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Defaults: last 7 days
        $defaultEnd = now()->toDateString();
        $defaultStart = now()->subDays(6)->toDateString();

        $start = $request->input('start_date', $defaultStart);
        $end = $request->input('end_date', $defaultEnd);
        $roomId = $request->input('room_id'); // null means "All rooms"

        // Basic validation (kept simple so it won't break if Rooms table differs)
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'room_id' => ['nullable', 'integer', 'min:1'],
        ]);

        // Normalise dates
        $start = \Carbon\Carbon::parse($start)->toDateString();
        $end = \Carbon\Carbon::parse($end)->toDateString();

        // Ensure start <= end
        if (\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::parse($end))) {
            [$start, $end] = [$end, $start];
        }

        // Cap range so charts don't get huge (31 days max)
        $days = \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end)) + 1;
        if ($days > 31) {
            $end = \Carbon\Carbon::parse($start)->addDays(30)->toDateString();
            $days = 31;
        }

        // Overall KPIs (all rooms)
        $overall = Attendance::query()
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->selectRaw("COUNT(*) as total_count")
            ->whereBetween('date', [$start, $end])
            ->first();

        $overallPresent = (int) ($overall->present_count ?? 0);
        $overallAbsent  = (int) ($overall->absent_count ?? 0);
        $overallTotal   = (int) ($overall->total_count ?? 0);
        $overallRate    = $overallTotal > 0 ? round(($overallPresent / $overallTotal) * 100, 1) : 0;

        // Per-room KPIs (table)
        $perRoomRows = Attendance::query()
            ->selectRaw("room_id")
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->selectRaw("COUNT(*) as total_count")
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('room_id')
            ->groupBy('room_id')
            ->orderBy('room_id')
            ->get();

        // Build room options for dropdown:
        // If you have a Room model, we’ll use names. Otherwise fallback to "Room #ID".
        $roomOptions = collect();
        $roomNames = [];

        if (class_exists(\App\Models\Room::class)) {
            $ids = $perRoomRows->pluck('room_id')->unique()->values();
            $roomNames = \App\Models\Room::query()
                ->whereIn('id', $ids)
                ->pluck('name', 'id')
                ->toArray();

            $roomOptions = \App\Models\Room::query()->orderBy('name')->get(['id', 'name']);
        } else {
            $roomOptions = $perRoomRows->pluck('room_id')->unique()->values()->map(fn($id) => (object)[
                'id' => (int)$id,
                'name' => 'Room #' . $id,
            ]);
        }

        $perRoom = $perRoomRows->map(function ($r) use ($roomNames) {
            $present = (int) $r->present_count;
            $absent = (int) $r->absent_count;
            $total = (int) $r->total_count;
            $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $name = $roomNames[$r->room_id] ?? ('Room #' . $r->room_id);

            return [
                'room_id' => (int) $r->room_id,
                'room_name' => $name,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ];
        });

        // Selected room KPIs (only if room filter picked)
        $roomKpi = null;
        if (!empty($roomId)) {
            $roomSummary = Attendance::query()
                ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
                ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
                ->selectRaw("COUNT(*) as total_count")
                ->whereBetween('date', [$start, $end])
                ->where('room_id', $roomId)
                ->first();

            $p = (int) ($roomSummary->present_count ?? 0);
            $a = (int) ($roomSummary->absent_count ?? 0);
            $t = (int) ($roomSummary->total_count ?? 0);
            $r = $t > 0 ? round(($p / $t) * 100, 1) : 0;

            $roomName = $roomNames[$roomId] ?? ('Room #' . $roomId);

            $roomKpi = [
                'room_id' => (int)$roomId,
                'room_name' => $roomName,
                'present' => $p,
                'absent' => $a,
                'rate' => $r,
            ];
        }

        // Trend chart (uses selected room if chosen, otherwise overall)
        $trendQuery = Attendance::query()
            ->selectRaw("date")
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->whereBetween('date', [$start, $end]);

        if (!empty($roomId)) {
            $trendQuery->where('room_id', $roomId);
        }

        $trendRows = $trendQuery
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $map = [];
        foreach ($trendRows as $r) {
            $map[$r->date->toDateString()] = [
                'present' => (int) $r->present_count,
                'absent' => (int) $r->absent_count,
            ];
        }

        $labels = [];
        $presentSeries = [];
        $absentSeries = [];

        for ($i = 0; $i < $days; $i++) {
            $d = \Carbon\Carbon::parse($start)->addDays($i)->toDateString();
            $labels[] = $d;
            $presentSeries[] = $map[$d]['present'] ?? 0;
            $absentSeries[] = $map[$d]['absent'] ?? 0;
        }

        $pendingPayments = \App\Models\Invoice::where('payment_status', 'payment_submitted')
            ->with(['child', 'parent'])
            ->latest('payment_submitted_at')
            ->get();

        $pendingPaymentCount = $pendingPayments->count();

        // Incident stats
        $totalIncidents    = IncidentReport::count();
        $openIncidents     = IncidentReport::where('status', 'open')->count();
        $reviewedIncidents = IncidentReport::where('status', 'reviewed')->count();
        $closedIncidents   = IncidentReport::where('status', 'closed')->count();

        // Incidents by severity (open/reviewed only)
        $highSeverity   = IncidentReport::where('severity', 'high')->where('status', '!=', 'closed')->count();
        $mediumSeverity = IncidentReport::where('severity', 'medium')->where('status', '!=', 'closed')->count();
        $lowSeverity    = IncidentReport::where('severity', 'low')->where('status', '!=', 'closed')->count();

        // Incidents trend — last 30 days grouped by date
        $incidentTrend = IncidentReport::where('incident_date', '>=', now()->subDays(30))
            ->selectRaw('incident_date, count(*) as count')
            ->groupBy('incident_date')
            ->orderBy('incident_date')
            ->pluck('count', 'incident_date')
            ->toArray();

        // Overdue incidents (open/reviewed for 10+ days), oldest first
        $overdueIncidents = IncidentReport::overdue()
            ->with(['child', 'carer'])
            ->orderBy('incident_date', 'asc')
            ->get();

        return view('dashboards.manager', [
            'filters' => [
                'start_date' => $start,
                'end_date' => $end,
                'room_id' => $roomId,
            ],
            'rooms' => $roomOptions,
            'kpiOverall' => [
                'rangeLabel' => $start . ' to ' . $end,
                'present' => $overallPresent,
                'absent' => $overallAbsent,
                'rate' => $overallRate,
            ],
            'kpiRoom' => $roomKpi,
            'kpiPerRoom' => $perRoom,
            'attendanceChart' => [
                'labels' => $labels,
                'present' => $presentSeries,
                'absent' => $absentSeries,
            ],
            'pendingPayments' => $pendingPayments,
            'pendingPaymentCount' => $pendingPaymentCount,
            'totalIncidents' => $totalIncidents,
            'openIncidents' => $openIncidents,
            'reviewedIncidents' => $reviewedIncidents,
            'closedIncidents' => $closedIncidents,
            'highSeverity' => $highSeverity,
            'mediumSeverity' => $mediumSeverity,
            'lowSeverity' => $lowSeverity,
            'incidentTrend' => $incidentTrend,
            'overdueIncidents' => $overdueIncidents,
        ]);
    }
}