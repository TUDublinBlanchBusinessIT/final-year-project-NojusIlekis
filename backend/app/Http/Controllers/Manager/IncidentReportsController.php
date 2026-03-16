<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncidentReport;

class IncidentReportsController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date;

        $reports = IncidentReport::with(['child','carer','room'])
            ->when($date, function ($query) use ($date) {
                $query->whereDate('incident_date', $date);
            })
            ->latest()
            ->get();

        return view('manager.reports.incident-reports.index', compact('reports','date'));
    }

    public function updateStatus(Request $request, IncidentReport $incident)
    {
        $request->validate([
            'status' => 'required|in:open,reviewed,closed',
        ]);

        $incident->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Incident status updated.');
    }
}