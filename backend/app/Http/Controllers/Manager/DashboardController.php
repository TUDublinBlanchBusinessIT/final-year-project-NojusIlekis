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

        return view('dashboards.manager', [
            'kpi' => [
                'rangeLabel' => $start7 . ' to ' . $end,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ],
        ]);
    }
}