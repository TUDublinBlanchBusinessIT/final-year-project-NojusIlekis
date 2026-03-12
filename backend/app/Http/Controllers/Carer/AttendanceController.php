<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Room;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date   = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id');

        // Carer can only pick rooms assigned to them
        $rooms = Room::query()
            ->whereIn('id', auth()->user()->rooms()->pluck('rooms.id'))
            ->orderBy('name')
            ->get();

        $children = collect();
        $existing = collect();

        if ($roomId) {
            // Ensure chosen room is one of the carer's rooms
            abort_unless($rooms->pluck('id')->contains((int) $roomId), 403);

            $children = Child::where('room_id', $roomId)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            // Load existing attendance for this room + date
            $existing = Attendance::whereIn('child_id', $children->pluck('id'))
                ->where('date', $date)
                ->where('room_id', $roomId)
                ->get()
                ->keyBy('child_id');
        }

        return view('carer.attendance.index', compact('rooms', 'children', 'existing', 'date', 'roomId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['in:present,absent'],
        ]);

        // Security: carer can only submit for rooms assigned to them
        $allowedRoomIds = auth()->user()->rooms()->pluck('rooms.id')->map(fn ($id) => (int) $id);
        abort_unless($allowedRoomIds->contains((int) $data['room_id']), 403);

        // Only allow marking children that belong to this room
        $childIds = Child::where('room_id', $data['room_id'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        foreach ($data['attendance'] as $childId => $status) {
            $childId = (int) $childId;

            if (! $childIds->contains($childId)) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'child_id' => $childId,
                    'date'     => $data['date'],
                ],
                [
                    'room_id'     => $data['room_id'],
                    'status'      => $status,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->route('carer.attendance.index', ['room_id' => $data['room_id'], 'date' => $data['date']])
            ->with('success', 'Attendance saved.');
    }
}