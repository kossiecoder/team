<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    /** @test */
    public function canLogin()
    {
        $response = $this->login();

        $response->assertJsonStructure([
            'token', 'user'
        ])->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function canLogout()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', '/api/auth/logout');

        $response->assertJsonStructure(['message'])->assertStatus(Response::HTTP_OK);
    }

    private function login() {
        $email = $this->faker->email;
        $password = $this->faker->password;
        $first_name = $this->faker->firstName;

        User::create([
            'first_name'    => $first_name,
            'email'         => $email,
            'password'      => Hash::make($password)
        ]);

        return $this->json('POST', '/api/auth/login', [
            'email'     => $email,
            'password'  => $password
        ]);
    }
}
