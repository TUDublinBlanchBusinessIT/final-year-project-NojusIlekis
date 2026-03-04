<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $end = now()->toDateString();
        $start7 = now()->subDays(6)->toDateString();

        // KPI summary (last 7 days)
        $summary = Attendance::query()
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->selectRaw("COUNT(*) as total_count")
            ->whereBetween('date', [$start7, $end])
            ->first();

        $present = (int) ($summary->present_count ?? 0);
        $absent  = (int) ($summary->absent_count ?? 0);
        $total   = (int) ($summary->total_count ?? 0);
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        // Daily trend (group by date)
        $rows = Attendance::query()
            ->selectRaw("date")
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->whereBetween('date', [$start7, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Build a full 7-day timeline (fill missing days with 0)
        $labels = [];
        $presentSeries = [];
        $absentSeries = [];

        $map = [];
        foreach ($rows as $r) {
            $map[$r->date] = [
                'present' => (int) $r->present_count,
                'absent' => (int) $r->absent_count,
            ];
        }

        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $labels[] = $d;
            $presentSeries[] = $map[$d]['present'] ?? 0;
            $absentSeries[] = $map[$d]['absent'] ?? 0;
        }

        return view('dashboards.manager', [
            'kpi' => [
                'rangeLabel' => $start7 . ' to ' . $end,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ],
            'attendanceChart' => [
                'labels' => $labels,
                'present' => $presentSeries,
                'absent' => $absentSeries,
            ],
        ]);
    }
}