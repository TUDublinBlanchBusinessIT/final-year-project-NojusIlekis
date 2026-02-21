<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index()
{
    $children = Child::all(); // later filter by carer

    return view('carer.daily-updates.index', compact('children'));
}

    public function store(Request $request)
{
    $request->validate([
        'child_id' => 'required',
        'daily_report' => 'required',
    ]);

    $report = DailyReport::create([
        'child_id' => $request->child_id,
        'carer_id' => auth()->id(),
        'date' => now()->toDateString(),
        'daily_report' => $request->daily_report,
    ]);

    return redirect()->back()->with('success', 'Daily report saved.');
}
}
