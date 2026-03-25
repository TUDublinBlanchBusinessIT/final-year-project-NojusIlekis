<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use App\Models\Acknowledgement;
use Illuminate\Http\Request;

class ParentIncidentController extends Controller
{
    // LIST incidents for parent's children
    public function index(Request $request)
    {
        $childrenIds = $request->user()->children->pluck('id');

        $incidents = IncidentReport::with(['child', 'carer', 'acknowledgement'])
            ->whereIn('child_id', $childrenIds)
            ->latest('incident_date')
            ->get();

        return view('parent.incidents.index', compact('incidents'));
    }

    // SHOW single incident
    public function show(Request $request, IncidentReport $incident)
    {
        // SECURITY CHECK (VERY IMPORTANT)
        abort_unless(
            $request->user()->children->contains($incident->child_id),
            403
        );

        $incident->load(['child', 'carer', 'acknowledgement']);

        return view('parent.incidents.show', compact('incident'));
    }

    // SIGN incident
    public function sign(Request $request, IncidentReport $incident)
    {
        abort_unless(
            $request->user()->children->contains($incident->child_id),
            403
        );

        $request->validate([
            'signature_name' => 'required|string|max:255',
        ]);

        $ack = Acknowledgement::firstOrCreate([
            'record_type' => 'incident_report',
            'record_id' => $incident->id,
            'parent_id' => auth()->id(),
        ]);

        $ack->update([
            'status' => 'acknowledged',
            'signed_at' => now(),
            'signature_name' => $request->signature_name,
        ]);

        return back()->with('success', 'Incident acknowledged successfully.');
    }
}