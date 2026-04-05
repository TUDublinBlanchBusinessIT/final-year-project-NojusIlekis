<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyUpdate;
use App\Models\MedicationLog;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarerDataController extends Controller
{
    // -----------------------------------------------------------------------
    // Rooms
    // -----------------------------------------------------------------------

    public function rooms(Request $request): JsonResponse
    {
        $rooms = $request->user()
            ->activeRooms()
            ->with('children')
            ->get();

        return response()->json($rooms);
    }

    public function roomChildren(Request $request, Room $room): JsonResponse
    {
        $this->authoriseRoom($request, $room);

        $today = now()->toDateString();

        $children = $room->children()
            ->with(['attendances' => fn ($q) => $q->where('date', $today)->where('room_id', $room->id)])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($child) {
                return [
                    'id'             => $child->id,
                    'first_name'     => $child->first_name,
                    'last_name'      => $child->last_name,
                    'dob'            => $child->dob,
                    'allergies'      => $child->allergies,
                    'medical_notes'  => $child->medical_notes,
                    'attendance_today' => $child->attendances->first()?->only('id', 'status', 'check_in_at', 'check_out_at'),
                ];
            });

        return response()->json($children);
    }

    // -----------------------------------------------------------------------
    // Attendance
    // -----------------------------------------------------------------------

    public function storeAttendance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'date'     => ['required', 'date'],
            'status'   => ['required', 'in:present,absent'],
            'room_id'  => ['required', 'integer', 'exists:rooms,id'],
        ]);

        $room = Room::findOrFail($data['room_id']);
        $this->authoriseRoom($request, $room);

        $attendance = Attendance::updateOrCreate(
            ['child_id' => $data['child_id'], 'date' => $data['date']],
            [
                'room_id'     => $data['room_id'],
                'status'      => $data['status'],
                'recorded_by' => $request->user()->id,
            ]
        );

        return response()->json($attendance, 201);
    }

    // -----------------------------------------------------------------------
    // Daily Updates
    // -----------------------------------------------------------------------

    public function storeDailyUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'date'     => ['required', 'date'],
            'meals'    => ['nullable', 'string'],
            'sleep'    => ['nullable', 'string'],
            'notes'    => ['nullable', 'string'],
        ]);

        $update = DailyUpdate::updateOrCreate(
            ['child_id' => $data['child_id'], 'date' => $data['date']],
            [
                'meals'      => $data['meals'] ?? null,
                'sleep'      => $data['sleep'] ?? null,
                'notes'      => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]
        );

        return response()->json($update, 201);
    }

    // -----------------------------------------------------------------------
    // Medication Logs
    // -----------------------------------------------------------------------

    public function storeMedicationLog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id'        => ['required', 'integer', 'exists:children,id'],
            'medication_name' => ['required', 'string', 'max:255'],
            'dosage'          => ['required', 'string', 'max:255'],
            'date'            => ['required', 'date'],
            'time_given'      => ['required'],
            'notes'           => ['nullable', 'string'],
        ]);

        $log = MedicationLog::create([
            'child_id'        => $data['child_id'],
            'carer_id'        => $request->user()->id,
            'medication_name' => $data['medication_name'],
            'dosage'          => $data['dosage'],
            'date'            => $data['date'],
            'time_given'      => $data['time_given'],
            'notes'           => $data['notes'] ?? null,
        ]);

        return response()->json($log, 201);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function authoriseRoom(Request $request, Room $room): void
    {
        $assigned = $request->user()->activeRooms()->where('rooms.id', $room->id)->exists();
        abort_unless($assigned, 403);
    }
}
