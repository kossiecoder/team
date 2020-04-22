<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatChannelSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelMessage\ChannelMessageStoreRequest;
use App\Models\Channel;
use App\Models\ChannelMessage;
use App\Models\Message;
use Illuminate\Http\Response;

class ChannelMessageController extends Controller
{
    public function index($id)
    {
        $loadMoreNumber = request('loadMoreNumber');
        $noOfMessages = config('messages.no_of_messages_load_more', Message::NUM_MESSAGES_DEFAULT);

        if(auth()->user()->channels()->wherePivot('channel_id', $id)->exists()) {
            $messages = ChannelMessage::with('user')->where('channel_id', $id)->latest()->skip($noOfMessages * $loadMoreNumber)->take($noOfMessages)->get();

            return response()->json([
                'messages'       => $messages,
                'noOfMessages'   => $noOfMessages
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function store(ChannelMessageStoreRequest $request)
    {
        $auth_id = auth()->id();
        $validated = $request->validated();
        $channel_id = $validated['channel_id'];
        $validated['user_id'] = $auth_id;

        $message = ChannelMessage::create($validated)->load('user');

        Channel::find($channel_id)->updateMessageStatusToNotRead($channel_id);

        ChatChannelSent::dispatch($message);

        return response()->json([
            'message' => $message
        ], Response::HTTP_CREATED);
    }

    public function updateChannelMessagesToRead()
    {
        $channelId = request('channelId');

        auth()->user()->channels()->updateExistingPivot($channelId, ['is_read' => 1]);

        return response()->json([
            'success' => true,
            'channelId' => $channelId
        ], Response::HTTP_OK);
    }
}
