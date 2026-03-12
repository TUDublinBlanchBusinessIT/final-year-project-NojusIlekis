<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreParentRequest;
use App\Http\Requests\Manager\UpdateParentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $parents = User::where('role', 'parent')
            ->withCount('children')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('manager.parents.index', compact('parents'));
    }

    public function create()
    {
        return view('manager.parents.create');
    }

    public function store(StoreParentRequest $request)
    {
        $validated = $request->validated();

        $parentUser = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'parent',
        ]);

        return redirect()
            ->route('manager.parents.show', $parentUser)
            ->with('success', 'Parent added successfully.');
    }

    public function show(string $id)
    {
        $parentUser = User::where('role', 'parent')
            ->with(['children.room'])
            ->findOrFail($id);

        return view('manager.parents.show', compact('parentUser'));
    }

    public function edit(string $id)
    {
        $parentUser = User::where('role', 'parent')->findOrFail($id);

        return view('manager.parents.edit', compact('parentUser'));
    }

    public function update(UpdateParentRequest $request, string $id)
    {
        $parentUser = User::where('role', 'parent')->findOrFail($id);

        $validated = $request->validated();

        $parentUser->name  = $validated['name'];
        $parentUser->email = $validated['email'];

        if (!empty($validated['password'])) {
            $parentUser->password = Hash::make($validated['password']);
        }

        $parentUser->save();

        return redirect()
            ->route('manager.parents.show', $parentUser)
            ->with('success', 'Parent updated successfully.');
    }

    public function destroy(string $id)
    {
        $parentUser = User::where('role', 'parent')
            ->withCount('children')
            ->findOrFail($id);

        if ($parentUser->children_count > 0) {
            return redirect()
                ->route('manager.parents.show', $parentUser)
                ->with('error', 'Unlink all children before deleting this parent.');
        }

        $parentUser->delete();

        return redirect()
            ->route('manager.parents.index')
            ->with('success', 'Parent deleted successfully.');
    }
}
