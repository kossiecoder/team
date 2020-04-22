<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('privateChat_{userId}', function ($user, $userId) {
    return (int) $user->id == (int) $userId;
});

Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('channelChat_{channelId}', function ($user, $channelId) {
    return $user->channels()->wherePivot('channel_id', $channelId)->exists();
});

Broadcast::channel('Task.{taskId}', function ($user, $taskId) {
    return $user->followingTasks()->wherePivot('task_id', $taskId)->exists();
});

Broadcast::channel('Task.Comment.{taskId}', function ($user, $taskId) {
    return $user->followingTasks()->wherePivot('task_id', $taskId)->exists();
});
