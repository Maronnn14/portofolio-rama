<?php

namespace Tests\Feature\Api;

use App\Models\PersonalInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalInfoApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_can_view_personal_info()
    {
        PersonalInfo::create(['key' => 'name', 'value' => 'Rama']);
        PersonalInfo::create(['key' => 'role', 'value' => 'Developer']);

        $response = $this->getJson('/api/personal-info');

        $response->assertOk()
            ->assertJson([
                'name' => 'Rama',
                'role' => 'Developer',
            ]);
    }
}
