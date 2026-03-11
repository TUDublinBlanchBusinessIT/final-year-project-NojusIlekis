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

        // ----------------------------
        // Rooms
        // ----------------------------
        $roomA = Room::updateOrCreate(['name' => 'Bumblebees'], ['name' => 'Bumblebees']);
        $roomB = Room::updateOrCreate(['name' => 'Ladybirds'], ['name' => 'Ladybirds']);

        // ----------------------------
        // Assign carer to rooms (pivot room_user)
        // ----------------------------
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $carer->id],
            ['room_id' => $roomA->id, 'user_id' => $carer->id, 'start_date' => now()->toDateString()]
        );

        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomB->id, 'user_id' => $carer->id],
            ['room_id' => $roomB->id, 'user_id' => $carer->id, 'start_date' => now()->toDateString()]
        );

        // ----------------------------
        // Children (split across rooms)
        // ----------------------------
        $childrenData = [
            ['first_name' => 'Mia',  'last_name' => 'Kelly',   'room_id' => $roomA->id, 'parent' => $parent],
            ['first_name' => 'Noah', 'last_name' => 'Byrne',   'room_id' => $roomA->id, 'parent' => $parent],
            ['first_name' => 'Lily', 'last_name' => 'Murphy',  'room_id' => $roomA->id, 'parent' => $parent2],
            ['first_name' => 'Jack', 'last_name' => 'Walsh',   'room_id' => $roomA->id, 'parent' => $parent2],

            ['first_name' => 'Ella', 'last_name' => 'Doyle',   'room_id' => $roomB->id, 'parent' => $parent],
            ['first_name' => 'Leo',  'last_name' => 'Ryan',    'room_id' => $roomB->id, 'parent' => $parent],
            ['first_name' => 'Ava',  'last_name' => "O'Brien", 'room_id' => $roomB->id, 'parent' => $parent2],
            ['first_name' => 'Finn', 'last_name' => 'Nolan',   'room_id' => $roomB->id, 'parent' => $parent2],
        ];

        $childModels = [];
        foreach ($childrenData as $c) {
            $child = Child::updateOrCreate(
                [
                    'first_name' => $c['first_name'],
                    'last_name'  => $c['last_name'],
                    'room_id'    => $c['room_id'],
                ],
                [
                    'first_name' => $c['first_name'],
                    'last_name'  => $c['last_name'],
                    'room_id'    => $c['room_id'],
                ]
            );

            // Link parent via child_parent pivot
            DB::table('child_parent')->updateOrInsert(
                ['child_id' => $child->id, 'parent_id' => $c['parent']->id],
                ['child_id' => $child->id, 'parent_id' => $c['parent']->id, 'legal_guardian' => true]
            );

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
                    'child_id'    => $child->id,
                    'date'        => $today,
                    'status'      => 'present',
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
