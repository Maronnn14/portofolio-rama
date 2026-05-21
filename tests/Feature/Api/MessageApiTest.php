<?php

namespace Tests\Feature\Api;

use App\Models\PortfolioMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): string
    {
        $user = User::factory()->create();
        return $user->createToken('admin-token', ['admin:write'])->plainTextToken;
    }

    #[Test]
    public function guests_can_list_visible_messages()
    {
        PortfolioMessage::factory()->create(['hidden' => false, 'message_id' => 'msg_001']);
        PortfolioMessage::factory()->create(['hidden' => true, 'message_id' => 'msg_002']);

        $response = $this->getJson('/api/messages');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertEquals('msg_001', $response->json()[0]['id']);
    }

    #[Test]
    public function admins_see_all_messages()
    {
        $token = $this->actingAsAdmin();
        PortfolioMessage::factory()->create(['hidden' => false, 'message_id' => 'msg_001']);
        PortfolioMessage::factory()->create(['hidden' => true, 'message_id' => 'msg_002']);

        $response = $this->withToken($token)->getJson('/api/messages');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function guests_can_create_message()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Test User',
            'message' => 'This is a test message',
            'rating' => 5,
        ]);

        $response->assertCreated()
            ->assertJson([
                'name' => 'Test User',
                'message' => 'This is a test message',
                'rating' => 5,
            ]);
    }

    #[Test]
    public function message_creation_requires_name_and_message()
    {
        $response = $this->postJson('/api/messages', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'message']);
    }

    #[Test]
    public function message_validation_limits_name_to_100_chars()
    {
        $response = $this->postJson('/api/messages', [
            'name' => str_repeat('a', 101),
            'message' => 'Valid message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function message_validation_limits_message_to_1000_chars()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Test',
            'message' => str_repeat('a', 1001),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    #[Test]
    public function rating_is_optional_and_defaults_to_zero()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Test',
            'message' => 'No rating',
        ]);

        $response->assertCreated();
        $this->assertEquals(0, $response->json()['rating']);
    }

    #[Test]
    public function rating_must_be_between_zero_and_five()
    {
        $response = $this->postJson('/api/messages', [
            'name' => 'Test',
            'message' => 'Bad rating',
            'rating' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    #[Test]
    public function message_owner_can_update_with_session_token()
    {
        $msg = PortfolioMessage::factory()->create([
            'message_id' => 'msg_update_test',
            'session_token' => 'valid-session-token',
        ]);

        $response = $this->putJson('/api/messages/msg_update_test', [
            'message' => 'Updated message content',
            'session_token' => 'valid-session-token',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated message content', $response->json()['message']);
    }

    #[Test]
    public function message_owner_cannot_update_without_session_token()
    {
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_no_token',
            'session_token' => 'secret',
        ]);

        $response = $this->putJson('/api/messages/msg_no_token', [
            'message' => 'Hacked content',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function message_owner_cannot_update_with_wrong_session_token()
    {
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_wrong_token',
            'session_token' => 'correct-token',
        ]);

        $response = $this->putJson('/api/messages/msg_wrong_token', [
            'message' => 'Hacked content',
            'session_token' => 'wrong-token',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_update_any_message()
    {
        $token = $this->actingAsAdmin();
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_admin_update',
        ]);

        $response = $this->withToken($token)
            ->putJson('/api/messages/msg_admin_update', [
                'message' => 'Admin updated',
                'hidden' => true,
                'pinned' => true,
            ]);

        $response->assertOk();
        $this->assertTrue($response->json()['hidden']);
        $this->assertTrue($response->json()['pinned']);
    }

    #[Test]
    public function admin_cannot_update_message_with_non_boolean_admin_fields()
    {
        $token = $this->actingAsAdmin();
        PortfolioMessage::factory()->create(['message_id' => 'msg_invalid_admin']);

        $response = $this->withToken($token)
            ->putJson('/api/messages/msg_invalid_admin', [
                'hidden' => 'not-a-boolean',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hidden']);
    }

    #[Test]
    public function non_admin_cannot_set_hidden_flag()
    {
        $msg = PortfolioMessage::factory()->create([
            'message_id' => 'msg_nonadmin_hidden',
            'session_token' => 'token',
        ]);

        $response = $this->putJson('/api/messages/msg_nonadmin_hidden', [
            'message' => 'Normal update',
            'hidden' => true,
            'session_token' => 'token',
        ]);

        $response->assertOk();
        $this->assertFalse($response->json()['hidden']);
    }

    #[Test]
    public function message_owner_can_delete_with_session_token()
    {
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_delete_owner',
            'session_token' => 'delete-token',
        ]);

        $response = $this->deleteJson('/api/messages/msg_delete_owner', [
            'session_token' => 'delete-token',
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('portfolio_messages', ['message_id' => 'msg_delete_owner']);
    }

    #[Test]
    public function message_owner_cannot_delete_without_session_token()
    {
        PortfolioMessage::factory()->create(['message_id' => 'msg_delete_no_token']);

        $response = $this->deleteJson('/api/messages/msg_delete_no_token');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_delete_any_message()
    {
        $token = $this->actingAsAdmin();
        PortfolioMessage::factory()->create(['message_id' => 'msg_admin_delete']);

        $response = $this->withToken($token)
            ->deleteJson('/api/messages/msg_admin_delete');

        $response->assertOk();
        $this->assertDatabaseMissing('portfolio_messages', ['message_id' => 'msg_admin_delete']);
    }

    #[Test]
    public function deleting_nonexistent_message_returns_404()
    {
        $response = $this->deleteJson('/api/messages/nonexistent_msg');

        $response->assertNotFound();
    }

    #[Test]
    public function admin_can_bulk_delete_messages()
    {
        $token = $this->actingAsAdmin();
        PortfolioMessage::factory()->create(['message_id' => 'msg_bulk_1']);
        PortfolioMessage::factory()->create(['message_id' => 'msg_bulk_2']);

        $response = $this->withToken($token)
            ->deleteJson('/api/messages/bulk', [
                'ids' => ['msg_bulk_1', 'msg_bulk_2'],
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('portfolio_messages', ['message_id' => 'msg_bulk_1']);
        $this->assertDatabaseMissing('portfolio_messages', ['message_id' => 'msg_bulk_2']);
    }

    #[Test]
    public function messages_are_ordered_by_pinned_then_by_date_desc()
    {
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_old',
            'pinned' => false,
            'posted_at_ms' => 1000,
        ]);
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_new',
            'pinned' => false,
            'posted_at_ms' => 3000,
        ]);
        PortfolioMessage::factory()->create([
            'message_id' => 'msg_pinned',
            'pinned' => true,
            'posted_at_ms' => 2000,
        ]);

        $response = $this->getJson('/api/messages');
        $ids = collect($response->json())->pluck('id')->toArray();

        $this->assertEquals(['msg_pinned', 'msg_new', 'msg_old'], $ids);
    }

    #[Test]
    public function updating_nonexistent_message_returns_404()
    {
        $response = $this->putJson('/api/messages/nonexistent', [
            'message' => 'test',
        ]);

        $response->assertNotFound();
    }
}
