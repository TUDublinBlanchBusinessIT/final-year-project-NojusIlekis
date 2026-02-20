<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\DailyUpdate;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function attendance(Request $request)
    {
        $date   = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id'); // nullable

        $rooms = Room::orderBy('name')->get(['id', 'name']);

        $children = Child::query()
            ->with('room')
            ->when($roomId, fn ($q) => $q->where('room_id', $roomId))
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $childIds = $children->pluck('id');

        // existing attendance rows keyed by child_id
        $existing = Attendance::query()
            ->whereDate('date', $date)
            ->when($roomId, fn ($q) => $q->where('room_id', $roomId))
            ->whereIn('child_id', $childIds)
            ->get()
            ->keyBy('child_id');

        $childrenCount = $children->count();
        $present = $existing->where('status', 'present')->count();
        $absent  = $existing->where('status', 'absent')->count();
        $notMarked = max(0, $childrenCount - $existing->count());

        return view('manager.reports.attendance', compact(
            'rooms', 'date', 'roomId',
            'children', 'existing',
            'childrenCount', 'present', 'absent', 'notMarked'
        ));
    }

    public function tasks(Request $request)
    {
        $date   = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id');

        $rooms = Room::orderBy('name')->get(['id', 'name']);

        $children = Child::query()
            ->with('room')
            ->when($roomId, fn ($q) => $q->where('room_id', $roomId))
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $childIds = $children->pluck('id');

        // daily updates keyed by child_id
        $updates = DailyUpdate::query()
            ->whereDate('date', $date)
            ->whereIn('child_id', $childIds)
            ->get()
            ->keyBy('child_id');

        $childrenCount = $children->count();
        $updatesDone = $updates->count();
        $updatesMissing = max(0, $childrenCount - $updatesDone);

        // “task completeness” totals
        $mealsDone = $updates->filter(fn($u) => filled($u->meals))->count();
        $sleepDone = $updates->filter(fn($u) => filled($u->sleep))->count();
        $notesDone = $updates->filter(fn($u) => filled($u->notes))->count();

        return view('manager.reports.tasks', compact(
            'rooms', 'date', 'roomId',
            'children', 'updates',
            'childrenCount', 'updatesDone', 'updatesMissing',
            'mealsDone', 'sleepDone', 'notesDone'
        ));
    }
}