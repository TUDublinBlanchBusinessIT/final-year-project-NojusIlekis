<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreRoomRequest;
use App\Http\Requests\Manager\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::withCount('children')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('manager.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('manager.rooms.create');
    }

    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        return redirect()
            ->route('manager.rooms.show', $room)
            ->with('success', 'Room added successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['children', 'activeCarers']);

        return view('manager.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('manager.rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()
            ->route('manager.rooms.show', $room)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->children()->count() > 0) {
            return redirect()
                ->route('manager.rooms.show', $room)
                ->with('error', __('manager.rooms_cannot_delete_has_children'));
        }

        $room->users()->detach();

        $room->delete();

        return redirect()
            ->route('manager.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
