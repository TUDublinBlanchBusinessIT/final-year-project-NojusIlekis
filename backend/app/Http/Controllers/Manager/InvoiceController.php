<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

        $invoice = Invoice::create([
            'child_id' => $child->id,
            'parent_id' => $child->parent_user_id,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'due_date' => $validated['due_date'],
            'total' => 0,
            'status' => 'draft',
        ]);

        return redirect()->route('manager.invoices.items.create', $invoice)
            ->with('success', 'Invoice created successfully as draft. You can now add line items.');
    }

    public function createItem(Invoice $invoice)
    {
        $invoice->load(['child', 'parent', 'items']);

        return view('manager.reports.invoices.items.create', compact('invoice'));
    }

    public function storeItem(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $lineTotal = $validated['qty'] * $validated['unit_price'];

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $validated['description'],
            'qty' => $validated['qty'],
            'unit_price' => $validated['unit_price'],
            'total' => $lineTotal,
        ]);

        $invoice->update([
            'total' => $invoice->items()->sum('total'),
        ]);

        return redirect()->route('manager.invoices.items.create', $invoice)
            ->with('success', 'Invoice item added successfully.');
    }
}