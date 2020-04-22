<?php

namespace Tests\Feature;

use App\Events\ChatPrivateSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    
    /**
     * GET: /api/messages/{id}
     * @test
     */
    public function canGetMessagesForPrivateChat()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $message = $this->faker->sentence;
        $noOfMessages = config('messages.no_of_messages_load_more', Message::NUM_MESSAGES_DEFAULT);
        $extraMessages = 10;

        //insert 40 messages
        for ($i = 0; $i < $noOfMessages + $extraMessages; $i++) {
            $this->json('POST', '/api/messages', [
                'message'   =>  $message,
                'to'        => $user2->id
            ]);
        }

        // getting first messages
        $response = $this->json('GET', '/api/messages/'.$user2->id, [
            'loadMoreNumber' => 0
        ]);

        $response->assertJsonStructure(['messages', 'noOfMessages'])->assertStatus(Response::HTTP_OK);

        $this->assertCount($noOfMessages, $response->decodeResponseJson()['messages']);

        // getting next 10 messages
        $response = $this->json('GET', '/api/messages/'.$user2->id, [
            'loadMoreNumber' => 1
        ]);

        $response->assertJsonStructure(['messages', 'noOfMessages'])->assertStatus(Response::HTTP_OK);

        $this->assertCount($extraMessages, $response->decodeResponseJson()['messages']);

    }

    /**
     * POST: /api/messages
     * @test
     */
    public function canSendMessage()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $message = $this->faker->sentence;

        $response = $this->json('POST', '/api/messages', [
            'message'   =>  $message,
            'to'        => $user2->id
        ]);

        $response->assertJsonStructure(['message'])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('messages', [
            'message'   => $message,
            'to'        => $user2->id,
            'from'      => $user->id
        ]);
    }

    /**
     * POST: /api/messages/event
     * @test
     */
    public function canFirePusherEvent()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user, 'api');

        $message = $this->faker->sentence;

        $message = $this->json('POST', '/api/messages', [
            'message'   =>  $message,
            'to'        => $user2->id
        ]);
        Event::fake();

        $response = $this->json('POST', '/api/messages/event', [
            'id' => $message->decodeResponseJson()['message']['id']
        ]);

        Event::assertDispatched(ChatPrivateSent::class);

        $response->assertExactJson(['status' => 'success'])->assertStatus(Response::HTTP_OK);
    }

    /**
     * PUT: /api/messages/read
     * @test
     */
    public function canUpdateMessageReadStatus()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->actingAs($user2, 'api');

        $message = $this->faker->sentence;

        $response = $this->json('POST', '/api/messages', [
            'message'   =>  $message,
            'to'        => $user->id
        ]);
        $messageId = $response->decodeResponseJson()['message']['id'];

        $this->assertDatabaseHas('messages', [
            'id'    => $messageId,
            'is_read'  => false
        ]);

        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/messages/read', [
            'chatUserId' => $user2->id
        ]);

        $response->assertJsonStructure(['success', 'chatUserId']);

        $this->assertDatabaseHas('messages', [
            'id'    => $messageId,
            'is_read'  => true
        ]);
    }
}
