<?php

use App\Models\User;
use App\Models\Child;
use App\Models\Room;

/*
|--------------------------------------------------------------------------
| Helper: set allergies on a child
|--------------------------------------------------------------------------
*/
function setChildAllergies(Child $child, string|null $allergies): Child
{
    $child->allergies = $allergies;
    $child->save();
    return $child->fresh();
}

/*
|--------------------------------------------------------------------------
| Model Tests
|--------------------------------------------------------------------------
*/

test('child hasAllergies returns true when allergies exist', function () {
    $child = Child::factory()->create();
    setChildAllergies($child, 'Peanuts, Dairy');

    expect($child->hasAllergies())->toBeTrue();
});

test('child hasAllergies returns false when no allergies', function () {
    $child = Child::factory()->create(['allergies' => null]);

    expect($child->hasAllergies())->toBeFalse();
});

test('child allergyList returns formatted string', function () {
    $child = Child::factory()->create();
    setChildAllergies($child, 'Peanuts, Dairy, Eggs');

    expect($child->allergyList())
        ->toContain('Peanuts')
        ->toContain('Dairy')
        ->toContain('Eggs');
});

test('child allergyArray returns array', function () {
    $child = Child::factory()->create();
    setChildAllergies($child, 'Peanuts, Dairy');

    $array = $child->allergyArray();
    expect($array)
        ->toBeArray()
        ->toContain('Peanuts')
        ->toContain('Dairy');
});

/*
|--------------------------------------------------------------------------
| Blade Component Tests
|--------------------------------------------------------------------------
*/

test('allergy alert component renders for child with allergies', function () {
    $child = Child::factory()->create();
    setChildAllergies($child, 'Peanuts');

    $view = $this->blade('<x-allergy-alert :child="$child" />', ['child' => $child]);
    $view->assertSee('Allergy Alert');
    $view->assertSee('Peanuts');
});

test('allergy alert component hidden for child without allergies', function () {
    $child = Child::factory()->create(['allergies' => null]);

    $view = $this->blade('<x-allergy-alert :child="$child" />', ['child' => $child]);
    $view->assertDontSee('Allergy Alert');
});

/*
|--------------------------------------------------------------------------
| Carer View Tests
|--------------------------------------------------------------------------
*/

test('carer attendance page loads successfully for carer with room', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $room = Room::factory()->create();
    $child = Child::factory()->create(['room_id' => $room->id]);
    setChildAllergies($child, 'Eggs');

    $room->users()->attach($carer->id, [
        'start_date' => now()->subDay(),
        'is_primary' => true,
    ]);

    $response = $this->actingAs($carer)->get(route('carer.attendance.index', [
        'room' => $room->id,
        'date' => today()->toDateString(),
    ]));

    $response->assertOk();
});

test('carer daily update page allergy alert component renders for child with allergies', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $room = Room::factory()->create();
    $child = Child::factory()->create(['room_id' => $room->id]);
    setChildAllergies($child, 'Gluten');

    $room->users()->attach($carer->id, [
        'start_date' => now()->subDay(),
        'is_primary' => true,
    ]);

    $view = $this->blade('<x-allergy-alert :child="$child" />', ['child' => $child]);
    $view->assertSee('Gluten');
});

/*
|--------------------------------------------------------------------------
| Manager View Tests
|--------------------------------------------------------------------------
*/

test('manager child profile shows allergy alert', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $child = Child::factory()->create();
    setChildAllergies($child, 'Shellfish, Soy');

    $response = $this->actingAs($manager)->get(route('manager.children.show', $child));

    $response->assertOk();
    $response->assertSee('Allergy Alert');
    $response->assertSee('Shellfish');
});

test('manager child profile shows no alert when no allergies', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $child = Child::factory()->create(['allergies' => null]);

    $response = $this->actingAs($manager)->get(route('manager.children.show', $child));

    $response->assertOk();
    $response->assertDontSee('Allergy Alert');
});

/*
|--------------------------------------------------------------------------
| Manager Edit Tests
|--------------------------------------------------------------------------
*/

test('manager can update child allergies', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $child = Child::factory()->create(['allergies' => null]);

    $response = $this->actingAs($manager)->put(route('manager.children.update', $child), [
        'first_name' => $child->first_name,
        'last_name'  => $child->last_name,
        'dob'        => $child->dob?->format('Y-m-d') ?? '2023-06-15',
        'allergies'  => 'Peanuts, Tree Nuts',
    ]);

    $response->assertRedirect();

    $child->refresh();
    expect($child->hasAllergies())->toBeTrue();

    $text = is_array($child->allergies) ? implode(', ', $child->allergies) : $child->allergies;
    expect($text)->toContain('Peanuts');
});
