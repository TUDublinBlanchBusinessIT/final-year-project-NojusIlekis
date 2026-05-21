<?php

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Room;
use App\Models\StaffClockIn;
use App\Models\StaffQualification;
use App\Models\User;

test('carer can clock in', function () {
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);

    $response = $this->actingAs($carer)->post(route('carer.clock-in'));

    $response->assertRedirect();
    expect($carer->fresh()->isClockedIn())->toBeTrue();
});

test('carer cannot clock in twice', function () {
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    StaffClockIn::create(['user_id' => $carer->id, 'clocked_in_at' => now()]);

    $response = $this->actingAs($carer)->post(route('carer.clock-in'));
    $response->assertStatus(422);
});

test('carer can clock out', function () {
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    StaffClockIn::create(['user_id' => $carer->id, 'clocked_in_at' => now()->subHours(2)]);

    $response = $this->actingAs($carer)->post(route('carer.clock-out'));

    $response->assertRedirect();
    expect($carer->fresh()->isClockedIn())->toBeFalse();
    $latest = $carer->clockIns()->first();
    expect($latest->clocked_out_at)->not->toBeNull();
});

test('carer cannot clock out when not clocked in', function () {
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $response = $this->actingAs($carer)->post(route('carer.clock-out'));
    $response->assertStatus(422);
});

test('manager can add a qualification for a carer', function () {
    $manager = User::factory()->create(['role' => 'manager', 'status' => 'approved']);
    $carer   = User::factory()->create(['role' => 'carer', 'status' => 'approved']);

    $response = $this->actingAs($manager)
        ->post(route('manager.carers.qualifications.store', $carer), [
            'type'        => 'first_aid',
            'name'        => 'Paediatric First Aid',
            'issuer'      => 'Irish Heart Foundation',
            'issued_date' => '2025-01-15',
            'expires_at'  => now()->addYears(2)->toDateString(),
        ]);

    $response->assertRedirect();
    expect($carer->qualifications()->count())->toBe(1);
    expect($carer->qualifications()->first()->name)->toBe('Paediatric First Aid');
});

test('manager can edit a qualification', function () {
    $manager = User::factory()->create(['role' => 'manager', 'status' => 'approved']);
    $carer   = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $qual    = StaffQualification::create([
        'user_id' => $carer->id, 'type' => 'first_aid', 'name' => 'Old Name',
    ]);

    $response = $this->actingAs($manager)
        ->put(route('manager.carers.qualifications.update', [$carer, $qual]), [
            'type'       => 'first_aid',
            'name'       => 'New Name',
            'expires_at' => now()->addYear()->toDateString(),
        ]);

    $response->assertRedirect();
    expect($qual->fresh()->name)->toBe('New Name');
});

test('manager can delete a qualification', function () {
    $manager = User::factory()->create(['role' => 'manager', 'status' => 'approved']);
    $carer   = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $qual    = StaffQualification::create([
        'user_id' => $carer->id, 'type' => 'first_aid', 'name' => 'Test',
    ]);

    $response = $this->actingAs($manager)
        ->delete(route('manager.carers.qualifications.destroy', [$carer, $qual]));

    $response->assertRedirect();
    expect(StaffQualification::find($qual->id))->toBeNull();
});

test('qualification is marked as expired when past expiry date', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $qual  = StaffQualification::create([
        'user_id'    => $carer->id,
        'type'       => 'first_aid',
        'name'       => 'Old First Aid',
        'expires_at' => now()->subDays(10)->toDateString(),
    ]);

    expect($qual->isExpired())->toBeTrue();
    expect($qual->statusLabel())->toBe(__('staff.expired'));
});

test('qualification is marked as expiring soon within 30 days', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $qual  = StaffQualification::create([
        'user_id'    => $carer->id,
        'type'       => 'garda_vetting',
        'name'       => 'Vetting',
        'expires_at' => now()->addDays(20)->toDateString(),
    ]);

    expect($qual->isExpired())->toBeFalse();
    expect($qual->isExpiringSoon(30))->toBeTrue();
});

test('permanent qualifications never show as expiring', function () {
    $carer = User::factory()->create(['role' => 'carer']);
    $qual  = StaffQualification::create([
        'user_id'    => $carer->id,
        'type'       => 'education',
        'name'       => 'QQI Level 6',
        'expires_at' => null,
    ]);

    expect($qual->isExpired())->toBeFalse();
    expect($qual->isExpiringSoon())->toBeFalse();
    expect($qual->statusLabel())->toBe(__('staff.permanent'));
});

test('non-manager cannot access qualification CRUD', function () {
    $parent = User::factory()->create(['role' => 'parent', 'status' => 'approved']);
    $carer  = User::factory()->create(['role' => 'carer', 'status' => 'approved']);

    $response = $this->actingAs($parent)
        ->get(route('manager.carers.qualifications.index', $carer));
    $response->assertForbidden();
});

test('room calculates required staff based on present children', function () {
    $room  = Room::factory()->create(['max_children_per_staff' => 5]);
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);

    $room->users()->attach($carer->id, ['start_date' => today(), 'is_primary' => true]);

    for ($i = 0; $i < 12; $i++) {
        $child = Child::factory()->create(['room_id' => $room->id]);
        Attendance::create([
            'child_id'    => $child->id,
            'date'        => today(),
            'status'      => 'present',
            'room_id'     => $room->id,
            'recorded_by' => $carer->id,
        ]);
    }

    $ratio = $room->currentRatio();
    expect($ratio['children'])->toBe(12);
    expect($ratio['required'])->toBe(3);
});

test('room is compliant when enough staff clocked in', function () {
    $room   = Room::factory()->create(['max_children_per_staff' => 5]);
    $carer1 = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $carer2 = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $room->users()->attach([
        $carer1->id => ['start_date' => today(), 'is_primary' => true],
        $carer2->id => ['start_date' => today(), 'is_primary' => false],
    ]);

    StaffClockIn::create(['user_id' => $carer1->id, 'clocked_in_at' => now()]);
    StaffClockIn::create(['user_id' => $carer2->id, 'clocked_in_at' => now()]);

    for ($i = 0; $i < 8; $i++) {
        $child = Child::factory()->create(['room_id' => $room->id]);
        Attendance::create([
            'child_id'    => $child->id,
            'date'        => today(),
            'status'      => 'present',
            'room_id'     => $room->id,
            'recorded_by' => $carer1->id,
        ]);
    }

    $ratio = $room->currentRatio();
    expect($ratio['staff'])->toBe(2);
    expect($ratio['required'])->toBe(2);
    expect($ratio['compliant'])->toBeTrue();
});

test('room is non-compliant when not enough staff clocked in', function () {
    $room  = Room::factory()->create(['max_children_per_staff' => 5]);
    $carer = User::factory()->create(['role' => 'carer', 'status' => 'approved']);
    $room->users()->attach($carer->id, ['start_date' => today(), 'is_primary' => true]);

    StaffClockIn::create(['user_id' => $carer->id, 'clocked_in_at' => now()]);

    for ($i = 0; $i < 12; $i++) {
        $child = Child::factory()->create(['room_id' => $room->id]);
        Attendance::create([
            'child_id'    => $child->id,
            'date'        => today(),
            'status'      => 'present',
            'room_id'     => $room->id,
            'recorded_by' => $carer->id,
        ]);
    }

    $ratio = $room->currentRatio();
    expect($ratio['compliant'])->toBeFalse();
    expect($ratio['shortfall'])->toBe(2);
});

test('user isClockedIn returns false after clock out', function () {
    $carer    = User::factory()->create(['role' => 'carer']);
    $clockIn  = StaffClockIn::create([
        'user_id'        => $carer->id,
        'clocked_in_at'  => now()->subHours(8),
        'clocked_out_at' => now(),
    ]);

    expect($carer->isClockedIn())->toBeFalse();
});
