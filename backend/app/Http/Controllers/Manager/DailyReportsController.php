<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\Acknowledgement; // ✅ ADD

class DailyReportsController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id');

        $rooms = Room::orderBy('name')->get();
        $children = collect();

        if ($roomId) {
            // children in room
            $children = Child::where('room_id', $roomId)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            // daily reports for selected date, for children in this room
            $reports = DailyReport::with(['carer', 'mediaUpdates', 'acknowledgement'])
                ->where('date', $date)
                ->whereIn('child_id', $children->pluck('id'))
                ->get()
                ->keyBy('child_id');

            // attach report to each child
            $children->transform(function ($child) use ($reports) {
                $child->reportForDay = $reports->get($child->id);
                return $child;
            });
        }

        return view('manager.reports.daily-reports.index', compact('rooms', 'children', 'date', 'roomId'));
    }

    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['child.room', 'carer', 'mediaUpdates', 'acknowledgement']);

        return view('manager.reports.daily-reports.show', compact('dailyReport'));
    }

    // ✅ NEW: Manager requests acknowledgement from the parent
    public function requestAck(Request $request, DailyReport $dailyReport)
    {
        $dailyReport->load('child');

        $child = $dailyReport->child;
        $parent = $child ? $child->parents()->first() : null;

        if (!$parent) {
            return back()->with('error', 'No parent is linked to this child.');
        }

        Acknowledgement::updateOrCreate(
            [
                'record_type' => 'daily_report',
                'record_id'   => $dailyReport->id,
                'parent_id'   => $parent->id,
            ],
            [
                'status'         => 'pending',
                'signed_at'      => null,
                'signature_name' => null,
            ]
        );

        return back()->with('success', 'Acknowledgement request sent.');
    }
}