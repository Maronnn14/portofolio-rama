<?php

namespace Tests\Unit\Models;

use App\Models\PersonalInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalInfoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_personal_info()
    {
        $info = PersonalInfo::create([
            'key' => 'name',
            'value' => 'Rama',
        ]);

        $this->assertDatabaseHas('personal_info', [
            'key' => 'name',
            'value' => 'Rama',
        ]);
        $this->assertEquals('Rama', $info->value);
    }

    #[Test]
    public function it_can_get_all_as_key_value_map()
    {
        PersonalInfo::create(['key' => 'name', 'value' => 'Rama']);
        PersonalInfo::create(['key' => 'role', 'value' => 'Developer']);

        $all = PersonalInfo::getAll();

        $this->assertEquals([
            'name' => 'Rama',
            'role' => 'Developer',
        ], $all);
    }

    #[Test]
    public function it_returns_empty_array_when_no_records()
    {
        $this->assertEquals([], PersonalInfo::getAll());
    }

    #[Test]
    public function it_fillable_fields_are_mass_assignable()
    {
        $info = PersonalInfo::create([
            'key' => 'email',
            'value' => 'test@example.com',
        ]);

        $this->assertEquals('email', $info->key);
        $this->assertEquals('test@example.com', $info->value);
    }

    #[Test]
    public function it_can_update_existing_key()
    {
        PersonalInfo::create(['key' => 'name', 'value' => 'Rama']);

        PersonalInfo::where('key', 'name')->update(['value' => 'Updated']);

        $this->assertDatabaseHas('personal_info', [
            'key' => 'name',
            'value' => 'Updated',
        ]);
    }

    #[Test]
    public function it_can_use_update_or_create()
    {
        PersonalInfo::updateOrCreate(
            ['key' => 'name'],
            ['value' => 'Rama']
        );

        PersonalInfo::updateOrCreate(
            ['key' => 'name'],
            ['value' => 'Updated']
        );

        $count = PersonalInfo::where('key', 'name')->count();
        $this->assertEquals(1, $count);
        $this->assertEquals('Updated', PersonalInfo::getAll()['name']);
    }

    #[Test]
    public function it_uses_correct_table()
    {
        $info = new PersonalInfo();
        $this->assertEquals('personal_info', $info->getTable());
    }
}
