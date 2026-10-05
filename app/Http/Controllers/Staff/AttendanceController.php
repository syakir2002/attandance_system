<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display staff dashboard with real-time status and today's sessions.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        // Fetch today's sessions
        $todaySessions = Attendance::where('staff_id', $user->staff_id)
            ->where('date', $today)
            ->orderBy('check_in', 'asc')
            ->get();

        // Check if there is an active session (clocked in without checkout)
        $activeSession = Attendance::where('staff_id', $user->staff_id)
            ->whereNull('check_out')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->first();

        $isClockedIn = ! is_null($activeSession);

        // Calculate total seconds worked today
        $totalSecondsToday = 0;
        foreach ($todaySessions as $session) {
            if ($session->period) {
                [$h, $m, $s] = explode(':', $session->period);
                $totalSecondsToday += ($h * 3600) + ($m * 60) + $s;
            } elseif ($session->isActive()) {
                // Approximate time elapsed for active session
                try {
                    $checkInTime = Carbon::createFromTimeString($session->check_in);
                    $totalSecondsToday += $checkInTime->diffInSeconds(now());
                } catch (\Exception) {
                }
            }
        }

        $hoursToday = floor($totalSecondsToday / 3600);
        $minutesToday = floor(($totalSecondsToday % 3600) / 60);

        return view('staff.dashboard', compact(
            'user',
            'today',
            'todaySessions',
            'activeSession',
            'isClockedIn',
            'hoursToday',
            'minutesToday'
        ));
    }

    /**
     * Handle Clock In action.
     */
    public function clockIn(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validation: Cannot clock in if still has an open session
        $existingActiveSession = Attendance::where('staff_id', $user->staff_id)
            ->whereNull('check_out')
            ->first();

        if ($existingActiveSession) {
            return back()->with('error', 'Anda masih mempunyai sesi aktif yang belum di-Clock Out.');
        }

        $request->validate([
            'keterangan_in' => ['nullable', 'string', 'max:255'],
            'keterangan_staff' => ['nullable', 'string', 'max:255'],
        ]);

        $keteranganIn = $request->input('keterangan_in') ?? $request->input('keterangan_staff');

        $today = now()->format('Y-m-d');
        $datePart = now()->format('Ymd');
        $sessionCount = Attendance::where('staff_id', $user->staff_id)
            ->where('date', $today)
            ->count();
        $nextSession = str_pad((string) ($sessionCount + 1), 2, '0', STR_PAD_LEFT);
        $customId = "{$user->staff_id}-{$datePart}-{$nextSession}";

        $nowTime = now()->format('H:i:s');

        Attendance::create([
            'id' => $customId,
            'staff_id' => $user->staff_id,
            'check_in' => $nowTime,
            'check_out' => null,
            'date' => $today,
            'keterangan_in' => $keteranganIn,
            'keterangan_out' => null,
            'keterangan_admin' => null,
            'period' => null,
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', "Berjaya Clock In pada jam {$nowTime} (Sesi {$nextSession}).");
    }

    /**
     * Handle Clock Out action.
     */
    public function clockOut(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Find active open session
        $activeSession = Attendance::where('staff_id', $user->staff_id)
            ->whereNull('check_out')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->first();

        if (! $activeSession) {
            return back()->with('error', 'Tiada sesi aktif ditemui untuk Clock Out.');
        }

        $request->validate([
            'keterangan_out' => ['nullable', 'string', 'max:255'],
        ]);

        $nowTime = now()->format('H:i:s');
        $period = Attendance::calculatePeriod($activeSession->check_in, $nowTime);

        $activeSession->update([
            'check_out' => $nowTime,
            'period' => $period,
            'keterangan_out' => $request->keterangan_out,
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', "Berjaya Clock Out pada jam {$nowTime}. Tempoh masa bekerja sesi ini: {$period}.");
    }

    /**
     * Display staff attendance history with date, month & year filter.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();

        $selectedDate = $request->input('date', '');
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        $query = Attendance::where('staff_id', $user->staff_id);

        if (! empty($selectedDate)) {
            $query->where('date', $selectedDate);
            try {
                $carbonDate = Carbon::parse($selectedDate);
                $selectedMonth = $carbonDate->month;
                $selectedYear = $carbonDate->year;
            } catch (\Exception $e) {
                // Keep default if invalid date string
            }
        } else {
            if (! empty($selectedYear)) {
                $query->whereYear('date', $selectedYear);
            }
            if (! empty($selectedMonth)) {
                $query->whereMonth('date', $selectedMonth);
            }
        }

        // Clone for stats
        $allRecords = (clone $query)->get();

        $totalSeconds = 0;
        foreach ($allRecords as $rec) {
            if ($rec->period) {
                $parts = explode(':', $rec->period);
                if (count($parts) === 3) {
                    $totalSeconds += ((int) $parts[0] * 3600) + ((int) $parts[1] * 60) + (int) $parts[2];
                }
            }
        }

        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalDaysPresent = $allRecords->pluck('date')->unique()->count();
        $totalSessions = $allRecords->count();

        // Paginated list
        $records = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('staff.attendance.history', compact(
            'user',
            'records',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'totalHours',
            'totalMinutes',
            'totalDaysPresent',
            'totalSessions'
        ));
    }
}
