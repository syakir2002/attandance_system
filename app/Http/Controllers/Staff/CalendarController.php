<?php

namespace App\Http\Controllers\Staff;

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
     * Display the read-only calendar view for staff.
     */
    public function index(): View
    {
        return view('staff.calendar');
    }

    /**
     * Fetch public calendar events and Terengganu holidays for staff.
     * Only events with status = 1 (Public) and public holidays are retrieved.
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');

        // 1. Retrieve public events (status = 1) from database
        $tasksQuery = CalendarEvent::public();

        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
            $tasksQuery->whereBetween('start_at', [$startDate, $endDate]);
        }

        $tasks = $tasksQuery->orderBy('start_at')->get();
        $events = [];

        foreach ($tasks as $task) {
            $isBlock = ($task->display_style ?? 'dot') === 'block';

            $events[] = [
                'id' => 'task_'.$task->id,
                'title' => $task->title,
                'start' => $task->start_at->toIso8601String(),
                'end' => $task->end_at ? $task->end_at->toIso8601String() : null,
                'allDay' => false,
                'className' => $isBlock
                    ? 'fc-custom-block-event fc-public-block'
                    : 'fc-custom-dot-event fc-public-dot',
                'extendedProps' => [
                    'source' => 'task',
                    'db_id' => $task->id,
                    'raw_title' => $task->title,
                    'description' => $task->description,
                    'status' => (int) $task->status,
                    'is_public' => true,
                    'type_label' => 'Aktiviti / Hebahan Syarikat',
                    'display_style' => $task->display_style ?? 'dot',
                    'is_block' => $isBlock,
                    'start_formatted' => $task->start_at->translatedFormat('d M Y, h:i A'),
                    'end_formatted' => $task->end_at ? $task->end_at->translatedFormat('d M Y, h:i A') : '-',
                ],
            ];
        }

        // 2. Fetch Malaysia Public Holidays for Terengganu (TRG)
        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
            $state = (string) $request->query('state', config('services.malaysia_holiday.default_state', 'TRG'));

            $holidayEvents = $this->holidayService->getAsCalendarEvents($startDate, $endDate, $state);
            $events = array_merge($events, $holidayEvents);
        }

        return response()->json($events);
    }
}
