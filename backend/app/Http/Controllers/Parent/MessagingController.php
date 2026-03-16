<?php

namespace App\Http\Controllers\Parent;

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
            ->with(['sender', 'receiver'])
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
        })->sortByDesc(fn ($c) => $c->last_message->created_at)->values();

        return view('parent.messages.index', compact('conversations'));
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
            ->with('child')
            ->oldest()
            ->get();

        // Mark all unread messages FROM the other user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $myChildren = auth()->user()->children()->get();

        return view('parent.messages.show', compact('messages', 'user', 'myChildren'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body'        => ['required', 'string', 'max:2000'],
            'child_id'    => ['nullable', 'exists:children,id'],
        ]);

        if ($validated['child_id'] ?? null) {
            $linked = auth()->user()->children()->where('children.id', $validated['child_id'])->exists();
            abort_unless($linked, 403);
        }

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'child_id'    => $validated['child_id'] ?? null,
            'body'        => $validated['body'],
        ]);

        return redirect()->route('parent.messages.show', $validated['receiver_id'])
            ->with('success', 'Message sent.');
    }

    public function create()
    {
        $parent = auth()->user();

        // Room IDs from parent's children
        $roomIds = $parent->children()
            ->with('room')
            ->get()
            ->pluck('room_id')
            ->filter()
            ->unique();

        // Carers actively assigned to those rooms
        $carers = User::where('role', 'carer')
            ->whereHas('activeRooms', fn ($q) => $q->whereIn('rooms.id', $roomIds))
            ->with('activeRooms')
            ->orderBy('name')
            ->get();

        return view('parent.messages.create', compact('carers'));
    }
}
