<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CarerController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::orderBy('name')->get();

        $carers = User::where('role', 'carer')
            ->with('activeRooms')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->room, function ($query, $roomId) {
                $query->whereHas('activeRooms', fn($q) => $q->where('rooms.id', $roomId));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('manager.carers.index', compact('carers', 'rooms'));
    }

    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        return view('manager.carers.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'room_id'  => ['nullable', 'exists:rooms,id'],
        ]);

        $carerUser = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'carer',
        ]);

        if (!empty($validated['room_id'])) {
            $carerUser->rooms()->attach($validated['room_id'], [
                'start_date' => now()->toDateString(),
                'end_date'   => null,
                'is_primary' => true,
            ]);
        }

        return redirect()
            ->route('manager.carers.show', $carerUser)
            ->with('success', 'Carer added successfully.');
    }

    public function show(string $id)
    {
        $carerUser = User::where('role', 'carer')
            ->with(['rooms' => fn($q) => $q->orderBy('room_user.start_date', 'desc')])
            ->findOrFail($id);

        $dailyReportsCount    = $carerUser->dailyReports()->count();
        $attendanceCount      = $carerUser->attendancesRecorded()->count();
        $medicationLogsCount  = $carerUser->medicationLogs()->count();

        return view('manager.carers.show', compact(
            'carerUser',
            'dailyReportsCount',
            'attendanceCount',
            'medicationLogsCount'
        ));
    }

    public function edit(string $id)
    {
        $carerUser = User::where('role', 'carer')
            ->with('activeRooms')
            ->findOrFail($id);

        $rooms = Room::orderBy('name')->get();

        return view('manager.carers.edit', compact('carerUser', 'rooms'));
    }

    public function update(Request $request, string $id)
    {
        $carerUser = User::where('role', 'carer')
            ->with('activeRooms')
            ->findOrFail($id);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', "unique:users,email,{$id}"],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'room_id'  => ['nullable', 'exists:rooms,id'],
        ]);

        $carerUser->name  = $validated['name'];
        $carerUser->email = $validated['email'];

        if (!empty($validated['password'])) {
            $carerUser->password = Hash::make($validated['password']);
        }

        $carerUser->save();

        // Handle room reassignment
        $newRoomId      = $validated['room_id'] ?? null;
        $currentActive  = $carerUser->activeRooms->first();
        $currentRoomId  = $currentActive?->id;

        if ((string) $newRoomId !== (string) $currentRoomId) {
            if ($currentActive) {
                $carerUser->rooms()->updateExistingPivot($currentActive->id, [
                    'end_date' => now()->toDateString(),
                ]);
            }
            if ($newRoomId) {
                $carerUser->rooms()->attach($newRoomId, [
                    'start_date' => now()->toDateString(),
                    'end_date'   => null,
                    'is_primary' => true,
                ]);
            }
        }

        return redirect()
            ->route('manager.carers.show', $carerUser)
            ->with('success', 'Carer updated successfully.');
    }

    public function destroy(string $id)
    {
        $carerUser = User::where('role', 'carer')->findOrFail($id);
        $carerUser->delete();

        return redirect()
            ->route('manager.carers.index')
            ->with('success', 'Carer deleted successfully.');
    }
}
