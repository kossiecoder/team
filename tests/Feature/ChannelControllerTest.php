<?php

namespace Tests\Feature;

use App\Events\ChannelCreated;
use App\Models\Channel;
use App\Models\Permission;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * DELETE: /api/channels/{channel}
     * @test
     */
    public function authorisedAdminCanDeleteChannel()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $channel = $this->createChannel([])->decodeResponseJson()['channel'];

        // with no permission to delete channel - 403
        $response = $this->json('DELETE', '/api/channels/'.$channel['id']);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        // with permission
        $user->permissions()->attach(Permission::where('code', 'delete_chat_channels')->first()->id);
        $response = $this->json('DELETE', '/api/channels/'.$channel['id']);
        $this->assertNotNull($response->decodeResponseJson()['channel']['deleted_at']);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * DELETE: /api/channels/{channel}
     * @test
     */
    public function userCanDeleteOwnChannel()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $user->permissions()->attach(Permission::where('code', 'delete_own_chat_channels')->first()->id);

        $channel = $this->createChannel([])->decodeResponseJson()['channel'];

        // user who don't own the channel try to delete - 403
        $this->actingAs(factory(User::class)->create(), 'api');
        $response = $this->json('DELETE', '/api/channels/'.$channel['id']);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        // delete own channel
        $this->actingAs($user, 'api');
        $response = $this->json('DELETE', '/api/channels/'.$channel['id']);
        $this->assertNotNull($response->decodeResponseJson()['channel']['deleted_at']);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * PUT: /api/channels/{channel}
     * @test
     */
    public function authorisedAdminCanEditChannel()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $user->permissions()->attach(Permission::where('code', 'edit_chat_channels')->first()->id);
        $channel = $this->createChannel([])->decodeResponseJson();

        $channel_id = $channel['channel']['id'];

        // channel name update test
        $new_channel_name = 'IT';
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'name'  => $new_channel_name
        ]);
        $this->assertDatabaseHas('channels', [
            'name' => $new_channel_name
        ]);
        $response->assertStatus(Response::HTTP_OK);

        // channel description update test
        $new_channel_description = 'new description';
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'description'  => $new_channel_description
        ]);
        $this->assertDatabaseHas('channels', [
            'description' => $new_channel_description
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * PUT: /api/channels/{channel}
     * @test
     */
    public function userCanEditOwnChannel()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $channel = $this->createChannel([])->decodeResponseJson();

        $channel_id = $channel['channel']['id'];
        $new_channel_name = 'IT';
        $new_channel_description = 'new description';

        /* without edit_own_chat_channels permission */
        // channel name update test
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'name'  => $new_channel_name
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        // channel description update test
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'description'  => $new_channel_description
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        /* with edit_own_chat_channels permission */
        $user->permissions()->attach(Permission::where('code', 'edit_own_chat_channels')->first()->id);
        // channel name update test
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'name'  => $new_channel_name
        ]);
        $this->assertDatabaseHas('channels', [
            'name' => $new_channel_name
        ]);
        $response->assertStatus(Response::HTTP_OK);

        // channel description update test
        $response = $this->json('PUT', '/api/channels/'.$channel_id, [
            'description'  => $new_channel_description
        ]);
        $this->assertDatabaseHas('channels', [
            'description' => $new_channel_description
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * GET: /api/channels/my-channels
     * @test
     */
    public function canGetParticipatedChannels()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $num_channels = 3;

        for ($i = 0; $i < $num_channels; $i++) {
            $this->createChannel([]);
        }

        $response = $this->json('GET', '/api/channels/my-channels');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($num_channels, count($response->decodeResponseJson()['myChannels']));
    }

    /**
     * GET: /api/channels/check
     * @test
     */
    public function canCheckIfChannelAlreadyExistsInDb()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $channelName = $this->faker->firstName;
        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $this->createChannel([], $channelName);

        $response = $this->json('GET', '/api/channels/check', [
            'name' => $channelName
        ]);

        $response->assertExactJson(['status' => true])->assertStatus(Response::HTTP_OK);

        $response = $this->json('GET', '/api/channels/check', [
            'name' => $channelName.'random'
        ]);

        $response->assertExactJson(['status' => false])->assertStatus(Response::HTTP_OK);
    }

    /**
     * GET: /api/channels/{id}
     * @test
     */
    public function canGetChannelInformation()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $channel = $this->createChannel([]);
        $channelId = $channel->decodeResponseJson()['channel']['id'];

        $response = $this->json('GET', '/api/channels/' . $channelId);

        $response->assertJsonStructure(['user', 'users'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($user->id, $response->decodeResponseJson()['user']['id']);
    }

    /**
     * Get: /api/channels/auth
     * @test
     */
    public function checkAuthIfUserHasPermissionToChannel()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user1, 'api');

        $user1->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $channel = $this->createChannel([$user2->id]);
        $channelId = $channel->decodeResponseJson()['channel']['id'];

        // creator of the channel has permission to the channel
        $response = $this->json('GET', '/api/channels/auth/'.$channelId);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(true, $response->decodeResponseJson()['auth']);

        // member of the channel
        Passport::actingAs($user2);
        $response = $this->json('GET', '/api/channels/auth/'.$channelId);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(true, $response->decodeResponseJson()['auth']);
    }

    /**
     * Get: /api/channels/auth
     * @test
     */
    public function checkAuthIfUserHasNoPermissionToChannel(){
        $user1 = factory(User::class)->create();
        $channel = Channel::create([
            'name'          => $this->faker->firstName,
            'description'   => $this->faker->sentence,
            'created_by'    => $user1->id
        ]);
        $this->actingAs(factory(User::class)->create(), 'api');

        $response = $this->json('GET', '/api/channels/auth/'.$channel->id);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals(false, $response->decodeResponseJson()['auth']);
    }

    /**
     * POST: /api/channels
     * @test
     */
    public function canCreateChannel()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $name = $this->faker->unique()->company;
        $description = $this->faker->text;

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $response = $this->json('POST', '/api/channels', [
            'name'          => $name,
            'description'   => $description,
        ]);

        $response->assertJsonStructure(['channel' => ['pivot']])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('channels', [
            'name'          => $name,
            'description'   => $description,
        ])->assertDatabaseHas('channel_user', [
            'user_id' => $user->id
        ]);
    }

    /**
     * POST: /api/channels/add-people
     * @test
     */
    public function canAddPeopleToChannel()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $channel = $this->createChannel([]);
        $channelId = $channel->decodeResponseJson()['channel']['id'];

        $response = $this->json('POST', '/api/channels/add-people', [
            'channelId'     => $channelId,
            'selectedUsers' => [$user1->id, $user2->id]
        ]);

        $response->assertJsonStructure(['users', 'channelId'])
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals(3, count($response->decodeResponseJson()['users']));
    }

    /**
     * POST :/api/channels/created
     * @test
     */
    public function channelCreatedEvent()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);

        $channel = $this->createChannel([$user2->id]);

        $channelId = $channel->decodeResponseJson()['channel']['id'];

        Event::fake();
        $response = $this->json('POST', '/api/channels/created', [
            'channelId' => $channelId
        ]);
        Event::assertDispatched(ChannelCreated::class);

        $response->assertStatus(Response::HTTP_OK);
    }

    private function createChannel(array $selectedUsers, $name = '')
    {
        $name = $name === '' ? $this->faker->unique()->company : $name;
        $description = $this->faker->text;
        return $this->json('POST', '/api/channels', [
            'name'          => $name,
            'description'   => $description,
            'selectedUsers' => $selectedUsers
        ]);
    }
}
