<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingRegistrationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $pending = User::where('status', 'pending')
            ->with(['children' => fn ($q) => $q->withPivot('relationship_type', 'legal_guardian')])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingParents = $pending->where('role', 'parent')->values();
        $pendingCarers  = $pending->where('role', 'carer')->values();

        return view('manager.pending-registrations.index', compact('pendingParents', 'pendingCarers'));
    }

    public function show(User $user)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($user->isPending(), 404);

        $user->load(['children' => fn ($q) => $q->withPivot('relationship_type', 'legal_guardian')]);

        return view('manager.pending-registrations.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($user->isPending(), 404);

        $user->load(['children' => fn ($q) => $q->withPivot('relationship_type', 'legal_guardian')]);
        $rooms = Room::orderBy('name')->get();
        $child = $user->children->first();

        return view('manager.pending-registrations.edit', compact('user', 'rooms', 'child'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($user->isPending(), 404);

        $rules = [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'              => ['required', 'string', 'max:20'],
            'address'            => ['required', 'string', 'max:500'],
            'registration_notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($user->role === 'parent') {
            $rules = array_merge($rules, [
                'child_first_name'    => ['required', 'string', 'max:100'],
                'child_last_name'     => ['required', 'string', 'max:100'],
                'child_dob'           => ['required', 'date', 'before:today'],
                'child_allergies'     => ['nullable', 'string', 'max:500'],
                'child_medical_notes' => ['nullable', 'string', 'max:1000'],
                'child_room_id'       => ['nullable', 'integer', 'exists:rooms,id'],
            ]);
        } else {
            $rules['room_id'] = ['nullable', 'integer', 'exists:rooms,id'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name'               => $validated['name'],
                'email'              => $validated['email'],
                'phone'              => $validated['phone'],
                'address'            => $validated['address'],
                'registration_notes' => $validated['registration_notes'] ?? null,
            ]);

            if ($user->role === 'parent') {
                $child = $user->children()->first();
                $childData = [
                    'first_name'    => $validated['child_first_name'],
                    'last_name'     => $validated['child_last_name'],
                    'dob'           => $validated['child_dob'],
                    'allergies'     => $validated['child_allergies'] ?? null,
                    'medical_notes' => $validated['child_medical_notes'] ?? null,
                    'room_id'       => $validated['child_room_id'] ?? null,
                ];

                if ($child) {
                    $child->update($childData);
                } else {
                    $newChild = Child::create($childData);
                    $newChild->parents()->attach($user->id, [
                        'relationship_type' => 'parent',
                        'legal_guardian'    => true,
                    ]);
                }
            }
        });

        return redirect()
            ->route('manager.pending-registrations.show', $user)
            ->with('success', __('manager.pending_registration_updated'));
    }

    public function approve(Request $request, User $user)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($user->isPending(), 404);

        $roomId = null;
        if ($user->role === 'carer') {
            $validated = $request->validate([
                'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            ]);
            $roomId = $validated['room_id'] ?? null;
        }

        DB::transaction(function () use ($user, $roomId) {
            $user->update([
                'status'           => 'approved',
                'approved_at'      => now(),
                'approved_by'      => auth()->id(),
                'rejection_reason' => null,
            ]);

            if ($user->role === 'carer' && $roomId) {
                $this->assignCarerToRoom($user, (int) $roomId);
            }
        });

        return redirect()
            ->route('manager.pending-registrations.index')
            ->with('success', __('manager.registration_approved', ['name' => $user->name]));
    }

    public function reject(Request $request, User $user)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($user->isPending(), 404);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('manager.pending-registrations.index')
            ->with('success', __('manager.registration_rejected', ['name' => $user->name]));
    }

    public function destroy(User $user)
    {
        $this->authorize('viewAny', User::class);

        if ($user->status === 'approved') {
            abort(403, 'Cannot delete an approved user from this screen.');
        }

        DB::transaction(function () use ($user) {
            if ($user->role === 'parent') {
                foreach ($user->children as $child) {
                    $child->parents()->detach($user->id);
                    if ($child->parents()->count() === 0) {
                        $child->delete();
                    }
                }
            }
            $user->delete();
        });

        return redirect()
            ->route('manager.pending-registrations.index')
            ->with('success', __('manager.pending_registration_deleted'));
    }

    /**
     * Mirrors Manager\CarerController::assignRoom() — closes other active rows,
     * reopens a same-day closed row, or inserts fresh.
     */
    private function assignCarerToRoom(User $carer, int $roomId): void
    {
        $today = now()->toDateString();

        DB::table('room_user')
            ->where('user_id', $carer->id)
            ->whereNull('end_date')
            ->where('room_id', '!=', $roomId)
            ->update(['end_date' => $today]);

        $alreadyAssigned = DB::table('room_user')
            ->where('user_id', $carer->id)
            ->where('room_id', $roomId)
            ->whereNull('end_date')
            ->exists();

        if ($alreadyAssigned) {
            return;
        }

        $closedTodayRow = DB::table('room_user')
            ->where('user_id', $carer->id)
            ->where('room_id', $roomId)
            ->where('start_date', $today)
            ->whereNotNull('end_date')
            ->exists();

        if ($closedTodayRow) {
            DB::table('room_user')
                ->where('user_id', $carer->id)
                ->where('room_id', $roomId)
                ->where('start_date', $today)
                ->update([
                    'end_date'   => null,
                    'is_primary' => true,
                    'updated_at' => now(),
                ]);
            return;
        }

        $carer->rooms()->attach($roomId, [
            'start_date' => $today,
            'end_date'   => null,
            'is_primary' => true,
        ]);
    }
}
