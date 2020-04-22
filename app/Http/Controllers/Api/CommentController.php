<?php

namespace App\Http\Controllers\Api;

use App\Events\TaskCommentSubmitted as TaskCommentSubmittedEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use App\Notifications\TaskCommentSubmitted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Notification;

class CommentController extends Controller
{
    public function store()
    {
        $validated = request()->validate([
            'task_id'   => 'required|integer',
            'user_id'   => 'nullable|integer',
            'content'   => 'required',
        ]);

        $comment = Comment::create($validated);
        if($comment->user) {
            Notification::send($comment->task()->first()->followersExceptAuthUser()->get(), new TaskCommentSubmitted($comment));
        }
        TaskCommentSubmittedEvent::dispatch($comment);

        return response()->json([
            'comment' => $comment->load('user')
        ], Response::HTTP_CREATED);
    }
}
