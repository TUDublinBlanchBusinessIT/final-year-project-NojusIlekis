<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;

class ParentChildrenController extends Controller
{
    public function index(Request $request)
    {
        $children = $request->user()
            ->children()
            ->with('room')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('parent.children.index', compact('children'));
    }

    public function show(Request $request, Child $child)
    {
        $child = $request->user()
            ->children()
            ->with('room')
            ->findOrFail($child->id);

        $allowedTabs = ['profile', 'updates', 'attendance', 'reports', 'medication'];
        $tab = $request->query('tab', 'profile');
        $activeTab = in_array($tab, $allowedTabs, true) ? $tab : 'profile';

        $dailyUpdates = $child->dailyUpdates()
            ->with('createdBy')
            ->latest('date')
            ->get();

        $attendanceRecords = $child->attendances()
            ->with(['room', 'recordedBy'])
            ->latest('date')
            ->get();

        $dailyReports = $child->dailyReports()
            ->with(['carer', 'mediaUpdates'])
            ->latest('date')
            ->get();

        $medicationLogs = $child->medicationLogs()
            ->with('carer')
            ->orderByDesc('date')
            ->orderByDesc('time_given')
            ->get();

        return view('parent.children.show', compact(
            'child',
            'activeTab',
            'dailyUpdates',
            'attendanceRecords',
            'dailyReports',
            'medicationLogs'
        ));
    }
}