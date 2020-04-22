<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskUpdated;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Events\TaskUpdated as TaskUpdatedEvent;

class TaskController extends Controller
{
	public function index()
	{
		return response()->json([
			'tasks' => auth()->user()->followingTasks()->latest()->with('assignee')->paginate(10)
		], Response::HTTP_OK);
    }

	public function show(Task $task)
	{
	    abort_if(!$task->isFollowedBy(auth()->id()), Response::HTTP_FORBIDDEN);

        return response()->json([
            'success' => true,
            'task' => $task->load(['creator', 'assignee', 'comments.user'])
        ], Response::HTTP_OK);
    }

	public function store(TaskStoreRequest $request)
	{
	    $validated = $request->validated();

	    $auth_id = auth()->id();

	    $validated['creator_id'] = $auth_id;

        $task = Task::create($validated);

        $task->followers()->attach($auth_id);

        if($task->assignee_id) {
            $task->followers()->attach($task->assignee_id);
            User::find($task->assignee_id)->notify(new TaskAssigned($task));
        }

		return response()->json([
            'task' => $task->load(['assignee'])
        ], Response::HTTP_CREATED);
    }

	public function update(TaskUpdateRequest $request, Task $task)
	{
        $validated = $request->validated();

        $old_assignee_id = $task->assignee_id;

        $task->update($validated);

	    if(isset($validated['assignee_id']) && $validated['assignee_id'] != $old_assignee_id) {
            User::find($validated['assignee_id'])->notify(new TaskAssigned($task));
            if(!$task->isFollowedBy($validated['assignee_id'])) {
                $task->followers()->attach($validated['assignee_id']);
            }
        }
        $taskFollowers = $task->followersExceptAuthUser()->get();

        Notification::send($taskFollowers, new TaskUpdated($task));

        TaskUpdatedEvent::dispatch($task);

        return response()->json([
            'success'   => true,
            'task'      => $task
        ], Response::HTTP_OK);
    }

	public function destroy(Task $task)
	{
	    $task->delete();

        return response()->json([
            'task' => $task
        ], Response::HTTP_OK);
    }

    public function detachFollowers()
    {

    }
}
