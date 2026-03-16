<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function conversations()
    {
        $userId = auth()->id();

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender:id,name,email,role', 'receiver:id,name,email,role'])
            ->latest()
            ->get();

        $conversations = $messages->groupBy(function ($msg) use ($userId) {
            return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
        })->map(function ($msgs) use ($userId) {
            $last      = $msgs->first();
            $otherUser = $last->sender_id === $userId ? $last->receiver : $last->sender;
            $unread    = $msgs->where('receiver_id', $userId)->whereNull('read_at')->count();

            return [
                'user'         => $otherUser ? [
                    'id'    => $otherUser->id,
                    'name'  => $otherUser->name,
                    'email' => $otherUser->email,
                    'role'  => $otherUser->role,
                ] : null,
                'last_message' => [
                    'body'       => $last->body,
                    'created_at' => $last->created_at,
                ],
                'unread_count' => $unread,
            ];
        })->sortByDesc(fn ($c) => $c['last_message']['created_at'])->values();

        return response()->json(['data' => $conversations]);
    }

    public function show(User $user)
    {
        $authId = auth()->id();

        $messages = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->with([
                'sender:id,name,email,role',
                'receiver:id,name,email,role',
                'child:id,first_name,last_name',
            ])
            ->oldest()
            ->get();

        // Mark unread messages from the other user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['data' => $messages]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body'        => ['required', 'string', 'max:2000'],
            'child_id'    => ['nullable', 'exists:children,id'],
        ]);

        $message = Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'child_id'    => $validated['child_id'] ?? null,
            'body'        => $validated['body'],
        ]);

        $message->load(['sender:id,name,email,role', 'receiver:id,name,email,role', 'child:id,first_name,last_name']);

        return response()->json(['data' => $message], 201);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
