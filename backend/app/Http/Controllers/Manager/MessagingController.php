<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'child'])
            ->latest()
            ->get();

        $conversations = $messages->groupBy(function ($msg) use ($userId) {
            return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
        })->map(function ($msgs) use ($userId) {
            $lastMessage = $msgs->first();
            $otherUser   = $lastMessage->sender_id === $userId
                ? $lastMessage->receiver
                : $lastMessage->sender;

            $unreadCount = $msgs->where('receiver_id', $userId)->whereNull('read_at')->count();

            return (object) [
                'user'         => $otherUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        })->filter(fn ($conversation) => $conversation->user && $conversation->user->role === 'parent')
          ->sortByDesc(fn ($c) => $c->last_message->created_at)
          ->values();

        return view('manager.messages.index', compact('conversations'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'parent', 403);

        $authId = auth()->id();

        $messages = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->with('child')
            ->oldest()
            ->get();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('manager.messages.show', compact('messages', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body'        => ['required', 'string', 'max:2000'],
            'child_id'    => ['nullable', 'exists:children,id'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);
        abort_unless($receiver->role === 'parent', 403);

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'child_id'    => $validated['child_id'] ?? null,
            'body'        => $validated['body'],
        ]);

        return redirect()->route('manager.messages.show', $validated['receiver_id'])
            ->with('success', 'Reply sent.');
    }
}