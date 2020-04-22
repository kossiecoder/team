<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * GET: /api/users
     * @test
     */
    public function canGetAllUsers()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'manage_users')->first()->id);
        $response = $this->json('GET', '/api/users');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()['users']));

        $extra_users_count = 10;

        for ($i = 0; $i < $extra_users_count; $i++) {
            factory(User::class)->create();
        }

        $response = $this->json('GET', '/api/users');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1 + $extra_users_count, count($response->decodeResponseJson()['users']));

    }
    
    /**
     * GET: /api/users/me
     * @test
     */
    public function canGetLoggedInUserDetail()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $response = $this->json('GET', '/api/users/me');

        $response->assertJsonStructure(['me'])->assertStatus(Response::HTTP_OK);

        $this->assertEquals($user->id, $response->decodeResponseJson()['me']['id']);
    }

    /**
     * PATCH: /api/users/{user}
     * @test
     */
    public function canUpdateProfileDetails()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $this->faker->email;
        $password = $this->faker->password;
        $password_confirmation = $this->faker->password;

        $response = $this->json('PATCH','/api/users/'.$user->id, [
            'first_name'            => $firstName,
            'last_name'             => $lastName,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password
        ]);

        $response->assertJsonStructure([
            'user' => [
                'first_name',
                'last_name',
                'email'
            ]
        ])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => $email
        ]);

        // password not matching
        $response = $this->json('PATCH','/api/users/'.$user->id, [
            'first_name'            => $firstName,
            'last_name'             => $lastName,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password_confirmation
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * DELETE: /api/users/{user}
     * @test
     */
    public function onlyAuthorisedAdminCanDeleteUsers()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user1, 'api');

        // without permission - 403 error
        $response = $this->json('DELETE', '/api/users/'.$user2->id);

        $response->assertStatus(403);

        $user1->permissions()->attach(Permission::where('code', 'delete_users')->first()->id);

        // with permission - successfully delete user
        $response = $this->json('DELETE', '/api/users/'.$user2->id);

        $this->assertDatabaseHas('users', [
            'id'            => $user2->id,
            'deleted_at'    => $response->decodeResponseJson()['user']['deleted_at']
        ]);

        $response->assertStatus(200);
    }
}
