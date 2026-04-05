<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use App\Models\Child;
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

        return view('carer.messages.index', compact('conversations'));
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

        // Children in the carer's active rooms (for "about child" dropdown)
        $roomIds     = auth()->user()->activeRooms()->pluck('rooms.id');
        $roomChildren = Child::whereIn('room_id', $roomIds)->orderBy('first_name')->get();

        return view('carer.messages.show', compact('messages', 'user', 'roomChildren'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body'        => ['required', 'string', 'max:2000'],
            'child_id'    => ['nullable', 'exists:children,id'],
        ]);

        if ($validated['child_id'] ?? null) {
            $roomIds = auth()->user()->activeRooms()->pluck('rooms.id');
            $inRoom  = Child::where('id', $validated['child_id'])
                ->whereIn('room_id', $roomIds)
                ->exists();
            abort_unless($inRoom, 403);
        }

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'child_id'    => $validated['child_id'] ?? null,
            'body'        => $validated['body'],
        ]);

        return redirect()->route('carer.messages.show', $validated['receiver_id'])
            ->with('success', 'Message sent.');
    }

    public function create()
    {
        $carer   = auth()->user();
        $roomIds = $carer->activeRooms()->pluck('rooms.id');

        // Parents linked to children in the carer's active rooms
        $parents = User::where('role', 'parent')
            ->whereHas('children', fn ($q) => $q->whereIn('room_id', $roomIds))
            ->with(['children' => fn ($q) => $q->whereIn('room_id', $roomIds)])
            ->orderBy('name')
            ->get();

        return view('carer.messages.create', compact('parents'));
    }
}
