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
}