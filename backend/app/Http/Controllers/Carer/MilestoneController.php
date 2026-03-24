<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\ChildMilestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * List children in the carer's active rooms so they can pick one.
     */
    public function index()
    {
        $children = auth()->user()
            ->activeRooms()
            ->with('children')
            ->get()
            ->flatMap(fn ($room) => $room->children)
            ->unique('id')
            ->sortBy('first_name');

        return view('carer.milestones.index', compact('children'));
    }

    /**
     * Show milestones for a child, grouped by Aistear theme.
     */
    public function show(Child $child)
    {
        $ageRange = $child->age_range;
        $categoryFilter = request('category');

        $query = Milestone::where('age_range_months', $ageRange)->orderBy('sort_order');
        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }
        $milestones = $query->get()->groupBy('category');

        $achievedIds = $child->milestones()->pluck('milestones.id')->toArray();

        $achievedData = ChildMilestone::where('child_id', $child->id)
            ->with('observer')
            ->get()
            ->keyBy('milestone_id');

        $progress = [];
        foreach (Milestone::CATEGORIES as $key => $label) {
            $progress[$key] = $child->milestoneProgress($key);
        }

        return view('carer.milestones.show', compact(
            'child', 'milestones', 'achievedIds', 'achievedData',
            'ageRange', 'progress', 'categoryFilter'
        ));
    }

    /**
     * Toggle a milestone as observed for a child.
     */
    public function toggle(Request $request, Child $child, Milestone $milestone)
    {
        $existing = ChildMilestone::where('child_id', $child->id)
            ->where('milestone_id', $milestone->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Milestone removed.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        ChildMilestone::create([
            'child_id'    => $child->id,
            'milestone_id' => $milestone->id,
            'observed_by' => auth()->id(),
            'observed_at' => now()->toDateString(),
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'Milestone observed: ' . $milestone->name);
    }
}
