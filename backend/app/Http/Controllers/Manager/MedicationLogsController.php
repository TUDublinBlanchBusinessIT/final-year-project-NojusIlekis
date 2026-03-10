<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Child;
use App\Models\MedicationLog;

class MedicationLogsController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id');

        $rooms = Room::orderBy('name')->get();

        $logs = MedicationLog::with(['child.room', 'carer'])
            ->when($date, fn ($query) => $query->whereDate('date', $date))
            ->when($roomId, function ($query) use ($roomId) {
                $query->whereHas('child', function ($childQuery) use ($roomId) {
                    $childQuery->where('room_id', $roomId);
                });
            })
            ->latest('date')
            ->latest('time_given')
            ->get();

        return view('manager.reports.medication.index', compact('rooms', 'logs', 'date', 'roomId'));
    }
}