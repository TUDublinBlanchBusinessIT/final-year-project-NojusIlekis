<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Room;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::orderBy('name')->get();

        $children = Child::with(['room', 'parents'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->room, function ($query, $room) {
                $query->where('room_id', $room);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return view('manager.children.index', compact('children', 'rooms'));
    }

    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        return view('manager.children.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:120'],
            'last_name'     => ['required', 'string', 'max:120'],
            'dob'           => ['nullable', 'date', 'before_or_equal:today'],
            'allergies'     => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'room_id'       => ['nullable', 'exists:rooms,id'],
        ]);

        $child = Child::create($validated);

        return redirect()
            ->route('manager.children.show', $child)
            ->with('success', 'Child added successfully.');
    }

    public function show(Child $child)
    {
        $child->load('room', 'parents');

        $recentAttendances = $child->attendances()
            ->with('room')
            ->latest('date')
            ->take(10)
            ->get();

        $recentDailyReports = $child->dailyReports()
            ->with('carer')
            ->latest('date')
            ->take(5)
            ->get();

        $recentMedicationLogs = $child->medicationLogs()
            ->with('carer')
            ->latest('date')
            ->take(5)
            ->get();

        return view('manager.children.show', compact(
            'child',
            'recentAttendances',
            'recentDailyReports',
            'recentMedicationLogs'
        ));
    }

    public function edit(Child $child)
    {
        $rooms = Room::orderBy('name')->get();
        return view('manager.children.edit', compact('child', 'rooms'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:120'],
            'last_name'     => ['required', 'string', 'max:120'],
            'dob'           => ['nullable', 'date', 'before_or_equal:today'],
            'allergies'     => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'room_id'       => ['nullable', 'exists:rooms,id'],
        ]);

        $child->update($validated);

        return redirect()
            ->route('manager.children.show', $child)
            ->with('success', 'Child updated successfully.');
    }

    public function destroy(Child $child)
    {
        $child->delete();

        return redirect()
            ->route('manager.children.index')
            ->with('success', 'Child deleted successfully.');
    }
}
