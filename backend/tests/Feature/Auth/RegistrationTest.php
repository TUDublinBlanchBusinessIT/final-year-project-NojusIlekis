<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register as parent and end up pending', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0871234567',
        'address' => '123 Main St',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'parent',
        'child_first_name' => 'Test',
        'child_last_name' => 'Child',
        'child_dob' => now()->subYears(2)->toDateString(),
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('registration.pending'));
    expect(User::where('email', 'test@example.com')->first()?->status)->toBe('pending');
});
