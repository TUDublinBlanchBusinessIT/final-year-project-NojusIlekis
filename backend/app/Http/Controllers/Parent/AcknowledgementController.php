<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Acknowledgement;
use Illuminate\Http\Request;

class AcknowledgementController extends Controller
{
    public function index(Request $request)
    {
        $parent = auth()->user();

        $acknowledgements = Acknowledgement::where('parent_id', $parent->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('parent.acknowledgements.index', compact('acknowledgements'));
    }
}