<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Child;
use App\Models\IncidentReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncidentReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_carer_can_create_incident_report()
    {
        $carer = User::factory()->create(['role' => 'carer']);
        $room = Room::factory()->create();
        $child = Child::factory()->create(['room_id' => $room->id]);

        // attach carer to room
        $carer->rooms()->attach($room->id);

        $response = $this->actingAs($carer)->post(route('carer.incident-reports.store'), [
            'child_id' => $child->id,
            'room_id' => $room->id,
            'incident_date' => now()->toDateString(),
            'incident_time' => '11:15',
            'title' => 'Fall during outdoor play',
            'description' => 'Child slipped while running in the outdoor area.',
            'action_taken' => 'Applied plaster and monitored child.',
            'severity' => 'low',
            'parent_contact_required' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('incident_reports', [
            'child_id' => $child->id,
            'carer_id' => $carer->id,
            'title' => 'Fall during outdoor play',
        ]);
    }

    public function test_manager_can_view_incident_reports_page()
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('manager.reports.incidents'));

        $response->assertStatus(200);
    }

    public function test_parent_cannot_access_carer_incident_reports_page()
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $response = $this->actingAs($parent)->get(route('carer.incident-reports.index'));

        $response->assertStatus(403);
    }
}