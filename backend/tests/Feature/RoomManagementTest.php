<?php

use App\Models\Child;
use App\Models\Room;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Access control
|--------------------------------------------------------------------------
*/

it('allows a manager to view the rooms index page', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->get(route('manager.rooms.index'))
        ->assertOk();
});

it('blocks a carer from viewing the rooms index page', function () {
    $carer = User::factory()->create(['role' => 'carer']);

    $this->actingAs($carer)
        ->get(route('manager.rooms.index'))
        ->assertForbidden();
});

it('blocks a parent from viewing the rooms index page', function () {
    $parent = User::factory()->create(['role' => 'parent']);

    $this->actingAs($parent)
        ->get(route('manager.rooms.index'))
        ->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

it('allows a manager to create a new room with valid data', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $response = $this->actingAs($manager)->post(route('manager.rooms.store'), [
        'name'        => 'Sunflowers',
        'age_band'    => '3-5 years',
        'capacity'    => 12,
        'description' => 'Bright south-facing room with a reading corner.',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('rooms', [
        'name'     => 'Sunflowers',
        'capacity' => 12,
        'age_band' => '3-5 years',
    ]);
});

it('fails validation when required fields are missing', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->from(route('manager.rooms.create'))
        ->post(route('manager.rooms.store'), [
            'name'     => '',
            'capacity' => '',
        ])
        ->assertRedirect(route('manager.rooms.create'))
        ->assertSessionHasErrors(['name', 'capacity']);
});

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

it('allows a manager to update a room', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $room = Room::factory()->create([
        'name'     => 'Old Name',
        'capacity' => 10,
    ]);

    $this->actingAs($manager)->put(route('manager.rooms.update', $room), [
        'name'        => 'New Name',
        'age_band'    => '2-3 years',
        'capacity'    => 14,
        'description' => 'Updated description.',
    ])->assertRedirect();

    $this->assertDatabaseHas('rooms', [
        'id'       => $room->id,
        'name'     => 'New Name',
        'capacity' => 14,
    ]);
});

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

it('allows a manager to delete a room that has no children', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $room = Room::factory()->create();

    $this->actingAs($manager)
        ->delete(route('manager.rooms.destroy', $room))
        ->assertRedirect(route('manager.rooms.index'));

    $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
});

it('prevents a manager from deleting a room that has children assigned', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $room = Room::factory()->create();

    Child::factory()->create(['room_id' => $room->id]);

    $this->actingAs($manager)
        ->delete(route('manager.rooms.destroy', $room))
        ->assertRedirect(route('manager.rooms.show', $room))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('rooms', ['id' => $room->id]);
});
