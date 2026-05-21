<?php

namespace Tests\Unit\Models;

use App\Models\Interest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InterestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_interest()
    {
        $interest = Interest::create([
            'name' => 'Photography',
            'icon' => 'https://cdn-icons-png.flaticon.com/128/1042/1042344.png',
            'description' => 'Capturing moments',
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('interests', ['name' => 'Photography']);
        $this->assertEquals('https://cdn-icons-png.flaticon.com/128/1042/1042344.png', $interest->icon);
    }

    #[Test]
    public function it_casts_sort_order_to_integer()
    {
        $interest = Interest::create([
            'name' => 'Music',
            'sort_order' => '2',
        ]);

        $this->assertIsInt($interest->sort_order);
        $this->assertEquals(2, $interest->sort_order);
    }

    #[Test]
    public function it_allows_null_icon_and_description()
    {
        $interest = Interest::create([
            'name' => 'Coding',
        ]);

        $this->assertNull($interest->icon);
        $this->assertNull($interest->description);
    }

    #[Test]
    public function it_orders_by_sort_order()
    {
        Interest::create(['name' => 'Second', 'sort_order' => 2]);
        Interest::create(['name' => 'First', 'sort_order' => 1]);
        Interest::create(['name' => 'Third', 'sort_order' => 3]);

        $interests = Interest::orderBy('sort_order')->get();

        $this->assertEquals('First', $interests[0]->name);
        $this->assertEquals('Second', $interests[1]->name);
        $this->assertEquals('Third', $interests[2]->name);
    }
}
