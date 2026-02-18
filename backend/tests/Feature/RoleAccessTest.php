<?php

use App\Models\User;

it('blocks parent from carer dashboard', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $this->actingAs($parent)->get('/carer/dashboard')->assertForbidden();
});

it('blocks parent from manager dashboard', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $this->actingAs($parent)->get('/manager/dashboard')->assertForbidden();
});

it('allows parent to access parent dashboard', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $this->actingAs($parent)->get('/parent/dashboard')->assertOk();
});

it('allows carer to access carer dashboard', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $this->actingAs($carer)->get('/carer/dashboard')->assertOk();
});

it('allows manager to access manager dashboard', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager)->get('/manager/dashboard')->assertOk();
});
