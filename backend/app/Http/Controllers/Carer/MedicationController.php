<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\MedicationLog;

class MedicationController extends Controller
{
    public function index()
    {
        $carer = auth()->user();

        $roomIds = $carer->activeRooms()->pluck('rooms.id');

        $children = Child::whereIn('room_id', $roomIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $recentLogs = MedicationLog::with(['child', 'carer'])
            ->where('carer_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $allergyData = $children->mapWithKeys(fn($child) => [
            $child->id => [
                'name'             => $child->first_name . ' ' . $child->last_name,
                'has_allergies'    => $child->hasAllergies(),
                'allergies'        => $child->allergyList(),
                'has_medical_needs' => $child->hasMedicalNeeds(),
                'medical_notes'    => $child->medicalNeedsSummary(),
            ]
        ]);

        return view('carer.medication.index', compact('children', 'recentLogs', 'allergyData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'date' => 'required|date',
            'time_given' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Verify the child is in one of the carer's active rooms
        $child = Child::findOrFail($request->child_id);
        $carerRoomIds = auth()->user()->activeRooms()->pluck('rooms.id')->toArray();
        abort_unless(in_array($child->room_id, $carerRoomIds), 403, 'This child is not in your assigned rooms.');

        MedicationLog::create([
            'child_id' => $request->child_id,
            'carer_id' => auth()->id(),
            'medication_name' => $request->medication_name,
            'dosage' => $request->dosage,
            'date' => $request->date,
            'time_given' => $request->time_given,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Medication logged successfully.');
    }
}