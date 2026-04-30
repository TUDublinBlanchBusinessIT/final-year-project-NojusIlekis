<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\IncidentReport;

class IncidentReportController extends Controller
{
    public function index()
    {
        $carer = auth()->user();

        $roomIds = $carer->activeRooms()->pluck('rooms.id');

        $children = Child::whereIn('room_id', $roomIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $recentIncidents = IncidentReport::with(['child', 'room'])
            ->where('carer_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return view('carer.incident-reports.index', compact('children', 'recentIncidents', 'roomIds'));
    }

    public function store(Request $request)
    {
        $carer = auth()->user();

        $roomIds = $carer->activeRooms()->pluck('rooms.id');

        $request->validate([
            'child_id' => 'required|exists:children,id',
            'room_id' => 'required|exists:rooms,id',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'action_taken' => 'nullable|string',
            'severity' => 'required|in:low,medium,high',
            'parent_contact_required' => 'nullable|boolean',
        ]);

        if (! $roomIds->contains((int) $request->room_id)) {
            return back()
                ->with('error', 'You are not allowed to create incident reports for this room.')
                ->withInput();
        }

        $child = Child::where('id', $request->child_id)
            ->whereIn('room_id', $roomIds)
            ->first();

        if (! $child) {
            return back()
                ->with('error', 'You are not allowed to create incident reports for this child.')
                ->withInput();
        }

        if ((int) $child->room_id !== (int) $request->room_id) {
            return back()
                ->with('error', 'Selected child does not belong to the selected room.')
                ->withInput();
        }

        IncidentReport::create([
            'child_id' => $request->child_id,
            'carer_id' => auth()->id(),
            'room_id' => $request->room_id,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'title' => $request->title,
            'description' => $request->description,
            'action_taken' => $request->action_taken,
            'severity' => $request->severity,
            'parent_contact_required' => $request->boolean('parent_contact_required'),
            'status' => 'open',
        ]);

        return back()->with('success', 'Incident report submitted successfully.');
    }
}