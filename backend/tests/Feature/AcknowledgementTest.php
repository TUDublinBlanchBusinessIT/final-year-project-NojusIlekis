<?php

use App\Models\User;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\Acknowledgement;

test('parent can view pending acknowledgements', function () {
    $parent = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($parent)
        ->get(route('parent.acknowledgements.index'));

    $response->assertOk();
});

test('parent can sign an acknowledgement', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $child  = Child::factory()->create();

    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);

    $report = DailyReport::create([
        'child_id'     => $child->id,
        'carer_id'     => $carer->id,
        'date'         => today()->toDateString(),
        'daily_report' => 'Test report content.',
    ]);

    $acknowledgement = Acknowledgement::create([
        'record_type' => 'daily_report',
        'record_id'   => $report->id,
        'parent_id'   => $parent->id,
        'status'      => 'pending',
    ]);

    $response = $this->actingAs($parent)
        ->post(route('parent.acknowledgements.sign', $acknowledgement), [
            'signature_name'          => 'John Smith',
            'confirm_acknowledgement' => true,
        ]);

    $response->assertRedirect();

    $acknowledgement->refresh();
    expect($acknowledgement->status)->toBe('acknowledged');
    expect($acknowledgement->signature_name)->toBe('John Smith');
    expect($acknowledgement->signed_at)->not->toBeNull();
});

test('parent cannot sign another parents acknowledgement', function () {
    $parent1 = User::factory()->create(['role' => 'parent']);
    $parent2 = User::factory()->create(['role' => 'parent']);

    $acknowledgement = Acknowledgement::create([
        'record_type' => 'daily_report',
        'record_id'   => 1,
        'parent_id'   => $parent1->id,
        'status'      => 'pending',
    ]);

    $response = $this->actingAs($parent2)
        ->post(route('parent.acknowledgements.sign', $acknowledgement), [
            'signature_name'          => 'Sneaky Person',
            'confirm_acknowledgement' => true,
        ]);

    $response->assertForbidden();

    $acknowledgement->refresh();
    expect($acknowledgement->status)->toBe('pending');
});

test('parent cannot sign already acknowledged item', function () {
    $parent = User::factory()->create(['role' => 'parent']);

    $acknowledgement = Acknowledgement::create([
        'record_type'    => 'daily_report',
        'record_id'      => 1,
        'parent_id'      => $parent->id,
        'status'         => 'acknowledged',
        'signed_at'      => now(),
        'signature_name' => 'Already Signed',
    ]);

    $response = $this->actingAs($parent)
        ->post(route('parent.acknowledgements.sign', $acknowledgement), [
            'signature_name'          => 'Try Again',
            'confirm_acknowledgement' => true,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $acknowledgement->refresh();
    expect($acknowledgement->signature_name)->toBe('Already Signed');
});

test('signature name is required when signing', function () {
    $parent = User::factory()->create(['role' => 'parent']);

    $acknowledgement = Acknowledgement::create([
        'record_type' => 'daily_report',
        'record_id'   => 1,
        'parent_id'   => $parent->id,
        'status'      => 'pending',
    ]);

    $response = $this->actingAs($parent)
        ->post(route('parent.acknowledgements.sign', $acknowledgement), [
            'signature_name'          => '',
            'confirm_acknowledgement' => true,
        ]);

    $response->assertSessionHasErrors('signature_name');
});

test('manager can see acknowledgement status on daily reports', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $response = $this->actingAs($manager)
        ->get(route('manager.reports.daily-reports.index'));

    $response->assertOk();
});
