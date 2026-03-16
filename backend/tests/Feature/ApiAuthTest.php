<?php

use App\Models\User;

it('user can register via API', function () {
    $response = $this->postJson('/api/register', [
        'name'                  => 'New User',
        'email'                 => 'newuser@test.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role'                  => 'parent',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role'], 'token']);

    $this->assertDatabaseHas('users', ['email' => 'newuser@test.com', 'role' => 'parent']);
});

it('register fails with missing fields', function () {
    $this->postJson('/api/register', [])->assertStatus(422);
});

it('register fails with duplicate email', function () {
    User::factory()->create(['email' => 'dup@test.com']);

    $this->postJson('/api/register', [
        'name'                  => 'Duplicate',
        'email'                 => 'dup@test.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role'                  => 'parent',
    ])->assertStatus(422);
});

it('user can login via API', function () {
    $user = User::factory()->create([
        'role'     => 'parent',
        'password' => bcrypt('Password123!'),
    ]);

    $response = $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role'], 'token']);
});

it('login fails with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('CorrectPass1!')]);

    $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'WrongPass999!',
    ])->assertStatus(401)
      ->assertJson(['message' => 'Invalid credentials']);
});

it('login fails with non-existent email', function () {
    $this->postJson('/api/login', [
        'email'    => 'nobody@test.com',
        'password' => 'Password123!',
    ])->assertStatus(401);
});

it('authenticated user can get their info', function () {
    $user  = User::factory()->create(['role' => 'parent']);
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/user')
         ->assertStatus(200)
         ->assertJsonFragment(['email' => $user->email, 'name' => $user->name]);
});

it('unauthenticated request to /api/user returns 401', function () {
    $this->getJson('/api/user')->assertStatus(401);
});

it('user can logout via API and token is revoked', function () {
    $user  = User::factory()->create(['role' => 'parent']);
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson('/api/logout')
         ->assertStatus(200)
         ->assertJson(['message' => 'Logged out successfully']);

    // Reset the cached Sanctum guard so the next request re-checks the DB
    $this->app['auth']->forgetGuards();

    // Token should now be invalid
    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/user')
         ->assertStatus(401);
});
