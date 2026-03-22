<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Message;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['child', 'parent'])
            ->latest()
            ->get();

        return view('manager.reports.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['child', 'parent', 'items']);

        $subtotal = $invoice->items->sum('total');
        $finalTotal = $invoice->total;

        return view('manager.reports.invoices.show', compact('invoice', 'subtotal', 'finalTotal'));
    }

    public function create()
    {
        $children = Child::with('parents')->orderBy('first_name')->get();

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

        $child = Child::with('parents')->findOrFail($validated['child_id']);

        $parent = $child->parents->first();

        if (!$parent) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This child does not have a linked parent.');
        }

        $invoice = Invoice::create([
            'child_id' => $child->id,
            'parent_id' => $parent->id,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'due_date' => $validated['due_date'],
            'total' => 0,
            'discount' => 0,
            'status' => 'draft',
        ]);

        return redirect()->route('manager.invoices.items.create', $invoice)
            ->with('success', 'Invoice created successfully as draft. You can now add line items.');
    }

    public function createItem(Invoice $invoice)
    {
        $invoice->load(['child', 'parent', 'items']);

        $subtotal = $invoice->items->sum('total');
        $finalTotal = max(0, $subtotal - $invoice->discount);

        return view('manager.reports.invoices.items.create', compact('invoice', 'subtotal', 'finalTotal'));
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

        $subtotal = $invoice->items()->sum('total');
        $finalTotal = max(0, $subtotal - $invoice->discount);

        $invoice->update([
            'total' => $finalTotal,
        ]);

        return redirect()->route('manager.invoices.items.create', $invoice)
            ->with('success', 'Invoice item added successfully.');
    }

    public function updateDiscount(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'discount' => ['required', 'numeric', 'min:0'],
        ]);

        $subtotal = $invoice->items()->sum('total');
        $discount = min($validated['discount'], $subtotal);
        $finalTotal = max(0, $subtotal - $discount);

        $invoice->update([
            'discount' => $discount,
            'total' => $finalTotal,
        ]);

        return redirect()->route('manager.invoices.items.create', $invoice)
            ->with('success', 'Discount updated successfully.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:sent,paid'],
        ]);

        $oldStatus = $invoice->status;
        $newStatus = $validated['status'];

        $invoice->load(['child', 'parent']);

        $invoice->update([
            'status' => $newStatus,
        ]);

        if ($newStatus === 'sent' && $oldStatus !== 'sent') {
            $childName = trim(($invoice->child->first_name ?? '') . ' ' . ($invoice->child->last_name ?? ''));

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $invoice->parent_id,
                'child_id' => $invoice->child_id,
                'body' => "A new invoice has been issued for {$childName}. Total: €" . number_format($invoice->total, 2) . ". Due date: " . $invoice->due_date . ". Please review it in your parent portal.",
            ]);
        }

        return redirect()
            ->route('manager.invoices.show', $invoice)
            ->with('success', 'Invoice status updated.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['child', 'parent', 'items']);

        $subtotal = $invoice->items->sum('total');
        $finalTotal = $invoice->total;

        return view('manager.reports.invoices.print', compact(
            'invoice',
            'subtotal',
            'finalTotal'
        ));
    }
}