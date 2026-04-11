<?php

use App\Models\User;
use App\Models\Child;
use App\Models\Invoice;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Helper: create an invoice for a parent
|--------------------------------------------------------------------------
*/
function createTestInvoice(User $parent, Child $child, string $status = 'sent', string $paymentStatus = 'unpaid'): Invoice
{
    return Invoice::create([
        'child_id' => $child->id,
        'parent_id' => $parent->id,
        'period_start' => now()->startOfMonth(),
        'period_end' => now()->endOfMonth(),
        'due_date' => now()->addDays(14),
        'total' => 850.00,
        'discount' => 0,
        'status' => $status,
        'payment_status' => $paymentStatus,
    ]);
}

/*
|--------------------------------------------------------------------------
| Model Tests
|--------------------------------------------------------------------------
*/

test('invoice canSubmitPayment returns true for sent unpaid invoice', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'unpaid');

    expect($invoice->canSubmitPayment())->toBeTrue();
});

test('invoice canSubmitPayment returns false for draft invoice', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'draft', 'unpaid');

    expect($invoice->canSubmitPayment())->toBeFalse();
});

test('invoice canSubmitPayment returns true for rejected invoice', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'rejected');

    expect($invoice->canSubmitPayment())->toBeTrue();
});

test('invoice canBeEdited returns false after payment submitted', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'payment_submitted');

    expect($invoice->canBeEdited())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Parent Payment Tests
|--------------------------------------------------------------------------
*/

test('parent can submit payment proof', function () {
    Storage::fake('public');

    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child);

    $response = $this->actingAs($parent)
        ->post(route('parent.invoices.pay', $invoice), [
            'payment_proof' => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
            'payment_notes' => 'Bank ref: 12345',
        ]);

    $response->assertRedirect();

    $invoice->refresh();
    expect($invoice->payment_status)->toBe('payment_submitted');
    expect($invoice->payment_notes)->toBe('Bank ref: 12345');
    expect($invoice->payment_proof_path)->not->toBeNull();
    expect($invoice->payment_submitted_at)->not->toBeNull();
});

test('parent cannot submit payment on draft invoice', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'draft');

    $response = $this->actingAs($parent)
        ->post(route('parent.invoices.pay', $invoice), [
            'payment_proof' => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
        ]);

    $response->assertStatus(422);

    $invoice->refresh();
    expect($invoice->payment_status)->toBe('unpaid');
});

test('parent cannot submit payment on another parents invoice', function () {
    $parent1 = User::factory()->create(['role' => 'parent']);
    $parent2 = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent1, $child);

    $response = $this->actingAs($parent2)
        ->post(route('parent.invoices.pay', $invoice), [
            'payment_proof' => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
        ]);

    $response->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| Manager Approval Tests
|--------------------------------------------------------------------------
*/

test('manager can approve payment', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'payment_submitted');

    $response = $this->actingAs($manager)
        ->post(route('manager.invoices.approve-payment', $invoice));

    $response->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe('paid');
    expect($invoice->payment_status)->toBe('approved');
    expect($invoice->payment_approved_at)->not->toBeNull();
    expect($invoice->payment_approved_by)->toBe($manager->id);
});

test('manager can reject payment with reason', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'payment_submitted');

    $response = $this->actingAs($manager)
        ->post(route('manager.invoices.reject-payment', $invoice), [
            'rejection_reason' => 'Amount does not match invoice total',
        ]);

    $response->assertRedirect();

    $invoice->refresh();
    expect($invoice->payment_status)->toBe('rejected');
    expect($invoice->rejection_reason)->toBe('Amount does not match invoice total');
    expect($invoice->status)->toBe('sent');
});

test('manager cannot approve non-pending payment', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'sent', 'unpaid');

    $response = $this->actingAs($manager)
        ->post(route('manager.invoices.approve-payment', $invoice));

    $response->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| Cancel Tests
|--------------------------------------------------------------------------
*/

test('manager can cancel invoice', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child);

    $response = $this->actingAs($manager)
        ->post(route('manager.invoices.cancel', $invoice));

    $response->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe('cancelled');
});

test('manager cannot cancel paid invoice', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child, 'paid', 'approved');

    $response = $this->actingAs($manager)
        ->post(route('manager.invoices.cancel', $invoice));

    $response->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| API Tests
|--------------------------------------------------------------------------
*/

test('API parent can submit payment', function () {
    Storage::fake('public');

    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    $invoice = createTestInvoice($parent, $child);

    $token = $parent->createToken('test')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson("/api/parent/invoices/{$invoice->id}/pay", [
            'payment_proof' => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
            'payment_notes' => 'Paid via Revolut',
        ]);

    $response->assertOk()
        ->assertJsonPath('payment_status', 'payment_submitted');
});

test('API manager can view pending payments', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $parent = User::factory()->create(['role' => 'parent']);
    $child = Child::factory()->create();
    createTestInvoice($parent, $child, 'sent', 'payment_submitted');

    $token = $manager->createToken('test')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->getJson('/api/manager/payments/pending');

    $response->assertOk()
        ->assertJsonPath('count', 1);
});
