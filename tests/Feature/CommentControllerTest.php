<?php

namespace Tests\Feature;

use App\Events\TaskCommentSubmitted as TaskCommentSubmittedEvent;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCommentSubmitted;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;

class CommentControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * PUT: /api/comments/{comment}
     * @test
     */
    public function canSubmitComment()
    {
        Notification::fake();
        Event::fake();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'assignee_id' => $user2->id,
        ]);

        $task->followers()->attach([$user->id, $user2->id]);

        $this->actingAs($user, 'api');


        $content = $this->faker->paragraph;

        $response = $this->json('POST', '/api/comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => $content
        ]);

        $comment = $response->decodeResponseJson()['comment'];

        $response->assertStatus(Response::HTTP_CREATED)->assertJsonStructure(['comment']);

        $this->assertDatabaseHas('comments', [
            'id'        => $comment['id'],
            'user_id'   => $comment['user_id'],
            'task_id'   => $comment['task_id'],
            'content'   => $content
        ]);
        Event::assertDispatched(TaskCommentSubmittedEvent::class);
        Notification::assertSentTo($task->followers()->wherePivot('user_id', '!=', $user->id)->get(), TaskCommentSubmitted::class);
    }
}
