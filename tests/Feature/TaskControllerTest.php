<?php

namespace Tests\Feature;

use App\Events\TaskUpdated as TaskUpdatedEvent;
use App\Models\Category;
use App\Models\PriorityLevel;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskUpdated;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
	use WithFaker, RefreshDatabase;
    /**
     * GET: /api/tasks
     * @test
     */
	public function canGetTasksThatIFollow()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user, 'api');

		$this->json('POST', '/api/tasks', [
            'category_id'   => '',
            'assignee_id'   => $user->id,
            'priority_level_id' => '',
            'title'         => $this->faker->sentence,
            'content'       => $this->faker->paragraph
        ]);

		$response = $this->json('GET', '/api/tasks');

		$response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'tasks' => [
                'current_page', 'data'
            ]
        ]);

		$this->assertEquals($user->followingTasks()->count(), count($response->decodeResponseJson()['tasks']['data']));
    }

	/**
	 * GET: /api/tasks/{task}
	 * @test
	 */
	public function canGetParticularTask()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user, 'api');

        $response = $this->json('POST', '/api/tasks', [
            'category_id'   => '',
            'assignee_id'   => $user->id,
            'priority_level_id' => '',
            'title'         => $this->faker->sentence,
            'content'       => $this->faker->paragraph
        ]);
		var_dump($response->decodeResponseJson());
	}

    /**
     * POST: /api/tasks
     * @test
     */
	public function canCreateTask()
	{
        Notification::fake();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $category = factory(Category::class)->create();
        $priority_level = factory(PriorityLevel::class)->create();

        $this->actingAs($user, 'api');

        $data = [];

        // with category_id and assignee_id
        $data[] = [
            'category_id'       => $category->id,
            'assignee_id'       => $user2->id,
            'priority_level_id' => $priority_level->id,
            'title'             => $this->faker->sentence,
            'content'           => $this->faker->paragraph
        ];

        // without category_id and assignee_id
        $data[] = [
            'category_id'   => '',
            'assignee_id'   => '',
            'priority_level_id' => $priority_level->id,
            'title'         => $this->faker->sentence,
            'content'       => $this->faker->paragraph
        ];

        // without category_id
        $data[] = [
            'category_id'   => '',
            'assignee_id'   => $user2->id,
            'priority_level_id' => $priority_level->id,
            'title'         => $this->faker->sentence,
            'content'       => $this->faker->paragraph
        ];

        // without assignee_id
        $data[] = [
            'category_id'   => $category->id,
            'assignee_id'   => '',
            'priority_level_id' => $priority_level->id,
            'title'         => $this->faker->sentence,
            'content'       => $this->faker->paragraph
        ];

        foreach ($data as $value) {
            $response = $this->json('POST', '/api/tasks', $value);

            $response->assertStatus(Response::HTTP_CREATED)->assertJsonStructure(['task']);

            $task = $response->decodeResponseJson()['task'];

            $this->assertDatabaseHas('tasks', [
                'id' => $task['id']
            ]);

            Task::find($task['id'])->followers()->attach($task['creator_id']);

            $this->assertDatabaseHas('task_user', [
                'user_id' => $task['creator_id'],
                'task_id' => $task['id']
            ]);

            if($value['assignee_id']) {
                $this->assertDatabaseHas('task_user', [
                    'user_id' => $value['assignee_id'],
                    'task_id' => $task['id']
                ]);
                Notification::assertSentTo($user2, TaskAssigned::class);
            }
        }
    }
    /**
     * PUT: /api/tasks/{task}
     * @test
     */
    public function canUpdateTaskDetailsWithoutNewAssignee() {
        Notification::fake();
        Event::fake();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $priority_level = factory(PriorityLevel::class)->create();
        $task = factory(Task::class)->create([
            'assignee_id'           => $user2->id,
            'priority_level_id'     => $priority_level->id
        ]);



        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/tasks/'.$task->id, [
            'title'         => $this->faker->sentence
        ]);

        Notification::assertNothingSent();

        Notification::assertSentTo($task->followers()->wherePivot('user_id', '!=', auth()->id())->get(), TaskUpdated::class);

        Event::assertDispatched(TaskUpdatedEvent::class);
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['success']);

        $this->assertDatabaseHas('tasks', [
            'id'            => $task->id,
            'assignee_id'   => $user2->id
        ]);
    }
    /**
     * PUT: /api/tasks/{task}
     * @test
     */
	public function canUpdateTaskDetailsWithNewAssignee()
	{
        Notification::fake();
        Event::fake();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'assignee_id' => $user2->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/tasks/'.$task->id, [
            'assignee_id'   => $user->id,
            'title'         => $this->faker->sentence
        ]);

        if(!$task->followers()->wherePivot('user_id', $user->id)->exists()) {
            $this->assertDatabaseHas('task_user', [
                'user_id' => $user2->id,
                'task_id' => $task->id
            ]);
        }

        Notification::assertSentTo($user, TaskAssigned::class);

        Notification::assertSentTo($task->followers()->wherePivot('user_id', '!=', auth()->id())->get(), TaskUpdated::class);

        Event::assertDispatched(TaskUpdatedEvent::class);

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['success']);

        $this->assertDatabaseHas('tasks', [
            'id'            => $task->id,
            'assignee_id'   => $user->id
        ]);
    }

    /**
     * DELETE: /api/tasks/{task}
     * @test
     */
	public function canDeleteTask()
	{
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create([
            'assignee_id' => $user->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->json('DELETE', '/api/tasks/'.$task->id);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }
}
