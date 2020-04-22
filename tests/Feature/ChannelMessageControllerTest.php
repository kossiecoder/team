<?php

namespace Tests\Feature;

use App\Events\ChatChannelSent;
use App\Models\Message;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelMessageControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * GET: /api/channel-messages/{id}
     * @test
     */
    public function canGetMessagesForChannelChat()
    {
        $user = factory(User::class)->create();
        factory(User::class)->create();

        $this->actingAs($user, 'api');
        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $channel = $this->createChannel([]);

        $channelId = $channel->decodeResponseJson()['channel']['id'];
        $message = $this->faker->sentence;
        $noOfMessages = config('messages.no_of_messages_load_more', Message::NUM_MESSAGES_DEFAULT);
        $extraMessages = 10;


        //insert messages
        for ($i = 0; $i < $noOfMessages + $extraMessages; $i++) {
            $this->json('POST', '/api/channel-messages', [
                'message'   =>  $message,
                'channel_id'    => $channelId
            ]);
        }

        // getting first messages
        $response = $this->json('GET', '/api/channel-messages/'.$channelId, [
            'loadMoreNumber' => 0
        ]);

        $response->assertJsonStructure(['messages', 'noOfMessages'])->assertStatus(Response::HTTP_OK);

        $this->assertCount($noOfMessages, $response->decodeResponseJson()['messages']);

        // getting next messages
        $response = $this->json('GET', '/api/channel-messages/'.$channelId, [
            'loadMoreNumber' => 1
        ]);

        $response->assertJsonStructure(['messages', 'noOfMessages'])->assertStatus(Response::HTTP_OK);

        $this->assertCount($extraMessages, $response->decodeResponseJson()['messages']);

    }

    /**
     * POST: /api/channel-messages
     * @test
     */
    public function canSendMessageToChannel()
    {
        $user   = factory(User::class)->create();
        $user2  = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $channel = $this->createChannel([$user2->id])->decodeResponseJson()['channel'];

        $message = $this->faker->sentence;

        $response = $this->json('POST', '/api/channel-messages', [
            'message'       => $message,
            'channel_id'    => $channel['id']
        ]);

        // when channel message is added, set users' of the channel as unread except the sender
        $this->assertDatabaseHas('channel_user', [
                'user_id'       => $user->id,
                'channel_id'    => $channel['id'],
                'is_read'       => 1
        ]);

        $this->assertDatabaseHas('channel_user', [
            'user_id'       => $user2->id,
            'channel_id'    => $channel['id'],
            'is_read'       => 0
        ]);

        $response->assertJsonStructure(['message'])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('channel_messages', [
            'message'       => $message,
            'channel_id'    => $channel['id'],
            'user_id'       => $user->id
        ]);

        $this->actingAs($user2, 'api');
        $this->json('PUT', '/api/channel-messages/read', [
            'channelId' => $channel['id']
        ]);

        $this->assertDatabaseHas('channel_user', [
            'user_id'       => $user->id,
            'channel_id'    => $channel['id'],
            'is_read'       => 1
        ]);

        $this->assertDatabaseHas('channel_user', [
            'user_id'       => $user2->id,
            'channel_id'    => $channel['id'],
            'is_read'       => 1
        ]);
    }

    /**
     * POST: /api/channel-messages/event
     * @test
     */
    public function canFirePusherEvent()
    {
        $user = factory(User::class)->create();
        factory(User::class)->create();

        $this->actingAs($user, 'api');
        $user->permissions()->attach(Permission::where('code', 'add_chat_channels')->first()->id);
        $channel = $this->createChannel([]);

        $message = $this->faker->sentence;

        $message = $this->json('POST', '/api/channel-messages', [
            'message'   =>  $message,
            'channel_id'    => $channel->decodeResponseJson()['channel']['id']
        ]);
        Event::fake();

        $response = $this->json('POST', '/api/channel-messages/event', [
            'id' => $message->decodeResponseJson()['message']['id']
        ]);

        Event::assertDispatched(ChatChannelSent::class);

        $response->assertExactJson(['success' => true])->assertStatus(Response::HTTP_OK);
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
