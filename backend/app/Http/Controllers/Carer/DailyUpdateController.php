<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Child;
use App\Models\DailyUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DailyUpdateController extends Controller
{
    public function index(Request $request)
    {
        $date   = $request->input('date', now()->toDateString());
        $roomId = $request->input('room_id');

        // Only show rooms the carer is actively assigned to
        $rooms = auth()->user()->activeRooms()->orderBy('name')->get(['rooms.id', 'rooms.name']);

        $children = collect();
        $existing = collect();

        if ($roomId) {
            // Ensure the selected room belongs to this carer
            abort_unless($rooms->pluck('id')->contains((int) $roomId), 403);

            $children = Child::query()
                ->with('room')
                ->where('room_id', $roomId)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            $existing = DailyUpdate::query()
                ->whereDate('date', $date)
                ->whereIn('child_id', $children->pluck('id'))
                ->get()
                ->keyBy('child_id');
        }

        return view('carer.daily_updates.index', compact('rooms', 'date', 'roomId', 'children', 'existing'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'    => ['required', 'date'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'meals'   => ['array'],
            'sleep'   => ['array'],
            'notes'   => ['array'],
        ]);

        $date   = $data['date'];
        $roomId = $data['room_id'];

        // Security: carer can only submit for rooms assigned to them
        $allowedRoomIds = auth()->user()->activeRooms()->pluck('rooms.id')->map(fn($id) => (int)$id);
        abort_unless($allowedRoomIds->contains((int) $roomId), 403);

        // Only save for children in that room
        $children = Child::where('room_id', $roomId)->get(['id']);

        foreach ($children as $child) {
            $meals = trim((string)($data['meals'][$child->id] ?? ''));
            $sleep = trim((string)($data['sleep'][$child->id] ?? ''));
            $notes = trim((string)($data['notes'][$child->id] ?? ''));

            $hasContent = ($meals !== '' || $sleep !== '' || $notes !== '');

            // If empty, remove existing record so manager sees "Missing"
            if (!$hasContent) {
                DailyUpdate::where('child_id', $child->id)
                    ->whereDate('date', $date)
                    ->delete();
                continue;
            }

            $payload = [
                'meals' => $meals ?: null,
                'sleep' => $sleep ?: null,
                'notes' => $notes ?: null,
            ];

            // Optional columns (only set if they exist)
            if (Schema::hasColumn('daily_updates', 'room_id')) {
                $payload['room_id'] = $roomId;
            }
            if (Schema::hasColumn('daily_updates', 'created_by')) {
                $payload['created_by'] = auth()->id();
            }

            DailyUpdate::updateOrCreate(
                ['child_id' => $child->id, 'date' => $date],
                $payload
            );
        }

        return redirect()
            ->route('carer.daily-updates.index', ['date' => $date, 'room_id' => $roomId])
            ->with('success', 'Daily updates saved.');
    }
}