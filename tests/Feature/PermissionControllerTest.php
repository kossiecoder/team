<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function canUpdatePermission()
    {
        $users = factory(User::class, 2)->create();

        $user1 = $users[0];
        $user2= $users[1];

        $this->actingAs($user1, 'api');

        $do_everything_permisson_id = Permission::where('code', 'do_everything')->first()->id;
        $do_everything_permisson_code = Permission::where('code', 'do_everything')->first()->code;

        $create_channels_permisson_code = Permission::where('code', 'add_chat_channels')->first()->code;
        $create_channels_permisson_id = Permission::where('code', 'add_chat_channels')->first()->id;

        $user1->permissions()->attach($do_everything_permisson_id);

        $this->assertDatabaseHas('permission_user', [
            'user_id'       => $user1->id,
            'permission_id' => $do_everything_permisson_id
        ]);

        // give do everything permission to user2
        $response = $this->json('PUT', '/api/permissions/'.$user2->id, [
            'permissionCode'    => $do_everything_permisson_code,
            'allowed'           => true
        ]);

        $response->assertJsonStructure(['permission_tree_array'])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $do_everything_permisson_id,
            'allowed'       => true
        ]);

        //remove create_channels permission from user2
        $response = $this->json('PUT', '/api/permissions/'.$user2->id, [
            'permissionCode'    => $create_channels_permisson_code,
            'allowed'           => false
        ]);

        $response->assertJsonStructure(['permission_tree_array'])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $create_channels_permisson_id,
            'allowed'       => false
        ]);

        $this->assertDatabaseHas('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $do_everything_permisson_id,
            'allowed'       => true
        ]);

        // remove do_everything permission for user2
        $response = $this->json('PUT', '/api/permissions/'.$user2->id, [
            'permissionCode'    => $do_everything_permisson_code,
            'allowed'           => false
        ]);

        $response->assertJsonStructure(['permission_tree_array'])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $create_channels_permisson_id
        ]);

        $this->assertDatabaseMissing('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $do_everything_permisson_id
        ]);

        // add do everything permission to user2 again
        $response = $this->json('PUT', '/api/permissions/'.$user2->id, [
            'permissionCode'    => $do_everything_permisson_code,
            'allowed'           => true
        ]);

        $response->assertJsonStructure(['permission_tree_array'])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('permission_user', [
            'user_id'       => $user2->id,
            'permission_id' => $do_everything_permisson_id,
            'allowed'       => true
        ]);
    }
}
