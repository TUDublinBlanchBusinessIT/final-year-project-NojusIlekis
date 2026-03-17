<?php

namespace App\Http\Controllers\Carer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $rooms = auth()->user()->activeRooms()->with('children')->get();

        return view('dashboards.carer', compact('rooms'));
    }
}
