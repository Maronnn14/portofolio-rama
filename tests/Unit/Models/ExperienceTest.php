<?php

namespace Tests\Unit\Models;

use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_experience()
    {
        $exp = Experience::create([
            'role' => 'Developer',
            'company' => 'Tech Co',
            'location' => 'Remote',
            'start_date' => '2023',
            'end_date' => 'Present',
            'type' => 'Full-time',
            'description' => 'Built amazing things',
            'tech' => ['Laravel', 'Vue.js'],
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('experiences', ['role' => 'Developer']);
        $this->assertEquals('Tech Co', $exp->company);
    }

    #[Test]
    public function it_casts_tech_to_array()
    {
        $exp = Experience::create([
            'role' => 'Developer',
            'company' => 'Company',
            'start_date' => '2023',
            'tech' => ['PHP', 'MySQL'],
        ]);

        $this->assertIsArray($exp->tech);
        $this->assertEquals(['PHP', 'MySQL'], $exp->tech);
    }

    #[Test]
    public function it_casts_sort_order_to_integer()
    {
        $exp = Experience::create([
            'role' => 'Tester',
            'company' => 'Test Co',
            'start_date' => '2023',
            'sort_order' => '2',
        ]);

        $this->assertIsInt($exp->sort_order);
        $this->assertEquals(2, $exp->sort_order);
    }

    #[Test]
    public function it_accepts_present_as_end_date()
    {
        $exp = Experience::create([
            'role' => 'Current Job',
            'company' => 'Present Co',
            'start_date' => '2024',
            'end_date' => 'Present',
        ]);

        $this->assertEquals('Present', $exp->end_date);
    }

    #[Test]
    public function it_allows_valid_year_end_date()
    {
        $exp = Experience::factory()->create(['end_date' => '2024']);

        $this->assertEquals('2024', $exp->end_date);
    }
}
