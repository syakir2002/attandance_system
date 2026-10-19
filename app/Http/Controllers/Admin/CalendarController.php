<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Services\MalaysiaHolidayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected MalaysiaHolidayService $holidayService,
    ) {}

    /**
     * Display the calendar view.
     */
    public function index(): View
    {
        return view('admin.calendar');
    }

    /**
     * Fetch calendar events from callendar_info table and Terengganu holidays.
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $statusFilter = $request->query('status'); // 'all', 'public', 'private', 'holidays'

        $events = [];

        // 1. Fetch Task/Event records from callendar_info table
        if ($statusFilter !== 'holidays') {
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
                $isBlock = ($task->display_style ?? 'dot') === 'block';
                $isPublic = (int) $task->status === 1;

                $events[] = [
                    'id' => 'task_'.$task->id,
                    'title' => $task->title,
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
                        'description' => $task->description,
                        'status' => (int) $task->status,
                        'is_public' => $isPublic,
                        'status_label' => $isPublic ? 'Public (Awam / Semua Staf)' : 'Private (Peribadi / Admin Sahaja)',
                        'display_style' => $task->display_style ?? 'dot',
                        'is_block' => $isBlock,
                        'start_formatted' => $task->start_at->translatedFormat('d M Y, h:i A'),
                        'end_formatted' => $task->end_at ? $task->end_at->translatedFormat('d M Y, h:i A') : '-',
                    ],
                ];
            }
        }

        // 2. Fetch Malaysia Public Holidays for Terengganu (TRG)
        if ($statusFilter !== 'private' && $start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
            $state = (string) $request->query('state', config('services.malaysia_holiday.default_state', 'TRG'));

            $holidayEvents = $this->holidayService->getAsCalendarEvents($startDate, $endDate, $state);
            $events = array_merge($events, $holidayEvents);
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
            'description' => 'nullable|string|max:1000',
            'start_at' => 'required|date',
            'end_at' => 'nullable',
            'status' => 'required|in:0,1',
            'display_style' => 'nullable|string|in:dot,block',
        ]);

        $startAt = Carbon::parse($validated['start_at']);
        $displayStyle = $validated['display_style'] ?? 'dot';

        // Safely determine end_at
        if (empty($validated['end_at'])) {
            $endAt = $displayStyle === 'block'
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
            'description' => $validated['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => (int) $validated['status'],
            'display_style' => $displayStyle,
            'timestamps' => now(),
        ]);

        $isPublic = (int) $event->status === 1;
        $isBlock = $displayStyle === 'block';

        return response()->json([
            'success' => true,
            'message' => 'Tugasan berjaya ditambah ke dalam pangkalan data!',
            'event' => [
                'id' => 'task_'.$event->id,
                'title' => $event->title,
                'start' => $event->start_at->toIso8601String(),
                'end' => $event->end_at->toIso8601String(),
                'allDay' => false,
                'className' => $isBlock
                    ? ($isPublic ? 'fc-custom-block-event fc-public-block' : 'fc-custom-block-event fc-private-block')
                    : ($isPublic ? 'fc-custom-dot-event fc-public-dot' : 'fc-custom-dot-event fc-private-dot'),
                'extendedProps' => [
                    'source' => 'task',
                    'db_id' => $event->id,
                    'raw_title' => $event->title,
                    'description' => $event->description,
                    'status' => (int) $event->status,
                    'is_public' => $isPublic,
                    'status_label' => $isPublic ? 'Public (Awam / Semua Staf)' : 'Private (Peribadi / Admin Sahaja)',
                    'display_style' => $displayStyle,
                    'is_block' => $isBlock,
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
