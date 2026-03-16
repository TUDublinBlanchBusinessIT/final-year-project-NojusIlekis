<?php

use App\Models\Child;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;

// ---------------------------------------------------------------------------
// Web — Parent
// ---------------------------------------------------------------------------

it('parent can view messages inbox', function () {
    $parent = User::factory()->create(['role' => 'parent']);

    $this->actingAs($parent)
         ->get('/parent/messages')
         ->assertStatus(200);
});

it('parent can send a message to a carer', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $room   = Room::factory()->create();
    $child  = Child::factory()->create(['room_id' => $room->id]);

    $child->parents()->attach($parent->id, ['relationship_type' => 'mother', 'legal_guardian' => true]);
    $carer->rooms()->attach($room->id, ['start_date' => now()->toDateString()]);

    $this->actingAs($parent)
         ->post('/parent/messages', [
             'receiver_id' => $carer->id,
             'body'        => 'Hello from parent',
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'sender_id'   => $parent->id,
        'receiver_id' => $carer->id,
        'body'        => 'Hello from parent',
    ]);
});

it('parent cannot send message with empty body', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);

    $this->actingAs($parent)
         ->post('/parent/messages', [
             'receiver_id' => $carer->id,
             'body'        => '',
         ])
         ->assertSessionHasErrors('body');
});

it('opening a conversation marks messages as read', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);

    $message = Message::create([
        'sender_id'   => $carer->id,
        'receiver_id' => $parent->id,
        'body'        => 'Unread message',
        'read_at'     => null,
    ]);

    $this->actingAs($parent)
         ->get('/parent/messages/' . $carer->id)
         ->assertStatus(200);

    $this->assertNotNull($message->fresh()->read_at);
});

// ---------------------------------------------------------------------------
// Web — Carer
// ---------------------------------------------------------------------------

it('carer can view messages inbox', function () {
    $carer = User::factory()->create(['role' => 'carer']);

    $this->actingAs($carer)
         ->get('/carer/messages')
         ->assertStatus(200);
});

it('carer can reply to a parent message', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);

    Message::create([
        'sender_id'   => $parent->id,
        'receiver_id' => $carer->id,
        'body'        => 'Original message',
    ]);

    $this->actingAs($carer)
         ->post('/carer/messages', [
             'receiver_id' => $parent->id,
             'body'        => 'Reply from carer',
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'sender_id'   => $carer->id,
        'receiver_id' => $parent->id,
        'body'        => 'Reply from carer',
    ]);
});

// ---------------------------------------------------------------------------
// API — Messaging
// ---------------------------------------------------------------------------

it('authenticated user can list conversations via API', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $token  = $parent->createToken('test')->plainTextToken;

    Message::create([
        'sender_id'   => $parent->id,
        'receiver_id' => $carer->id,
        'body'        => 'API test message',
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/messages/conversations')
         ->assertStatus(200)
         ->assertJsonStructure(['data']);
});

it('authenticated user can send message via API', function () {
    $parent   = User::factory()->create(['role' => 'parent']);
    $receiver = User::factory()->create(['role' => 'carer']);
    $token    = $parent->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson('/api/messages', [
             'receiver_id' => $receiver->id,
             'body'        => 'API message body',
         ])
         ->assertStatus(201)
         ->assertJsonStructure(['data' => ['id', 'sender_id', 'receiver_id', 'body']]);

    $this->assertDatabaseHas('messages', [
        'sender_id'   => $parent->id,
        'receiver_id' => $receiver->id,
        'body'        => 'API message body',
    ]);
});

it('authenticated user can get unread count via API', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $token  = $parent->createToken('test')->plainTextToken;

    Message::create([
        'sender_id'   => $carer->id,
        'receiver_id' => $parent->id,
        'body'        => 'Unread',
        'read_at'     => null,
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/messages/unread-count')
         ->assertStatus(200)
         ->assertJson(['count' => 1]);
});

it('opening conversation via API marks messages as read', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $carer  = User::factory()->create(['role' => 'carer']);
    $token  = $parent->createToken('test')->plainTextToken;

    $message = Message::create([
        'sender_id'   => $carer->id,
        'receiver_id' => $parent->id,
        'body'        => 'Unread API message',
        'read_at'     => null,
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/messages/conversations/' . $carer->id)
         ->assertStatus(200);

    $this->assertNotNull($message->fresh()->read_at);
});
