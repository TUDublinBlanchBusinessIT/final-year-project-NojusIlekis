<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\DailyUpdate;
use App\Models\IncidentReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------
        // Users
        // ----------------------------
        $parent = User::updateOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Test Parent',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $carer = User::updateOrCreate(
            ['email' => 'carer@test.com'],
            [
                'name' => 'Test Carer',
                'password' => Hash::make('Password123!'),
                'role' => 'carer',
                'email_verified_at' => now(),
            ]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('Password123!'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        $parent2 = User::updateOrCreate(
            ['email' => 'parent2@test.com'],
            [
                'name' => 'Test Parent 2',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $parent3 = User::updateOrCreate(
            ['email' => 'parent3@test.com'],
            [
                'name' => 'Emma Byrne',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $parent4 = User::updateOrCreate(
            ['email' => 'parent4@test.com'],
            [
                'name' => 'Liam Murphy',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $parent5 = User::updateOrCreate(
            ['email' => 'parent5@test.com'],
            [
                'name' => 'Grace Walsh',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $carer2 = User::updateOrCreate(
            ['email' => 'carer2@test.com'],
            [
                'name' => 'Sarah Collins',
                'password' => Hash::make('Password123!'),
                'role' => 'carer',
                'email_verified_at' => now(),
            ]
        );

        $carer3 = User::updateOrCreate(
            ['email' => 'carer3@test.com'],
            [
                'name' => 'James O\'Connor',
                'password' => Hash::make('Password123!'),
                'role' => 'carer',
                'email_verified_at' => now(),
            ]
        );

        // ----------------------------
        // Rooms
        // ----------------------------
        $roomA = Room::updateOrCreate(['name' => 'Bumblebees'], ['name' => 'Bumblebees']);
        $roomB = Room::updateOrCreate(['name' => 'Ladybirds'], ['name' => 'Ladybirds']);
        $roomC = Room::updateOrCreate(['name' => 'Caterpillars'], ['name' => 'Caterpillars']);

        // ----------------------------
        // Assign carers to rooms (pivot room_user)
        // ----------------------------
        // carer: Bumblebees + Ladybirds
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $carer->id],
            ['room_id' => $roomA->id, 'user_id' => $carer->id, 'start_date' => now()->toDateString()]
        );
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomB->id, 'user_id' => $carer->id],
            ['room_id' => $roomB->id, 'user_id' => $carer->id, 'start_date' => now()->toDateString()]
        );

        // carer2 (Sarah Collins): Bumblebees
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $carer2->id],
            ['room_id' => $roomA->id, 'user_id' => $carer2->id, 'start_date' => now()->toDateString()]
        );

        // carer3 (James O'Connor): Caterpillars
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomC->id, 'user_id' => $carer3->id],
            ['room_id' => $roomC->id, 'user_id' => $carer3->id, 'start_date' => now()->toDateString()]
        );

        // ----------------------------
        // Children
        // ----------------------------
        $childrenData = [
            // Bumblebees (ages ~2–4)
            ['first_name' => 'Mia',    'last_name' => 'Kelly',    'dob' => '2021-04-12', 'room_id' => $roomA->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent,  'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Noah',   'last_name' => 'Byrne',    'dob' => '2021-08-03', 'room_id' => $roomA->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent,  'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Lily',   'last_name' => 'Murphy',   'dob' => '2020-12-20', 'room_id' => $roomA->id, 'allergies' => 'Peanuts', 'medical_notes' => 'Carries EpiPen at all times.', 'parent1' => $parent2, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Jack',   'last_name' => 'Walsh',    'dob' => '2021-02-15', 'room_id' => $roomA->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent2, 'rel1' => 'father', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Oliver', 'last_name' => 'Davis',    'dob' => '2021-06-30', 'room_id' => $roomA->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent3, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Sophie', 'last_name' => 'Mitchell', 'dob' => '2021-09-18', 'room_id' => $roomA->id, 'allergies' => 'Dairy',   'medical_notes' => 'Lactose intolerant.',          'parent1' => $parent3, 'rel1' => 'mother', 'parent2' => $parent4, 'rel2' => 'father'],

            // Ladybirds (ages ~3–5)
            ['first_name' => 'Ella',   'last_name' => 'Doyle',    'dob' => '2020-05-08', 'room_id' => $roomB->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent,  'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Leo',    'last_name' => 'Ryan',     'dob' => '2020-03-22', 'room_id' => $roomB->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent,  'rel1' => 'guardian', 'parent2' => null,  'rel2' => null],
            ['first_name' => 'Ava',    'last_name' => "O'Brien",  'dob' => '2019-11-14', 'room_id' => $roomB->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent2, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Finn',   'last_name' => 'Nolan',    'dob' => '2020-07-01', 'room_id' => $roomB->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent2, 'rel1' => 'father', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Charlie','last_name' => 'White',    'dob' => '2020-10-25', 'room_id' => $roomB->id, 'allergies' => null,      'medical_notes' => 'Mild asthma, has inhaler.',   'parent1' => $parent4, 'rel1' => 'father', 'parent2' => $parent5, 'rel2' => 'mother'],
            ['first_name' => 'Isla',   'last_name' => 'Brown',    'dob' => '2021-01-09', 'room_id' => $roomB->id, 'allergies' => 'Eggs',    'medical_notes' => null,                          'parent1' => $parent5, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],

            // Caterpillars (babies/toddlers, ages 0–2)
            ['first_name' => 'Ethan',  'last_name' => 'Martin',   'dob' => '2023-04-18', 'room_id' => $roomC->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent5, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Poppy',  'last_name' => 'Clarke',   'dob' => '2023-09-02', 'room_id' => $roomC->id, 'allergies' => null,      'medical_notes' => null,                          'parent1' => $parent2, 'rel1' => 'mother', 'parent2' => null,    'rel2' => null],
            ['first_name' => 'Hugo',   'last_name' => 'Sullivan',  'dob' => '2024-02-14', 'room_id' => $roomC->id, 'allergies' => 'Gluten', 'medical_notes' => 'Coeliac disease diagnosed.',   'parent1' => $parent3, 'rel1' => 'mother', 'parent2' => $parent4, 'rel2' => 'father'],
        ];

        $childModels = [];
        foreach ($childrenData as $c) {
            $child = Child::updateOrCreate(
                [
                    'first_name' => $c['first_name'],
                    'last_name'  => $c['last_name'],
                ],
                [
                    'first_name'    => $c['first_name'],
                    'last_name'     => $c['last_name'],
                    'dob'           => $c['dob'],
                    'room_id'       => $c['room_id'],
                    'allergies'     => $c['allergies'],
                    'medical_notes' => $c['medical_notes'],
                ]
            );

            // Link first parent
            DB::table('child_parent')->updateOrInsert(
                ['child_id' => $child->id, 'parent_id' => $c['parent1']->id],
                [
                    'child_id'          => $child->id,
                    'parent_id'         => $c['parent1']->id,
                    'relationship_type' => $c['rel1'],
                    'legal_guardian'    => true,
                ]
            );

            // Link second parent if provided
            if ($c['parent2']) {
                DB::table('child_parent')->updateOrInsert(
                    ['child_id' => $child->id, 'parent_id' => $c['parent2']->id],
                    [
                        'child_id'          => $child->id,
                        'parent_id'         => $c['parent2']->id,
                        'relationship_type' => $c['rel2'],
                        'legal_guardian'    => true,
                    ]
                );
            }

            $childModels[] = $child;
        }

        // ----------------------------
        // Sample attendance + daily updates for today
        // ----------------------------
        $today = Carbon::today()->toDateString();

        foreach ($childModels as $child) {
            Attendance::updateOrCreate(
                ['child_id' => $child->id, 'date' => $today],
                [
                    'status'      => 'present',
                    'room_id'     => $child->room_id,
                    'recorded_by' => $carer->id,
                ]
            );

            DailyUpdate::updateOrCreate(
                ['child_id' => $child->id, 'date' => $today],
                [
                    'child_id'   => $child->id,
                    'date'       => $today,
                    'meals'      => 'Breakfast eaten well, good appetite.',
                    'sleep'      => 'Napped for 45 minutes.',
                    'notes'      => 'Had a great day and played nicely with others.',
                    'created_by' => $carer->id,
                ]
            );
        }

        // ----------------------------
        // Sample incident reports
        // ----------------------------
        if (!empty($childModels)) {
            IncidentReport::updateOrCreate(
                [
                    'child_id' => $childModels[0]->id,
                    'incident_date' => now()->toDateString(),
                    'incident_time' => '11:15:00',
                    'title' => 'Fall during outdoor play',
                ],
                [
                    'carer_id' => $carer->id,
                    'room_id' => $childModels[0]->room_id,
                    'description' => 'Child slipped while running in the outdoor area and grazed left knee.',
                    'action_taken' => 'Cleaned wound, applied plaster, monitored child for 20 minutes.',
                    'severity' => 'low',
                    'parent_contact_required' => true,
                    'status' => 'open',
                ]
            );

            if (isset($childModels[1])) {
                IncidentReport::updateOrCreate(
                    [
                        'child_id' => $childModels[1]->id,
                        'incident_date' => now()->toDateString(),
                        'incident_time' => '12:40:00',
                        'title' => 'Bumped head on table edge',
                    ],
                    [
                        'carer_id' => $carer->id,
                        'room_id' => $childModels[1]->room_id,
                        'description' => 'Child stood up quickly and bumped forehead on the corner of a table.',
                        'action_taken' => 'Applied cold compress and observed child closely for 30 minutes.',
                        'severity' => 'medium',
                        'parent_contact_required' => true,
                        'status' => 'open',
                    ]
                );
            }

            if (isset($childModels[2])) {
                IncidentReport::updateOrCreate(
                    [
                        'child_id' => $childModels[2]->id,
                        'incident_date' => now()->toDateString(),
                        'incident_time' => '14:10:00',
                        'title' => 'Possible allergic reaction',
                    ],
                    [
                        'carer_id' => $carer->id,
                        'room_id' => $childModels[2]->room_id,
                        'description' => 'Child developed mild redness around mouth after afternoon snack.',
                        'action_taken' => 'Snack removed, child monitored, manager informed immediately.',
                        'severity' => 'high',
                        'parent_contact_required' => true,
                        'status' => 'open',
                    ]
                );
            }
        }
    }
}
