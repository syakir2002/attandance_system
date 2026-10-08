<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index(): View
    {
        return view('admin.calendar');
    }

    /**
     * Fetch calendar events from callendar_info table.
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $statusFilter = $request->query('status'); // 'all', 'public', 'private'

        $events = [];

        // 1. Fetch Task/Event records from callendar_info table
        $tasksQuery = CalendarEvent::query();

        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
            $tasksQuery->whereBetween('start_at', [$startDate, $endDate]);
        }

        if ($statusFilter === 'public') {
            $tasksQuery->where('status', 1);
        } elseif ($statusFilter === 'private') {
            $tasksQuery->where('status', 0);
        }

        $tasks = $tasksQuery->orderBy('start_at')->get();

        foreach ($tasks as $task) {
            $diffMinutes = $task->start_at->diffInMinutes($task->end_at);
            $isBlock = $diffMinutes >= 60;
            $isPublic = (int) $task->status === 1;

            $statusPrefix = $isPublic ? '[Public] ' : '[Private] ';

            $events[] = [
                'id' => 'task_'.$task->id,
                'title' => $statusPrefix.$task->title,
                'start' => $task->start_at->toIso8601String(),
                'end' => $task->end_at ? $task->end_at->toIso8601String() : null,
                'allDay' => false,
                'className' => $isBlock
                    ? ($isPublic ? 'fc-custom-block-event fc-public-block' : 'fc-custom-block-event fc-private-block')
                    : ($isPublic ? 'fc-custom-dot-event fc-public-dot' : 'fc-custom-dot-event fc-private-dot'),
                'extendedProps' => [
                    'source' => 'task',
                    'db_id' => $task->id,
                    'raw_title' => $task->title,
                    'status' => (int) $task->status,
                    'is_public' => $isPublic,
                    'status_label' => $isPublic ? 'Public (Awam / Semua Staf)' : 'Private (Peribadi / Admin Sahaja)',
                    'is_block' => $isBlock,
                    'start_formatted' => $task->start_at->translatedFormat('d M Y, h:i A'),
                    'end_formatted' => $task->end_at ? $task->end_at->translatedFormat('d M Y, h:i A') : '-',
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Store a new task/event in callendar_info.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable',
            'status' => 'required|in:0,1',
            'display_style' => 'nullable|string|in:dot,block',
        ]);

        $startAt = Carbon::parse($validated['start_at']);

        // Safely determine end_at
        if (empty($validated['end_at'])) {
            $endAt = ($validated['display_style'] ?? 'dot') === 'block'
                ? (clone $startAt)->addHours(2)
                : (clone $startAt)->addMinutes(30);
        } else {
            $endAt = Carbon::parse($validated['end_at']);
            if ($endAt->lessThan($startAt)) {
                $endAt = (clone $startAt)->addHour();
            }
        }

        $event = CalendarEvent::create([
            'title' => $validated['title'],
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => (int) $validated['status'],
            'timestamps' => now(),
        ]);

        $isPublic = (int) $event->status === 1;
        $statusPrefix = $isPublic ? '[Public] ' : '[Private] ';

        return response()->json([
            'success' => true,
            'message' => 'Tugasan berjaya ditambah ke dalam pangkalan data!',
            'event' => [
                'id' => 'task_'.$event->id,
                'title' => $statusPrefix.$event->title,
                'start' => $event->start_at->toIso8601String(),
                'end' => $event->end_at->toIso8601String(),
                'allDay' => false,
                'className' => ($validated['display_style'] ?? 'dot') === 'block' ? 'fc-custom-block-event' : 'fc-custom-dot-event',
                'extendedProps' => [
                    'source' => 'task',
                    'db_id' => $event->id,
                    'raw_title' => $event->title,
                    'status' => (int) $event->status,
                    'is_public' => $isPublic,
                    'status_label' => $isPublic ? 'Public (Awam / Semua Staf)' : 'Private (Peribadi / Admin Sahaja)',
                    'start_formatted' => $event->start_at->translatedFormat('d M Y, h:i A'),
                    'end_formatted' => $event->end_at->translatedFormat('d M Y, h:i A'),
                ],
            ],
        ]);
    }

    /**
     * Delete a task/event from callendar_info.
     */
    public function destroy(int $id): JsonResponse
    {
        $event = CalendarEvent::find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Tugasan tidak dijumpai.',
            ], 404);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugasan berjaya dipadam daripada pangkalan data!',
        ]);
    }
}
