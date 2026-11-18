<?php

namespace Tests\Feature\Api;

use App\Models\PortfolioMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_can_list_visible_messages()
    {
        PortfolioMessage::factory()->create(['hidden' => false, 'name' => 'Visible Message']);
        PortfolioMessage::factory()->create(['hidden' => true, 'name' => 'Hidden Message']);

        $response = $this->getJson('/api/messages');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertEquals('Visible Message', $response->json()[0]['name']);
    }

    #[Test]
    public function guests_can_create_message()
    {
        $payload = [
            'name' => 'Alice',
            'message' => 'Great portfolio!',
            'rating' => 5,
            'session_token' => 'token-123',
        ];

        $response = $this->postJson('/api/messages', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('portfolio_messages', [
            'name' => 'Alice',
            'message' => 'Great portfolio!',
            'rating' => 5,
            'session_token' => 'token-123',
        ]);
    }

    #[Test]
    public function message_creation_requires_name_and_message()
    {
        $response = $this->postJson('/api/messages', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'message']);
    }

    #[Test]
    public function message_validation_limits_name_to_100_chars()
    {
        $response = $this->postJson('/api/messages', [
            'name' => str_repeat('a', 101),
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function message_validation_limits_message_to_1000_chars()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Bob',
            'message' => str_repeat('a', 1001),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    #[Test]
    public function rating_is_optional_and_defaults_to_zero()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Charlie',
            'message' => 'Nice work',
        ]);

        $response->assertCreated();
        $this->assertEquals(0, $response->json('rating'));
    }

    #[Test]
    public function rating_must_be_between_zero_and_five()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'David',
            'message' => 'Cool',
            'rating' => 6,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['rating']);
    }

    #[Test]
    public function message_owner_can_update_with_session_token()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-abc',
            'message' => 'Initial message',
        ]);

        $response = $this->putJson("/api/messages/{$message->message_id}", [
            'message' => 'Updated message',
            'session_token' => 'token-abc',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('portfolio_messages', [
            'id' => $message->id,
            'message' => 'Updated message',
        ]);
    }

    #[Test]
    public function message_owner_cannot_update_without_session_token()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-abc',
            'message' => 'Initial message',
        ]);

        $response = $this->putJson("/api/messages/{$message->message_id}", [
            'message' => 'Updated message',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function message_owner_cannot_update_with_wrong_session_token()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-abc',
            'message' => 'Initial message',
        ]);

        $response = $this->putJson("/api/messages/{$message->message_id}", [
            'message' => 'Updated message',
            'session_token' => 'wrong-token',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function non_admin_cannot_set_hidden_flag()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-abc',
            'hidden' => false,
        ]);

        $response = $this->putJson("/api/messages/{$message->message_id}", [
            'session_token' => 'token-abc',
            'hidden' => true,
        ]);

        $response->assertOk();
        $this->assertFalse($message->fresh()->hidden);
    }

    #[Test]
    public function message_owner_can_delete_with_session_token()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-to-delete',
        ]);

        $response = $this->deleteJson("/api/messages/{$message->message_id}", [
            'session_token' => 'token-to-delete',
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('portfolio_messages', ['id' => $message->id]);
    }

    #[Test]
    public function message_owner_cannot_delete_without_session_token()
    {
        $message = PortfolioMessage::factory()->create([
            'session_token' => 'token-abc',
        ]);

        $response = $this->deleteJson("/api/messages/{$message->message_id}");

        $response->assertForbidden();
    }

    #[Test]
    public function deleting_nonexistent_message_returns_404()
    {
        $response = $this->deleteJson('/api/messages/nonexistent-id', [
            'session_token' => 'any-token',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function messages_are_ordered_by_pinned_then_by_date_desc()
    {
        $older = PortfolioMessage::factory()->create([
            'posted_at_ms' => 1000,
            'pinned' => false,
        ]);
        $newer = PortfolioMessage::factory()->create([
            'posted_at_ms' => 2000,
            'pinned' => false,
        ]);
        $pinnedOlder = PortfolioMessage::factory()->create([
            'posted_at_ms' => 500,
            'pinned' => true,
        ]);

        $response = $this->getJson('/api/messages');

        $response->assertOk();
        $ids = array_column($response->json(), 'id');

        $this->assertEquals([$pinnedOlder->message_id, $newer->message_id, $older->message_id], $ids);
    }

    #[Test]
    public function updating_nonexistent_message_returns_404()
    {
        $response = $this->putJson('/api/messages/nonexistent-id', [
            'session_token' => 'any-token',
            'message' => 'Hello',
        ]);

        $response->assertNotFound();
    }
}
