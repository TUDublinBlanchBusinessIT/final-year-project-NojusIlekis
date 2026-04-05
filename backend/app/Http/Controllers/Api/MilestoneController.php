<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\ChildMilestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * Get all milestones for a child's age range with achievement status.
     */
    public function index(Child $child)
    {
        $ageRange = $child->age_range;

        $milestones = Milestone::where('age_range_months', $ageRange)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $achievedIds = $child->milestones()->pluck('milestones.id')->toArray();
        $achievedData = ChildMilestone::where('child_id', $child->id)
            ->with('observer:id,name')
            ->get()
            ->keyBy('milestone_id');

        $grouped = $milestones->groupBy('category')->map(function ($items) use ($achievedIds, $achievedData) {
            return $items->map(function ($m) use ($achievedIds, $achievedData) {
                $achieved = in_array($m->id, $achievedIds);
                $data = $achievedData[$m->id] ?? null;
                return [
                    'id'          => $m->id,
                    'name'        => $m->name,
                    'description' => $m->description,
                    'achieved'    => $achieved,
                    'observed_at' => $data?->observed_at?->toDateString(),
                    'observed_by' => $data?->observer?->name,
                    'notes'       => $data?->notes,
                ];
            });
        });

        $progress = [];
        foreach (Milestone::CATEGORIES as $key => $label) {
            $progress[$key] = $child->milestoneProgress($key);
        }

        return response()->json([
            'child_id'        => $child->id,
            'child_name'      => $child->first_name . ' ' . $child->last_name,
            'age_months'      => $child->age_in_months,
            'age_range'       => $ageRange,
            'progress'        => $progress,
            'overall_progress' => $child->milestoneProgress(),
            'milestones'      => $grouped,
        ]);
    }

    /**
     * Toggle a milestone observation (carer only).
     */
    public function toggle(Request $request, Child $child, Milestone $milestone)
    {
        $user = $request->user();
        if ($user->role === 'parent') {
            return response()->json(['message' => 'Parents cannot modify milestones'], 403);
        }

        $existing = ChildMilestone::where('child_id', $child->id)
            ->where('milestone_id', $milestone->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Milestone observation removed', 'achieved' => false]);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $record = ChildMilestone::create([
            'child_id'     => $child->id,
            'milestone_id' => $milestone->id,
            'observed_by'  => $user->id,
            'observed_at'  => now()->toDateString(),
            'notes'        => $request->notes,
        ]);

        return response()->json([
            'message'     => 'Milestone observed: ' . $milestone->name,
            'achieved'    => true,
            'observed_at' => $record->observed_at->toDateString(),
            'observed_by' => $user->name,
        ], 201);
    }
}
