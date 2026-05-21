<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\StaffQualification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffQualificationController extends Controller
{
    public function index(User $carer)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);

        $qualifications = $carer->qualifications()->get();
        return view('manager.qualifications.index', compact('carer', 'qualifications'));
    }

    public function create(User $carer)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);

        $types = StaffQualification::TYPES;
        return view('manager.qualifications.create', compact('carer', 'types'));
    }

    public function store(Request $request, User $carer)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);

        $validated = $request->validate([
            'type'        => ['required', 'in:' . implode(',', array_keys(StaffQualification::TYPES))],
            'name'        => ['required', 'string', 'max:255'],
            'issuer'      => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'expires_at'  => ['nullable', 'date', 'after:today'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'document'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('qualifications', 'public');
        }

        StaffQualification::create([
            'user_id'       => $carer->id,
            'type'          => $validated['type'],
            'name'          => $validated['name'],
            'issuer'        => $validated['issuer'] ?? null,
            'issued_date'   => $validated['issued_date'] ?? null,
            'expires_at'    => $validated['expires_at'] ?? null,
            'notes'         => $validated['notes'] ?? null,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('manager.carers.qualifications.index', $carer)
            ->with('success', __('staff.qualification_added'));
    }

    public function edit(User $carer, StaffQualification $qualification)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);
        abort_unless($qualification->user_id === $carer->id, 404);

        $types = StaffQualification::TYPES;
        return view('manager.qualifications.edit', compact('carer', 'qualification', 'types'));
    }

    public function update(Request $request, User $carer, StaffQualification $qualification)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);
        abort_unless($qualification->user_id === $carer->id, 404);

        $validated = $request->validate([
            'type'        => ['required', 'in:' . implode(',', array_keys(StaffQualification::TYPES))],
            'name'        => ['required', 'string', 'max:255'],
            'issuer'      => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'expires_at'  => ['nullable', 'date'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'document'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('document')) {
            if ($qualification->document_path) {
                Storage::disk('public')->delete($qualification->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('qualifications', 'public');
        }
        unset($validated['document']);

        $qualification->update($validated);

        return redirect()->route('manager.carers.qualifications.index', $carer)
            ->with('success', __('staff.qualification_updated'));
    }

    public function destroy(User $carer, StaffQualification $qualification)
    {
        $this->authorize('viewAny', User::class);
        abort_unless($carer->role === 'carer', 404);
        abort_unless($qualification->user_id === $carer->id, 404);

        if ($qualification->document_path) {
            Storage::disk('public')->delete($qualification->document_path);
        }
        $qualification->delete();

        return redirect()->route('manager.carers.qualifications.index', $carer)
            ->with('success', __('staff.qualification_deleted'));
    }
}
