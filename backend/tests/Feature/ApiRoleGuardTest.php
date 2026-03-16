<?php

use App\Models\Room;
use App\Models\User;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function tokenFor(string $role): array
{
    $user  = User::factory()->create(['role' => $role]);
    $token = $user->createToken('test-token')->plainTextToken;
    return [$user, $token];
}

// ---------------------------------------------------------------------------
// Parent role guard
// ---------------------------------------------------------------------------

it('parent can access /api/parent/children', function () {
    [, $token] = tokenFor('parent');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/parent/children')
         ->assertStatus(200);
});

it('parent cannot access /api/carer/rooms', function () {
    [, $token] = tokenFor('parent');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/carer/rooms')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: carer']);
});

it('parent cannot access /api/manager/dashboard', function () {
    [, $token] = tokenFor('parent');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/manager/dashboard')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: manager']);
});

// ---------------------------------------------------------------------------
// Carer role guard
// ---------------------------------------------------------------------------

it('carer can access /api/carer/rooms', function () {
    [$carer, $token] = tokenFor('carer');
    $room = Room::factory()->create();
    $carer->rooms()->attach($room->id, ['start_date' => now()->toDateString()]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/carer/rooms')
         ->assertStatus(200);
});

it('carer cannot access /api/parent/children', function () {
    [, $token] = tokenFor('carer');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/parent/children')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: parent']);
});

it('carer cannot access /api/manager/dashboard', function () {
    [, $token] = tokenFor('carer');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/manager/dashboard')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: manager']);
});

// ---------------------------------------------------------------------------
// Manager role guard
// ---------------------------------------------------------------------------

it('manager can access /api/manager/dashboard', function () {
    [, $token] = tokenFor('manager');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/manager/dashboard')
         ->assertStatus(200);
});

it('manager cannot access /api/parent/children', function () {
    [, $token] = tokenFor('manager');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/parent/children')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: parent']);
});

it('manager cannot access /api/carer/rooms', function () {
    [, $token] = tokenFor('manager');

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/carer/rooms')
         ->assertStatus(403)
         ->assertJsonFragment(['message' => 'Forbidden. Required role: carer']);
});

// ---------------------------------------------------------------------------
// Edge case
// ---------------------------------------------------------------------------

it('unauthenticated request to role-guarded route returns 401', function () {
    $this->getJson('/api/parent/children')->assertStatus(401);
});
