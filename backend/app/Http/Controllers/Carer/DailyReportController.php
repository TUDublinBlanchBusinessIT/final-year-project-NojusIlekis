<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\MediaUpdate;
use App\Models\Room;

class DailyReportController extends Controller
{
    public function index()
    {
        $carer = auth()->user();

        // Get room IDs assigned to this carer
        $roomIds = $carer->rooms()->pluck('rooms.id');

        // Get rooms and children in those rooms
        $rooms = Room::whereIn('id', $roomIds)->orderBy('name')->get();

        $children = Child::whereIn('room_id', $roomIds)
            ->orderBy('first_name')
            ->get();

        $allergyData = $children->mapWithKeys(fn($child) => [
            $child->id => [
                'name'          => $child->first_name . ' ' . $child->last_name,
                'has_allergies' => $child->hasAllergies(),
                'allergies'     => $child->allergyList(),
                'medical_notes' => $child->medical_notes ?? '',
            ]
        ]);

        return view('carer.daily_reports.index', compact('rooms', 'children', 'allergyData'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'daily_report' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:10240'
        ]);

        // Verify the child belongs to one of this carer's assigned rooms
        $child = Child::findOrFail($request->child_id);
        $carerRoomIds = auth()->user()->activeRooms()->pluck('rooms.id')->toArray();
        abort_unless(in_array($child->room_id, $carerRoomIds), 403, 'This child is not in your assigned rooms.');

        $today = now()->toDateString();

        // Prevent duplicate for same child + date
        $existing = DailyReport::where('child_id', $request->child_id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return back()->with('error', 'Report already exists for today.');
        }

        $report = DailyReport::create([
            'child_id' => $request->child_id,
            'carer_id' => auth()->id(),
            'date' => $today,
            'daily_report' => $request->daily_report,
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {

                $path = $file->store('daily_media', 'public');

                MediaUpdate::create([
                    'daily_report_id' => $report->id,
                    'file_path' => $path,
                    'type' => str_contains($file->getMimeType(), 'video') ? 'video' : 'image',
                    'uploaded_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Daily report saved successfully.');
    }
}