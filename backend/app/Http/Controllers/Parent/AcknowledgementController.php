<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Acknowledgement;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class AcknowledgementController extends Controller
{
    public function dashboard()
    {
        $parent = auth()->user();

        $acknowledgements = Acknowledgement::where('parent_id', $parent->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $outstandingInvoices = Invoice::where('parent_id', $parent->id)
            ->where('status', 'sent')
            ->with('child')
            ->orderBy('due_date')
            ->get();

        $recentPaidInvoices = Invoice::where('parent_id', $parent->id)
            ->where('status', 'paid')
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $totalOutstanding = $outstandingInvoices->sum('total');
        $nextDueInvoice = $outstandingInvoices->sortBy('due_date')->first();

        $centreManager = User::where('role', 'manager')
            ->orderBy('id')
            ->first();

        $myChildren = $parent->children()->with('room')->get();

        return view('dashboards.parent', compact(
            'acknowledgements',
            'outstandingInvoices',
            'recentPaidInvoices',
            'totalOutstanding',
            'nextDueInvoice',
            'centreManager',
            'myChildren'
        ));
    }

    public function index(Request $request)
    {
        $parent = auth()->user();

        $acknowledgements = Acknowledgement::where('parent_id', $parent->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('parent.acknowledgements.index', compact('acknowledgements'));
    }

    public function sign(Request $request, Acknowledgement $acknowledgement)
    {
        $parent = auth()->user();

        if ($acknowledgement->parent_id !== $parent->id) {
            abort(403);
        }

        if ($acknowledgement->status === 'acknowledged') {
            return back()->with('error', 'This acknowledgement has already been signed.');
        }

        $validated = $request->validate([
            'signature_name' => ['required', 'string', 'max:255'],
            'confirm_acknowledgement' => ['accepted'],
        ]);

        $acknowledgement->update([
            'status' => 'acknowledged',
            'signed_at' => now(),
            'signature_name' => $validated['signature_name'],
        ]);

        return back()->with('success', 'Acknowledgement signed successfully.');
    }
}