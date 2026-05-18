<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $baseRules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'phone'    => ['required', 'string', 'max:20'],
            'address'  => ['required', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:parent,carer'],
        ];

        if ($request->role === 'parent') {
            $baseRules = array_merge($baseRules, [
                'child_first_name'    => ['required', 'string', 'max:100'],
                'child_last_name'     => ['required', 'string', 'max:100'],
                'child_dob'           => ['required', 'date', 'before:today'],
                'child_allergies'     => ['nullable', 'string', 'max:500'],
                'child_medical_notes' => ['nullable', 'string', 'max:1000'],
                'registration_notes'  => ['nullable', 'string', 'max:1000'],
            ]);
        } else {
            $baseRules['registration_notes'] = ['required', 'string', 'max:2000'];
        }

        $validated = $request->validate($baseRules);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'               => $validated['name'],
                'email'              => $validated['email'],
                'phone'              => $validated['phone'],
                'address'            => $validated['address'],
                'password'           => Hash::make($validated['password']),
                'role'               => $validated['role'],
                'status'             => 'pending',
                'registration_notes' => $validated['registration_notes'] ?? null,
            ]);

            if ($validated['role'] === 'parent') {
                $child = Child::create([
                    'first_name'    => $validated['child_first_name'],
                    'last_name'     => $validated['child_last_name'],
                    'dob'           => $validated['child_dob'],
                    'allergies'     => $validated['child_allergies'] ?? null,
                    'medical_notes' => $validated['child_medical_notes'] ?? null,
                    'room_id'       => null,
                ]);

                $child->parents()->attach($user->id, [
                    'relationship_type' => 'parent',
                    'legal_guardian'    => true,
                ]);
            }
        });

        return redirect()->route('registration.pending');
    }
}
