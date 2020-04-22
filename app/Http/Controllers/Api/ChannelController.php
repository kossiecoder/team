<?php

namespace App\Http\Controllers\Api;

use App\Events\ChannelCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Channel\ChannelDeleteRequest;
use App\Http\Requests\Channel\ChannelStoreRequest;
use App\Http\Requests\Channel\ChannelUpdateRequest;
use App\Models\Channel;
use Illuminate\Http\Response;

class ChannelController extends Controller
{
    public function store(ChannelStoreRequest $request)
    {
        $user = auth()->user();


        $channel = Channel::create([
            'name'          => $request['name'],
            'description'   => $request['description'],
            'created_by'    => $user->id
        ]);

        $channel->users()->attach($user->id);

        $channel->users()->attach($request['selectedUsers']);

        return response()->json([
            'channel' => $user->channels()->where('id', $channel->id)->withPivot('is_read')->first()
        ], Response::HTTP_CREATED);
    }

    public function update(ChannelUpdateRequest $request, Channel $channel)
    {
        $channel->update($request->toArray());
        return response()->json([
            'success'   => true,
            'message'   => 'channel has been updated successfully',
            'channel'   => $channel
        ], Response::HTTP_OK);
    }

    public function destroy(ChannelDeleteRequest $request, Channel $channel)
    {
        $channel->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the channel',
            'channel' => $channel
        ], Response::HTTP_OK);
    }

    public function myChannel()
    {
        $my_channels = auth()->user()->channels()->withPivot('is_read')->get();

        return response()->json([
            'myChannels' => $my_channels
        ], Response::HTTP_OK);
    }

    public function getChannelInformation($id)
    {
        return response()->json(Channel::with('users', 'user')->where('id', $id)->first(), Response::HTTP_OK);
    }

    public function addPeople()
    {
        $channel = Channel::find(request('channelId'));

        $channel->users()->attach(request('selectedUsers'));

        return response()->json([
            'status'    => 'success',
            'message'   => 'You have successfully added people to the channel.',
            'users'      => $channel->users,
            'channelId' => request('channelId')
        ], Response::HTTP_CREATED);
    }

    public function checkAuth($channelId)
    {
        return response()->json([
            'auth' => auth()->user()->channels()->wherePivot('channel_id', $channelId)->exists()
        ], Response::HTTP_OK);
    }

    public function checkChannelExists()
    {
        return response()->json([
            'status' => Channel::where('name', request('name'))->exists()
        ], Response::HTTP_OK);
    }

    public function sendChannelCreatedEvent()
    {
        $channel_id = request('channelId');

        $channel = Channel::find($channel_id);

        ChannelCreated::dispatch($channel);

        return response()->json([
            'success' => true,
            'channel' => $channel
        ], Response::HTTP_OK);
    }
}
