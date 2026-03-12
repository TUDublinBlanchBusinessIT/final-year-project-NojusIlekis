<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;

class ChildPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['manager', 'carer']);
    }

    public function view(User $user, Child $child): bool
    {
        if ($user->role === 'manager') {
            return true;
        }

        if ($user->role === 'carer') {
            return $user->activeRooms()->where('rooms.id', $child->room_id)->exists();
        }

        if ($user->role === 'parent') {
            return $user->children()->where('child_id', $child->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function update(User $user, Child $child): bool
    {
        return $user->role === 'manager';
    }

    public function delete(User $user, Child $child): bool
    {
        return $user->role === 'manager';
    }

    public function linkParent(User $user, Child $child): bool
    {
        return $user->role === 'manager';
    }

    public function assignRoom(User $user, Child $child): bool
    {
        return $user->role === 'manager';
    }
}
