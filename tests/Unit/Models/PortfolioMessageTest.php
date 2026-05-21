<?php

namespace Tests\Unit\Models;

use App\Models\PortfolioMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortfolioMessageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_message()
    {
        $msg = PortfolioMessage::create([
            'message_id' => 'msg_test_001',
            'name' => 'John Doe',
            'message' => 'Great portfolio!',
            'rating' => 5,
            'posted_at_ms' => 1000000,
            'session_token' => 'token_123',
            'hidden' => false,
            'flagged' => false,
            'pinned' => false,
        ]);

        $this->assertDatabaseHas('portfolio_messages', ['message_id' => 'msg_test_001']);
        $this->assertEquals('John Doe', $msg->name);
    }

    #[Test]
    public function it_casts_boolean_fields()
    {
        $msg = PortfolioMessage::create([
            'message_id' => 'msg_test_002',
            'name' => 'Jane',
            'message' => 'Nice work!',
            'rating' => 4,
            'posted_at_ms' => 2000000,
            'session_token' => 'token_456',
            'hidden' => 1,
            'flagged' => 0,
            'pinned' => 1,
        ]);

        $this->assertTrue($msg->getAttribute('hidden'));
        $this->assertFalse($msg->getAttribute('flagged'));
        $this->assertTrue($msg->getAttribute('pinned'));
    }

    #[Test]
    public function it_casts_integer_fields()
    {
        $msg = PortfolioMessage::create([
            'message_id' => 'msg_test_003',
            'name' => 'Bob',
            'message' => 'Awesome!',
            'rating' => '3',
            'posted_at_ms' => '3000000',
            'session_token' => 'token_789',
        ]);

        $this->assertIsInt($msg->rating);
        $this->assertIsInt($msg->posted_at_ms);
        $this->assertEquals(3, $msg->rating);
        $this->assertEquals(3000000, $msg->posted_at_ms);
    }

    #[Test]
    public function it_transforms_to_frontend_array()
    {
        $msg = PortfolioMessage::create([
            'message_id' => 'msg_test_004',
            'name' => 'Alice',
            'message' => 'Love it!',
            'rating' => 5,
            'posted_at_ms' => 4000000,
            'session_token' => 'token_abc',
            'hidden' => false,
            'flagged' => true,
            'pinned' => false,
        ]);

        $frontend = $msg->toFrontendArray();

        $this->assertEquals([
            'id' => 'msg_test_004',
            'name' => 'Alice',
            'message' => 'Love it!',
            'rating' => 5,
            'timestamp' => 4000000,
            'sessionToken' => 'token_abc',
            'hidden' => false,
            'flagged' => true,
            'pinned' => false,
        ], $frontend);
    }
}
