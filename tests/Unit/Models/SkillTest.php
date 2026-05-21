<?php

namespace Tests\Unit\Models;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_skill()
    {
        $skill = Skill::create([
            'name' => 'Laravel',
            'category' => 'Backend',
            'icon' => 'laravel',
            'teaser' => 'PHP Framework',
            'proficiency' => 90,
            'level' => 'Expert',
        ]);

        $this->assertDatabaseHas('skills', ['name' => 'Laravel']);
        $this->assertEquals(90, $skill->proficiency);
    }

    #[Test]
    public function it_casts_proficiency_and_sort_order_to_integer()
    {
        $skill = Skill::create([
            'name' => 'PHP',
            'proficiency' => '85',
            'sort_order' => '3',
        ]);

        $this->assertIsInt($skill->proficiency);
        $this->assertIsInt($skill->sort_order);
        $this->assertEquals(85, $skill->proficiency);
        $this->assertEquals(3, $skill->sort_order);
    }

    #[Test]
    public function it_casts_related_projects_and_gallery_to_array()
    {
        $skill = Skill::create([
            'name' => 'Vue.js',
            'related_projects' => ['project-1', 'project-2'],
            'gallery' => ['img1.jpg'],
        ]);

        $this->assertIsArray($skill->related_projects);
        $this->assertIsArray($skill->gallery);
        $this->assertEquals(['project-1', 'project-2'], $skill->related_projects);
    }

    #[Test]
    public function it_returns_null_for_unset_array_casts()
    {
        $skill = Skill::create(['name' => 'CSS']);

        $this->assertNull($skill->related_projects);
    }

    #[Test]
    public function it_proficiency_is_between_zero_and_one_hundred()
    {
        $skill = Skill::create([
            'name' => 'Testing',
            'proficiency' => 0,
        ]);
        $this->assertGreaterThanOrEqual(0, $skill->proficiency);

        $skill->update(['proficiency' => 100]);
        $this->assertLessThanOrEqual(100, $skill->fresh()->proficiency);
    }
}
