<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerDataController extends Controller
{
    public function children(Request $request): JsonResponse
    {
        $children = Child::with(['room', 'parents'])
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->query('room_id'), fn ($q, $roomId) => $q->where('room_id', $roomId))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return response()->json($children);
    }

    public function parents(Request $request): JsonResponse
    {
        $parents = User::where('role', 'parent')
            ->withCount('children')
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return response()->json($parents);
    }

    public function carers(Request $request): JsonResponse
    {
        $carers = User::where('role', 'carer')
            ->with('activeRooms')
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->query('room_id'), function ($query, $roomId) {
                $query->whereHas('activeRooms', fn ($q) => $q->where('rooms.id', $roomId));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return response()->json($carers);
    }

    public function rooms(): JsonResponse
    {
        $rooms = Room::withCount(['children', 'activeCarers'])
            ->orderBy('name')
            ->get();

        return response()->json($rooms);
    }

    /**
     * List invoices with pending payments.
     * GET /api/manager/payments/pending
     */
    public function pendingPayments(): JsonResponse
    {
        $pending = Invoice::where('payment_status', 'payment_submitted')
            ->with(['child:id,first_name,last_name', 'parent:id,first_name,last_name,name'])
            ->latest('payment_submitted_at')
            ->get()
            ->map(fn($inv) => [
                'invoice_id' => $inv->id,
                'total' => $inv->total,
                'child_name' => ($inv->child->first_name ?? '') . ' ' . ($inv->child->last_name ?? ''),
                'parent_name' => ($inv->parent->first_name ?? $inv->parent->name ?? '') . ' ' . ($inv->parent->last_name ?? ''),
                'payment_submitted_at' => $inv->payment_submitted_at,
                'payment_notes' => $inv->payment_notes,
                'payment_proof_url' => $inv->payment_proof_path ? \Storage::url($inv->payment_proof_path) : null,
            ]);

        return response()->json([
            'count' => $pending->count(),
            'pending_payments' => $pending,
        ]);
    }

    /**
     * Approve a payment.
     * POST /api/manager/invoices/{invoice}/approve
     */
    public function approvePayment(Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->isPaymentPending(), 422, 'No pending payment to approve.');
        $invoice->approvePayment(auth()->id());

        return response()->json([
            'message' => 'Payment approved.',
            'invoice_id' => $invoice->id,
            'status' => $invoice->status,
            'payment_status' => $invoice->payment_status,
        ]);
    }

    /**
     * Reject a payment.
     * POST /api/manager/invoices/{invoice}/reject
     */
    public function rejectPayment(\Illuminate\Http\Request $request, Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->isPaymentPending(), 422, 'No pending payment to reject.');

        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $invoice->rejectPayment($request->rejection_reason);

        return response()->json([
            'message' => 'Payment rejected.',
            'invoice_id' => $invoice->id,
            'payment_status' => $invoice->payment_status,
            'rejection_reason' => $invoice->rejection_reason,
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $today = now()->toDateString();

        $todayAttendance = Attendance::where('date', $today)
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->first();

        $recentInvoices = Invoice::with(['child:id,first_name,last_name', 'parent:id,name,email'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'total_children'  => Child::count(),
            'total_parents'   => User::where('role', 'parent')->count(),
            'total_carers'    => User::where('role', 'carer')->count(),
            'total_rooms'     => Room::count(),
            'today_present'   => (int) ($todayAttendance->present_count ?? 0),
            'today_absent'    => (int) ($todayAttendance->absent_count ?? 0),
            'recent_invoices' => $recentInvoices,
        ]);
    }
}
