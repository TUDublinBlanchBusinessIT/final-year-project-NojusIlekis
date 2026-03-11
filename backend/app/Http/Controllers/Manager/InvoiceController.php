<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function create()
    {
        $children = Child::with('parent')->orderBy('first_name')->get();

        return view('manager.reports.invoices.create', compact('children'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'due_date' => ['required', 'date'],
        ]);

        $child = Child::with('parent')->findOrFail($validated['child_id']);

        Invoice::create([
            'child_id' => $child->id,
            'parent_id' => $child->parent_user_id,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'due_date' => $validated['due_date'],
            'total' => 0,
            'status' => 'draft',
        ]);

        return redirect()->route('manager.invoices.create')
            ->with('success', 'Invoice created successfully as draft.');
    }
}