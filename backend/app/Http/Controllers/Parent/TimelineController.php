<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TimelineController extends Controller
{
    public function show(Request $request, Child $child)
    {
        $parent = auth()->user();

        $linked = $parent->children()
            ->where('children.id', $child->id)
            ->exists();

        abort_unless($linked, 403);

        $date = $request->input('date', now()->toDateString());

        $events = collect()
            ->merge($this->attendanceEvents($child, $date))
            ->merge($this->dailyUpdateEvents($child, $date))
            ->merge($this->dailyReportEvents($child, $date))
            ->merge($this->medicationEvents($child, $date))
            ->merge($this->incidentEvents($child, $date))
            ->sortBy('timestamp')
            ->values();

        return view('parent.timeline.show', compact('child', 'date', 'events'));
    }

    private function attendanceEvents(Child $child, string $date): Collection
    {
        return $child->attendances()
            ->whereDate('date', $date)
            ->get()
            ->flatMap(function ($attendance) use ($date) {
                $events = collect();

                if ($attendance->check_in_at) {
                    $events->push([
                        'type' => 'attendance',
                        'title' => 'Checked In',
                        'details' => 'Status: ' . ucfirst($attendance->status ?? 'present'),
                        'timestamp' => Carbon::parse($attendance->check_in_at),
                        'record' => $attendance,
                    ]);
                }

                if ($attendance->check_out_at) {
                    $events->push([
                        'type' => 'attendance',
                        'title' => 'Checked Out',
                        'details' => 'Child checked out for the day.',
                        'timestamp' => Carbon::parse($attendance->check_out_at),
                        'record' => $attendance,
                    ]);
                }

                if (!$attendance->check_in_at && !$attendance->check_out_at) {
                    $events->push([
                        'type' => 'attendance',
                        'title' => 'Attendance Recorded',
                        'details' => 'Status: ' . ucfirst($attendance->status ?? 'recorded'),
                        'timestamp' => Carbon::parse($date . ' 09:00:00'),
                        'record' => $attendance,
                    ]);
                }

                return $events;
            });
    }

    private function dailyUpdateEvents(Child $child, string $date): Collection
    {
        return $child->dailyUpdates()
            ->whereDate('date', $date)
            ->get()
            ->flatMap(function ($update) use ($date) {
                $events = collect();

                if (!empty($update->meals)) {
                    $events->push([
                        'type' => 'meal',
                        'title' => 'Meal Update',
                        'details' => $update->meals,
                        'timestamp' => Carbon::parse($date . ' 12:00:00'),
                        'record' => $update,
                    ]);
                }

                if (!empty($update->sleep)) {
                    $events->push([
                        'type' => 'sleep',
                        'title' => 'Sleep Update',
                        'details' => $update->sleep,
                        'timestamp' => Carbon::parse($date . ' 13:00:00'),
                        'record' => $update,
                    ]);
                }

                if (!empty($update->notes)) {
                    $events->push([
                        'type' => 'note',
                        'title' => 'Daily Note',
                        'details' => $update->notes,
                        'timestamp' => Carbon::parse($date . ' 15:00:00'),
                        'record' => $update,
                    ]);
                }

                return $events;
            });
    }

    private function dailyReportEvents(Child $child, string $date): Collection
    {
        return $child->dailyReports()
            ->whereDate('date', $date)
            ->get()
            ->map(function ($report) use ($date) {
                return [
                    'type' => 'report',
                    'title' => 'Daily Report',
                    'details' => $report->daily_report ?: 'Daily report submitted.',
                    'timestamp' => $report->created_at
                        ? Carbon::parse($report->created_at)
                        : Carbon::parse($date . ' 16:00:00'),
                    'record' => $report,
                ];
            });
    }

    private function medicationEvents(Child $child, string $date): Collection
    {
        return $child->medicationLogs()
            ->whereDate('date', $date)
            ->get()
            ->map(function ($medication) use ($date) {
                $time = $medication->time_given ?: '11:00:00';

                return [
                    'type' => 'medication',
                    'title' => 'Medication Given',
                    'details' => trim(
                        $medication->medication_name .
                        ($medication->dosage ? ' (' . $medication->dosage . ')' : '') .
                        ($medication->notes ? ' - ' . $medication->notes : '')
                    ),
                    'timestamp' => Carbon::parse($date . ' ' . $time),
                    'record' => $medication,
                ];
            });
    }

    private function incidentEvents(Child $child, string $date): Collection
    {
        return $child->incidentReports()
            ->whereDate('incident_date', $date)
            ->get()
            ->map(function ($incident) use ($date) {
                $time = $incident->incident_time ?: '14:00:00';

                return [
                    'type' => 'incident',
                    'title' => $incident->title ?: 'Incident Report',
                    'details' => trim(
                        ($incident->description ?: 'Incident recorded.') .
                        ($incident->action_taken ? ' Action taken: ' . $incident->action_taken : '')
                    ),
                    'timestamp' => Carbon::parse($date . ' ' . $time),
                    'record' => $incident,
                ];
            });
    }
}