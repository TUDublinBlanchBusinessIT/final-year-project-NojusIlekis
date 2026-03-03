<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\DailyUpdate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------
        // Users (Sprint 1 logins)
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

        // Extra parents (useful for testing)
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
        // Uses DB directly so it works even if you haven't added relationships yet.
        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomA->id, 'user_id' => $carer->id],
            ['room_id' => $roomA->id, 'user_id' => $carer->id]
        );

        DB::table('room_user')->updateOrInsert(
            ['room_id' => $roomB->id, 'user_id' => $carer->id],
            ['room_id' => $roomB->id, 'user_id' => $carer->id]
        );

        // ----------------------------
        // Children (split across rooms)
        // ----------------------------
        $children = [
            ['first_name' => 'Mia',  'last_name' => 'Kelly',   'room_id' => $roomA->id, 'parent_user_id' => $parent->id],
            ['first_name' => 'Noah', 'last_name' => 'Byrne',   'room_id' => $roomA->id, 'parent_user_id' => $parent->id],
            ['first_name' => 'Lily', 'last_name' => 'Murphy',  'room_id' => $roomA->id, 'parent_user_id' => $parent2->id],
            ['first_name' => 'Jack', 'last_name' => 'Walsh',   'room_id' => $roomA->id, 'parent_user_id' => $parent2->id],

            ['first_name' => 'Ella', 'last_name' => 'Doyle',   'room_id' => $roomB->id, 'parent_user_id' => $parent->id],
            ['first_name' => 'Leo',  'last_name' => 'Ryan',    'room_id' => $roomB->id, 'parent_user_id' => $parent->id],
            ['first_name' => 'Ava',  'last_name' => 'O’Brien', 'room_id' => $roomB->id, 'parent_user_id' => $parent2->id],
            ['first_name' => 'Finn', 'last_name' => 'Nolan',   'room_id' => $roomB->id, 'parent_user_id' => $parent2->id],
        ];

        $childModels = [];
        foreach ($children as $c) {
            $childModels[] = Child::updateOrCreate(
                [
                    'first_name' => $c['first_name'],
                    'last_name'  => $c['last_name'],
                    'room_id'    => $c['room_id'],
                ],
                $c
            );
        }

        // ----------------------------
        // OPTIONAL: sample attendance + daily updates for today
        // (remove this block if you want empty lists by default)
        // ----------------------------
        $today = Carbon::today()->toDateString();

        foreach ($childModels as $child) {
            Attendance::updateOrCreate(
                ['child_id' => $child->id, 'date' => $today],
                [
                    'child_id' => $child->id,
                    'date' => $today,
                    'status' => 'present',
                    'recorded_by' => $carer->id,
                ]
            );

            DailyUpdate::updateOrCreate(
                ['child_id' => $child->id, 'date' => $today],
                [
                    'child_id' => $child->id,
                    'date' => $today,
                    'meals' => 'Breakfast eaten well, good appetite.',
                    'sleep' => 'Napped for 45 minutes.',
                    'notes' => 'Had a great day and played nicely with others.',
                    'created_by' => $carer->id,
                ]
            );
        }
    }
}


