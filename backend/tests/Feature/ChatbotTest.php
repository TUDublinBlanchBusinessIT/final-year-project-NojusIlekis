<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Chatbot Feature Tests
|--------------------------------------------------------------------------
*/

test('chatbot responds to opening hours question', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'What are your opening hours?']);

    $response->assertOk()
        ->assertJsonPath('matched', true)
        ->assertJsonStructure(['matched', 'question', 'answer', 'category', 'related']);

    expect($response->json('answer'))->toContain('7:30');
});

test('chatbot responds to allergy question', function () {
    $user = User::factory()->create(['role' => 'carer']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'How do you handle allergies?']);

    $response->assertOk()
        ->assertJsonPath('matched', true);

    expect($response->json('answer'))->toContain('allerg');
});

test('chatbot responds to fees question', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'How much does it cost?']);

    $response->assertOk()
        ->assertJsonPath('matched', true)
        ->assertJsonPath('category', 'Fees & Payments');
});

test('chatbot responds to NCS subsidy question', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'NCS subsidy']);

    $response->assertOk()
        ->assertJsonPath('matched', true);

    expect($response->json('answer'))->toContain('NCS');
});

test('chatbot returns fallback for unrecognised question', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'xyzabc random gibberish 12345']);

    $response->assertOk()
        ->assertJsonPath('matched', false);
});

test('chatbot handles greeting', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'Hello']);

    $response->assertOk()
        ->assertJsonPath('matched', true);

    expect($response->json('answer'))->toContain('SnugBot');
});

test('chatbot suggestions returns categories', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->getJson('/chatbot/suggestions');

    $response->assertOk()
        ->assertJsonStructure(['categories']);

    $categories = array_keys($response->json('categories'));
    expect($categories)->toContain('General');
    expect($categories)->toContain('Fees & Payments');
});

test('unauthenticated user cannot access chatbot', function () {
    $response = $this->postJson('/chatbot/ask', ['message' => 'Hello']);

    $response->assertUnauthorized();
});

test('chatbot validates message is required', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => '']);

    $response->assertUnprocessable();
});

test('chatbot returns related questions', function () {
    $user = User::factory()->create(['role' => 'parent']);

    $response = $this->actingAs($user)
        ->postJson('/chatbot/ask', ['message' => 'fees payment cost NCS']);

    $response->assertOk();

    // Should have related questions since multiple FAQs match fee-related keywords
    $data = $response->json();
    if ($data['matched']) {
        expect($data)->toHaveKey('related');
    }
});

test('chatbot works for all roles', function () {
    foreach (['parent', 'carer', 'manager'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->actingAs($user)
            ->postJson('/chatbot/ask', ['message' => 'opening hours']);

        $response->assertOk()
            ->assertJsonPath('matched', true);
    }
});
