<?php

use App\Models\Attendance;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\DailyUpdate;
use App\Models\IncidentReport;
use App\Models\MedicationLog;
use App\Models\Room;
use App\Models\User;

// ---------------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------------

function linkParent(User $parent, Child $child): void
{
    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);
}

// ---------------------------------------------------------------------------
// Web timeline tests
// ---------------------------------------------------------------------------

test('parent can view timeline for their child', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    $this->actingAs($parent)
        ->get(route('parent.children.timeline', $child))
        ->assertOk();
});

test('parent cannot view timeline for another parents child', function () {
    $parent     = User::factory()->create(['role' => 'parent']);
    $otherChild = Child::factory()->create();
    // NOT linked

    $this->actingAs($parent)
        ->get(route('parent.children.timeline', $otherChild))
        ->assertForbidden();
});

test('timeline filters by date parameter', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    $this->actingAs($parent)
        ->get(route('parent.children.timeline', ['child' => $child->id, 'date' => '2026-03-20']))
        ->assertOk();
});

test('timeline shows empty state when no events for a future date', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    $this->actingAs($parent)
        ->get(route('parent.children.timeline', ['child' => $child->id, 'date' => '2030-01-01']))
        ->assertOk();
});

test('timeline shows attendance events for today', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $room   = Room::factory()->create();
    $child  = Child::factory()->create(['room_id' => $room->id]);
    linkParent($parent, $child);

    Attendance::create([
        'child_id'    => $child->id,
        'date'        => today(),
        'status'      => 'present',
        'room_id'     => $room->id,
        'recorded_by' => $carer->id,
        'check_in_at' => now()->setTime(9, 0),
    ]);

    $this->actingAs($parent)
        ->get(route('parent.children.timeline', ['child' => $child->id]))
        ->assertOk();
});

// ---------------------------------------------------------------------------
// API timeline tests
// ---------------------------------------------------------------------------

test('API returns timeline structure for a linked child', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    $token = $parent->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk()
        ->assertJsonStructure([
            'child_id',
            'child_name',
            'date',
            'event_count',
            'events',
        ]);
});

test('API timeline returns correct child name and date', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create(['first_name' => 'Sophie', 'last_name' => 'Murphy']);
    linkParent($parent, $child);

    $token = $parent->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline?date=2026-03-20")
        ->assertOk()
        ->assertJsonPath('child_name', 'Sophie Murphy')
        ->assertJsonPath('date', '2026-03-20');
});

test('API timeline is forbidden for an unlinked parent', function () {
    $parent     = User::factory()->create(['role' => 'parent']);
    $otherChild = Child::factory()->create();
    // NOT linked

    $token = $parent->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$otherChild->id}/timeline")
        ->assertForbidden();
});

test('API timeline requires authentication', function () {
    $child = Child::factory()->create();

    $this->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertUnauthorized();
});

test('API timeline includes attendance events', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $room   = Room::factory()->create();
    $child  = Child::factory()->create(['room_id' => $room->id]);
    linkParent($parent, $child);

    Attendance::create([
        'child_id'    => $child->id,
        'date'        => today(),
        'status'      => 'present',
        'room_id'     => $room->id,
        'recorded_by' => $carer->id,
        'check_in_at' => now()->setTime(9, 0),
    ]);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk();

    expect($response->json('event_count'))->toBeGreaterThanOrEqual(1);

    $types = collect($response->json('events'))->pluck('type');
    expect($types)->toContain('attendance');
});

test('API timeline includes daily update events', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    DailyUpdate::create([
        'child_id'   => $child->id,
        'date'       => today(),
        'meals'      => 'Porridge and fruit',
        'sleep'      => 'Napped 11am–12pm',
        'notes'      => 'Great mood today',
        'created_by' => $carer->id,
    ]);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk();

    $types = collect($response->json('events'))->pluck('type');
    expect($types)->toContain('meal')
        ->and($types)->toContain('sleep')
        ->and($types)->toContain('note');
});

test('API timeline includes medication events', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $child  = Child::factory()->create();
    linkParent($parent, $child);

    MedicationLog::create([
        'child_id'        => $child->id,
        'carer_id'        => $carer->id,
        'medication_name' => 'Calpol',
        'dosage'          => '5ml',
        'date'            => today(),
        'time_given'      => '10:30:00',
        'notes'           => 'Mild fever',
    ]);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk();

    $types = collect($response->json('events'))->pluck('type');
    expect($types)->toContain('medication');
});

test('API timeline includes incident events with severity', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $room   = Room::factory()->create();
    $child  = Child::factory()->create(['room_id' => $room->id]);
    linkParent($parent, $child);

    IncidentReport::create([
        'child_id'       => $child->id,
        'carer_id'       => $carer->id,
        'room_id'        => $room->id,
        'incident_date'  => today(),
        'incident_time'  => '14:00:00',
        'title'          => 'Minor fall',
        'description'    => 'Tripped on the mat.',
        'action_taken'   => 'Ice pack applied.',
        'severity'       => 'low',
        'parent_contact_required' => false,
        'status'         => 'open',
    ]);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk();

    $events = collect($response->json('events'));
    $incident = $events->firstWhere('type', 'incident');

    expect($incident)->not->toBeNull()
        ->and($incident['severity'])->toBe('low');
});

test('API timeline events are sorted chronologically', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $room   = Room::factory()->create();
    $child  = Child::factory()->create(['room_id' => $room->id]);
    linkParent($parent, $child);

    // check-in at 09:00 → first; meal defaults to 12:00 → second
    Attendance::create([
        'child_id'    => $child->id,
        'date'        => today(),
        'status'      => 'present',
        'room_id'     => $room->id,
        'recorded_by' => $carer->id,
        'check_in_at' => now()->setTime(9, 0),
    ]);

    DailyUpdate::create([
        'child_id'   => $child->id,
        'date'       => today(),
        'meals'      => 'Lunch served',
        'created_by' => $carer->id,
    ]);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/parent/children/{$child->id}/timeline")
        ->assertOk();

    $events = $response->json('events');
    expect(count($events))->toBeGreaterThanOrEqual(2);

    // Attendance (09:00) must come before meal (12:00)
    $attendanceIdx = collect($events)->search(fn ($e) => $e['type'] === 'attendance');
    $mealIdx       = collect($events)->search(fn ($e) => $e['type'] === 'meal');
    expect($attendanceIdx)->toBeLessThan($mealIdx);
});
