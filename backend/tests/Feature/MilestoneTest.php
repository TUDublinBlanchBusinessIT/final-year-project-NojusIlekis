<?php

use App\Models\User;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\ChildMilestone;

/*
|--------------------------------------------------------------------------
| Helper: seed some test milestones
|--------------------------------------------------------------------------
*/
function seedTestMilestones(): void
{
    $categories = ['wellbeing', 'identity-belonging', 'communicating', 'exploring-thinking'];
    $ageRanges  = ['0-12', '12-24', '24-36'];

    $i = 0;
    foreach ($categories as $cat) {
        foreach ($ageRanges as $range) {
            Milestone::create([
                'name'             => ucfirst($cat) . ' milestone ' . $range,
                'description'      => 'Test milestone for ' . $cat,
                'category'         => $cat,
                'age_range_months' => $range,
                'sort_order'       => $i++,
            ]);
        }
    }
}

/*
|--------------------------------------------------------------------------
| Model Tests
|--------------------------------------------------------------------------
*/

test('milestone model has correct category labels', function () {
    expect(Milestone::CATEGORIES)
        ->toHaveKey('wellbeing')
        ->toHaveKey('identity-belonging')
        ->toHaveKey('communicating')
        ->toHaveKey('exploring-thinking');
});

test('child age_range returns correct range', function () {
    $child = Child::factory()->create([
        'dob' => now()->subMonths(18)->toDateString(),
    ]);

    expect($child->age_range)->toBe('12-24');
});

test('child milestoneProgress returns correct counts', function () {
    seedTestMilestones();

    $child = Child::factory()->create([
        'dob' => now()->subMonths(18)->toDateString(),
    ]);

    $progress = $child->milestoneProgress();
    // 1 milestone per category in the 12-24 range = 4 total
    expect($progress['total'])->toBe(4)
        ->and($progress['achieved'])->toBe(0)
        ->and($progress['percentage'])->toEqual(0);
});

/*
|--------------------------------------------------------------------------
| Carer Milestone Tests
|--------------------------------------------------------------------------
*/

test('carer can view milestones for a child', function () {
    seedTestMilestones();
    $carer = User::factory()->create(['role' => 'carer']);
    $child = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);

    $this->actingAs($carer)
        ->get(route('carer.milestones.show', $child))
        ->assertOk();
});

test('carer can observe a milestone for a child', function () {
    seedTestMilestones();
    $carer     = User::factory()->create(['role' => 'carer']);
    $child     = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $milestone = Milestone::where('age_range_months', '12-24')->first();

    $this->actingAs($carer)
        ->post(route('carer.milestones.toggle', [$child, $milestone]), [
            'notes' => 'Observed during playtime',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('child_milestones', [
        'child_id'     => $child->id,
        'milestone_id' => $milestone->id,
        'observed_by'  => $carer->id,
    ]);
});

test('carer can uncheck an observed milestone', function () {
    seedTestMilestones();
    $carer     = User::factory()->create(['role' => 'carer']);
    $child     = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $milestone = Milestone::where('age_range_months', '12-24')->first();

    ChildMilestone::create([
        'child_id'     => $child->id,
        'milestone_id' => $milestone->id,
        'observed_by'  => $carer->id,
        'observed_at'  => now()->toDateString(),
    ]);

    $this->actingAs($carer)
        ->post(route('carer.milestones.toggle', [$child, $milestone]))
        ->assertRedirect();

    $this->assertDatabaseMissing('child_milestones', [
        'child_id'     => $child->id,
        'milestone_id' => $milestone->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Parent Milestone Tests
|--------------------------------------------------------------------------
*/

test('parent can view milestones for their own child', function () {
    seedTestMilestones();
    $parent = User::factory()->create(['role' => 'parent']);
    $child  = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $child->parents()->attach($parent->id, [
        'relationship_type' => 'parent',
        'legal_guardian'    => true,
    ]);

    $this->actingAs($parent)
        ->get(route('parent.milestones.show', $child))
        ->assertOk();
});

test('parent cannot view milestones for another parents child', function () {
    seedTestMilestones();
    $parent     = User::factory()->create(['role' => 'parent']);
    $otherChild = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    // Not linked to this parent

    $this->actingAs($parent)
        ->get(route('parent.milestones.show', $otherChild))
        ->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| API Tests
|--------------------------------------------------------------------------
*/

test('API returns milestones for a child', function () {
    seedTestMilestones();
    $carer = User::factory()->create(['role' => 'carer']);
    $child = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $token = $carer->createToken('test')->plainTextToken;

    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->getJson("/api/children/{$child->id}/milestones")
        ->assertOk()
        ->assertJsonPath('child_id', $child->id)
        ->assertJsonStructure([
            'child_id', 'age_range', 'progress', 'overall_progress', 'milestones',
        ]);
});

test('API carer can toggle a milestone', function () {
    seedTestMilestones();
    $carer     = User::factory()->create(['role' => 'carer']);
    $child     = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $milestone = Milestone::where('age_range_months', '12-24')->first();
    $token     = $carer->createToken('test')->plainTextToken;

    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson("/api/children/{$child->id}/milestones/{$milestone->id}/toggle")
        ->assertCreated()
        ->assertJsonPath('achieved', true);

    $this->assertDatabaseHas('child_milestones', [
        'child_id'     => $child->id,
        'milestone_id' => $milestone->id,
    ]);
});

test('API parent cannot toggle milestones', function () {
    seedTestMilestones();
    $parent    = User::factory()->create(['role' => 'parent']);
    $child     = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $milestone = Milestone::where('age_range_months', '12-24')->first();
    $token     = $parent->createToken('test')->plainTextToken;

    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson("/api/children/{$child->id}/milestones/{$milestone->id}/toggle")
        ->assertForbidden();
});

test('milestone progress updates after observation', function () {
    seedTestMilestones();
    $carer     = User::factory()->create(['role' => 'carer']);
    $child     = Child::factory()->create(['dob' => now()->subMonths(18)->toDateString()]);
    $milestone = Milestone::where('age_range_months', '12-24')->first();

    expect($child->milestoneProgress()['achieved'])->toBe(0);

    ChildMilestone::create([
        'child_id'     => $child->id,
        'milestone_id' => $milestone->id,
        'observed_by'  => $carer->id,
        'observed_at'  => now()->toDateString(),
    ]);

    expect($child->fresh()->milestoneProgress()['achieved'])->toBe(1);
});
