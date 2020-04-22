<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatPrivateSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Models\Message;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function index($id)
    {
        $loadMoreNumber = request('loadMoreNumber');
        $noOfMessages = config('messages.no_of_messages_load_more', 30);

        $messages = Message::with('from', 'to')->where(function($q) use ($id) {
            $q->where('from', auth()->id());
            $q->where('to', $id);
        })->orWhere(function($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        })->latest()->skip($noOfMessages * $loadMoreNumber)->take($noOfMessages)->get();

        return response()->json([
           'messages'       => $messages,
           'noOfMessages'   => $noOfMessages
        ], Response::HTTP_OK);
    }

    public function store(MessageStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['from'] = auth()->id();

        $validated['to'] = request('to');

        if(auth()->id() == request('to')) {
            $validated['is_read'] = true;
        }

        $message = Message::create($validated)->load('from', 'to');

        ChatPrivateSent::dispatch($message);

        return response()->json([
            'message' => $message,
        ], Response::HTTP_CREATED);
    }

    public function updateMessagesToRead()
    {
        $chatUserId = request('chatUserId');
        Message::where('to', auth()->id())
            ->where('from', $chatUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return response()->json([
            'success' => true,
            'chatUserId' => $chatUserId
        ], Response::HTTP_OK);
    }
}
