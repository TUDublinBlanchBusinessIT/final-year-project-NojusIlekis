<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;
use App\Models\StaffClockIn;
use Illuminate\Http\Request;

class ClockInController extends Controller
{
    public function clockIn(Request $request)
    {
        $user = $request->user();
        abort_if($user->isClockedIn(), 422, 'You are already clocked in.');

        $roomId = $user->activeRooms()->first()?->id;

        StaffClockIn::create([
            'user_id'       => $user->id,
            'clocked_in_at' => now(),
            'room_id'       => $roomId,
        ]);

        return redirect()->back()->with('success', __('staff.clocked_in_success'));
    }

    public function clockOut(Request $request)
    {
        $user   = $request->user();
        $active = $user->currentClockIn();
        abort_if(! $active, 422, 'You are not currently clocked in.');

        $active->update(['clocked_out_at' => now()]);

        return redirect()->back()->with('success', __('staff.clocked_out_success', [
            'duration' => $active->fresh()->durationLabel(),
        ]));
    }
}
