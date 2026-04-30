<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCarerRequest;
use App\Http\Requests\Manager\UpdateCarerRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CarerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

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
        $this->authorize('create', User::class);

        $rooms = Room::orderBy('name')->get();
        return view('manager.carers.create', compact('rooms'));
    }

    public function store(StoreCarerRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $carerUser = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'carer',
        ]);

        if (!empty($validated['room_id'])) {
            $this->assignRoom($carerUser, (int) $validated['room_id']);
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

        $this->authorize('view', $carerUser);

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

        $this->authorize('update', $carerUser);

        $rooms = Room::orderBy('name')->get();

        return view('manager.carers.edit', compact('carerUser', 'rooms'));
    }

    public function update(UpdateCarerRequest $request, string $id)
    {
        $carerUser = User::where('role', 'carer')
            ->with('activeRooms')
            ->findOrFail($id);

        $this->authorize('update', $carerUser);

        $validated = $request->validated();

        $carerUser->name  = $validated['name'];
        $carerUser->email = $validated['email'];

        if (!empty($validated['password'])) {
            $carerUser->password = Hash::make($validated['password']);
        }

        $carerUser->save();

        // Handle room reassignment
        $this->assignRoom($carerUser, $validated['room_id'] ?? null);

        return redirect()
            ->route('manager.carers.show', $carerUser)
            ->with('success', 'Carer updated successfully.');
    }

    /**
     * Re-assign a carer to a single active room (or to none).
     *
     * Closes any active assignments to OTHER rooms first. For the target room
     * the helper handles three states without ever inserting a duplicate of
     * the (room_id, user_id, start_date) unique key:
     *   - already actively assigned → no-op
     *   - a same-day row exists but is closed (end_date set) → reopen it
     *   - no same-day row → insert fresh
     */
    private function assignRoom(User $carer, ?int $roomId): void
    {
        $today = now()->toDateString();

        if (! $roomId) {
            // No target room — close every active assignment.
            DB::table('room_user')
                ->where('user_id', $carer->id)
                ->whereNull('end_date')
                ->update(['end_date' => $today]);
            return;
        }

        // Close active assignments to OTHER rooms only (leave the target room alone).
        DB::table('room_user')
            ->where('user_id', $carer->id)
            ->whereNull('end_date')
            ->where('room_id', '!=', $roomId)
            ->update(['end_date' => $today]);

        // If already actively assigned to the target room, nothing to do.
        $alreadyAssigned = DB::table('room_user')
            ->where('user_id', $carer->id)
            ->where('room_id', $roomId)
            ->whereNull('end_date')
            ->exists();

        if ($alreadyAssigned) {
            return;
        }

        // If there's a closed-today row for the target room, reopen it instead
        // of inserting a duplicate of the same composite key.
        $closedTodayRow = DB::table('room_user')
            ->where('user_id', $carer->id)
            ->where('room_id', $roomId)
            ->where('start_date', $today)
            ->whereNotNull('end_date')
            ->exists();

        if ($closedTodayRow) {
            DB::table('room_user')
                ->where('user_id', $carer->id)
                ->where('room_id', $roomId)
                ->where('start_date', $today)
                ->update([
                    'end_date'   => null,
                    'is_primary' => true,
                    'updated_at' => now(),
                ]);
            return;
        }

        // No existing same-day row — insert fresh.
        $carer->rooms()->attach($roomId, [
            'start_date' => $today,
            'end_date'   => null,
            'is_primary' => true,
        ]);
    }

    public function destroy(string $id)
    {
        $carerUser = User::where('role', 'carer')->findOrFail($id);

        $this->authorize('delete', $carerUser);

        $carerUser->delete();

        return redirect()
            ->route('manager.carers.index')
            ->with('success', 'Carer deleted successfully.');
    }
}
