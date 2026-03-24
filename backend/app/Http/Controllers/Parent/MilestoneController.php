<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\ChildMilestone;

class MilestoneController extends Controller
{
    public function show(Child $child)
    {
        $parent = auth()->user();

        $isLinked = $child->parents()->where('users.id', $parent->id)->exists();
        abort_unless($isLinked, 403, 'This is not your child.');

        $ageRange = $child->age_range;

        $milestones = Milestone::where('age_range_months', $ageRange)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $achievedIds = $child->milestones()->pluck('milestones.id')->toArray();
        $achievedData = ChildMilestone::where('child_id', $child->id)
            ->with('observer')
            ->get()
            ->keyBy('milestone_id');

        $progress = [];
        foreach (Milestone::CATEGORIES as $key => $label) {
            $progress[$key] = $child->milestoneProgress($key);
        }

        $overallProgress = $child->milestoneProgress();

        return view('parent.milestones.show', compact(
            'child', 'milestones', 'achievedIds', 'achievedData',
            'ageRange', 'progress', 'overallProgress'
        ));
    }
}
