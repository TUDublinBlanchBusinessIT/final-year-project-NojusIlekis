<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['child', 'items'])
            ->where('parent_id', auth()->id())
            ->latest()
            ->get();

        return view('parent.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        abort_unless($invoice->parent_id === auth()->id(), 403);

        $invoice->load(['child', 'parent', 'items']);

        $subtotal = $invoice->items->sum('total');
        $finalTotal = $invoice->total;

        return view('parent.invoices.show', compact('invoice', 'subtotal', 'finalTotal'));
    }

    public function submitPayment(Request $request, Invoice $invoice)
    {
        $parent = auth()->user();

        abort_unless($invoice->parent_id === $parent->id, 403);
        abort_unless($invoice->canSubmitPayment(), 422, 'Payment cannot be submitted for this invoice.');

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $invoice->markPaymentSubmitted($path, $request->payment_notes);

        return redirect()->route('parent.invoices.show', $invoice)
            ->with('success', 'Payment submitted successfully. The manager will review and confirm your payment.');
    }

    public function print(Invoice $invoice)
    {
        abort_unless($invoice->parent_id === auth()->id(), 403);

        $invoice->load(['child', 'parent', 'items']);

        $subtotal = $invoice->items->sum('total');
        $finalTotal = $invoice->total;

        return view('manager.reports.invoices.print', compact('invoice', 'subtotal', 'finalTotal'));
    }
}