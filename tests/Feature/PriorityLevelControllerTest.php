<?php

namespace Tests\Feature;

use App\Models\PriorityLevel;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriorityLevelControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * GET: /api/priority-levels
     * @test
     */
    public function canGetPriorityLevels()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $response = $this->json('GET', '/api/priority-levels');

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['priorityLevels']);
    }
}
