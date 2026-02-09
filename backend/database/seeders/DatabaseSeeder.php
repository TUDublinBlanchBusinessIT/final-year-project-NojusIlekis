<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
            ]
        );

        // Role test users
        User::updateOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Test Parent',
                'password' => Hash::make('Password123!'),
                'role' => 'parent',
            ]
        );

        User::updateOrCreate(
            ['email' => 'carer@test.com'],
            [
                'name' => 'Test Carer',
                'password' => Hash::make('Password123!'),
                'role' => 'carer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('Password123!'),
                'role' => 'manager',
            ]
        );
    }
}

