<?php

use App\Models\Child;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->manager = User::factory()->create([
        'role'        => 'manager',
        'email'       => 'manager@test.com',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);
});

test('parent can self-register with child details and ends up pending', function () {
    $response = $this->post('/register', [
        'name'              => 'Sarah Murphy',
        'email'             => 'sarah@example.com',
        'phone'             => '0871234567',
        'address'           => '12 Oak Street, Dublin',
        'password'          => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role'              => 'parent',
        'child_first_name'  => 'Liam',
        'child_last_name'   => 'Murphy',
        'child_dob'         => now()->subYears(3)->toDateString(),
        'child_allergies'   => 'peanuts',
    ]);

    $response->assertRedirect(route('registration.pending'));
    $this->assertGuest();

    $user = User::where('email', 'sarah@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->status)->toBe('pending')
        ->and($user->role)->toBe('parent');

    $child = Child::where('first_name', 'Liam')->where('last_name', 'Murphy')->first();
    expect($child)->not->toBeNull()
        ->and($child->parents()->where('users.id', $user->id)->exists())->toBeTrue();
});

test('carer can self-register and ends up pending', function () {
    $response = $this->post('/register', [
        'name'               => 'John Doe',
        'email'              => 'john@example.com',
        'phone'              => '0871234567',
        'address'            => '5 Main St',
        'password'           => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role'               => 'carer',
        'registration_notes' => 'QQI Level 6 in Early Childhood Care, 3 years experience',
    ]);

    $response->assertRedirect(route('registration.pending'));
    $this->assertGuest();

    $user = User::where('email', 'john@example.com')->first();
    expect($user->status)->toBe('pending')
        ->and($user->role)->toBe('carer');
    expect(Child::count())->toBe(0);
});

test('manager role cannot be selected at public registration', function () {
    $response = $this->post('/register', [
        'name'     => 'Admin',
        'email'    => 'admin@example.com',
        'phone'    => '0871234567',
        'address'  => '1 Main St',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role'     => 'manager',
    ]);

    $response->assertSessionHasErrors('role');
    expect(User::where('email', 'admin@example.com')->exists())->toBeFalse();
});

test('pending user cannot log in', function () {
    $user = User::factory()->create([
        'email'    => 'pending@example.com',
        'password' => Hash::make('Password123!'),
        'role'     => 'parent',
        'status'   => 'pending',
    ]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('registration.pending'));
});

test('rejected user cannot log in and sees rejection reason', function () {
    $user = User::factory()->create([
        'email'            => 'rejected@example.com',
        'password'         => Hash::make('Password123!'),
        'role'             => 'parent',
        'status'           => 'rejected',
        'rejection_reason' => 'No availability this term',
    ]);

    $response = $this->from('/login')->post('/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/login');
    $errors = session('errors')->get('email');
    expect(collect($errors)->some(fn ($m) => str_contains($m, 'No availability this term')))->toBeTrue();
});

test('approved user can log in normally', function () {
    $user = User::factory()->create([
        'email'       => 'approved@example.com',
        'password'    => Hash::make('Password123!'),
        'role'        => 'parent',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('manager can view list of pending registrations', function () {
    User::factory()->count(2)->create(['role' => 'parent', 'status' => 'pending']);
    User::factory()->create(['role' => 'carer', 'status' => 'pending']);

    $response = $this->actingAs($this->manager)
        ->get('/manager/pending-registrations');

    $response->assertStatus(200);
    expect(User::where('status', 'pending')->count())->toBe(3);
});

test('manager can approve a parent registration', function () {
    $parent = User::factory()->create([
        'role'   => 'parent',
        'status' => 'pending',
    ]);
    $child = Child::create([
        'first_name' => 'Lily',
        'last_name'  => 'Test',
        'dob'        => now()->subYears(2)->toDateString(),
    ]);
    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);

    $response = $this->actingAs($this->manager)
        ->post("/manager/pending-registrations/{$parent->id}/approve");

    $response->assertRedirect(route('manager.pending-registrations.index'));
    $parent->refresh();
    expect($parent->status)->toBe('approved')
        ->and($parent->approved_at)->not->toBeNull()
        ->and((int) $parent->approved_by)->toBe($this->manager->id);
});

test('manager can approve a carer with room assignment', function () {
    $room = Room::create(['name' => 'TestRoom']);
    $carer = User::factory()->create([
        'role'   => 'carer',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->manager)
        ->post("/manager/pending-registrations/{$carer->id}/approve", [
            'room_id' => $room->id,
        ]);

    $response->assertRedirect(route('manager.pending-registrations.index'));
    $carer->refresh();
    expect($carer->status)->toBe('approved');

    $active = \DB::table('room_user')
        ->where('user_id', $carer->id)
        ->where('room_id', $room->id)
        ->whereNull('end_date')
        ->exists();
    expect($active)->toBeTrue();
});

test('manager can reject a registration with a reason', function () {
    $user = User::factory()->create([
        'role'   => 'parent',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->manager)
        ->post("/manager/pending-registrations/{$user->id}/reject", [
            'rejection_reason' => 'Crèche is full',
        ]);

    $response->assertRedirect(route('manager.pending-registrations.index'));
    $user->refresh();
    expect($user->status)->toBe('rejected')
        ->and($user->rejection_reason)->toBe('Crèche is full');
});

test("manager can edit a pending parent's details before approval", function () {
    $parent = User::factory()->create([
        'role'   => 'parent',
        'status' => 'pending',
        'name'   => 'Old Name',
        'phone'  => '0870000000',
        'address' => '1 Old Street',
    ]);
    $child = Child::create([
        'first_name' => 'OldFirst',
        'last_name'  => 'OldLast',
        'dob'        => now()->subYears(2)->toDateString(),
    ]);
    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);

    $response = $this->actingAs($this->manager)
        ->put("/manager/pending-registrations/{$parent->id}", [
            'name'               => 'New Name',
            'email'              => $parent->email,
            'phone'              => '0879999999',
            'address'            => '99 New Street',
            'child_first_name'   => 'NewFirst',
            'child_last_name'    => 'NewLast',
            'child_dob'          => now()->subYears(3)->toDateString(),
        ]);

    $response->assertRedirect(route('manager.pending-registrations.show', $parent));
    $parent->refresh();
    $child->refresh();
    expect($parent->name)->toBe('New Name')
        ->and($parent->phone)->toBe('0879999999')
        ->and($child->first_name)->toBe('NewFirst');
});

test('manager can delete a pending registration', function () {
    $parent = User::factory()->create([
        'role'   => 'parent',
        'status' => 'pending',
    ]);
    $child = Child::create([
        'first_name' => 'Tobe',
        'last_name'  => 'Deleted',
        'dob'        => now()->subYears(2)->toDateString(),
    ]);
    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);

    $response = $this->actingAs($this->manager)
        ->delete("/manager/pending-registrations/{$parent->id}");

    $response->assertRedirect(route('manager.pending-registrations.index'));
    expect(User::find($parent->id))->toBeNull()
        ->and(Child::find($child->id))->toBeNull();
});

test('manager cannot delete an approved user via this controller', function () {
    $user = User::factory()->create([
        'role'        => 'parent',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($this->manager)
        ->delete("/manager/pending-registrations/{$user->id}");

    $response->assertStatus(403);
    expect(User::find($user->id))->not->toBeNull();
});

test('non-manager cannot access pending registrations', function () {
    $parent = User::factory()->create([
        'role'        => 'parent',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($parent)
        ->get('/manager/pending-registrations');

    $response->assertStatus(403);
});
