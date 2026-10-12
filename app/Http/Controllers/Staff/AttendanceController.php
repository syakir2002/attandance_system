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
    public function dashboard(Request $request): View
    {
        $user = Auth::user();
        Attendance::syncIncompleteSessions($user->staff_id);

        $today = now()->format('Y-m-d');

        // Fetch today's sessions
        $todaySessions = Attendance::where('staff_id', $user->staff_id)
            ->where('date', $today)
            ->orderBy('check_in', 'asc')
            ->get();

        // Check if there is an active session for today
        $activeSession = Attendance::where('staff_id', $user->staff_id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        $isClockedIn = ! is_null($activeSession);

        // Calculate total seconds worked today
        $totalSecondsToday = 0;
        foreach ($todaySessions as $session) {
            if ($session->period && ! is_null($session->check_out)) {
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

        $incompleteCount = $user->incompleteAttendancesCount();
        $showIncompleteModal = (bool) $request->session()->pull('just_logged_in', false) && ($incompleteCount > 0);

        return view('staff.dashboard', compact(
            'user',
            'today',
            'todaySessions',
            'activeSession',
            'isClockedIn',
            'hoursToday',
            'minutesToday',
            'incompleteCount',
            'showIncompleteModal'
        ));
    }

    /**
     * Handle Clock In action validation.
     */
    private function validation(string $staff_id): ?RedirectResponse
    {
        Attendance::syncIncompleteSessions($staff_id);

        // Cannot clock in if still has an open session today
        $existingActiveSession = Attendance::where('staff_id', $staff_id)
            ->where('date', now()->format('Y-m-d'))
            ->whereNull('check_out')
            ->first();

        if ($existingActiveSession) {
            return back()->with('error', 'Anda masih mempunyai sesi aktif hari ini yang belum di-Clock Out.');
        }

        return null;
    }

    private function genId($staff_id, $datePart, $sessionCount)
    {
        $nextSession = str_pad((string) ($sessionCount + 1), 2, '0', STR_PAD_LEFT);
        $customId = "{$staff_id}-{$datePart}-{$nextSession}";

        return [$customId, $nextSession];
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validationError = $this->validation($user->staff_id);
        if ($validationError) {
            return $validationError;
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
        $customId = $this->genId($user->staff_id, $datePart, $sessionCount);

        $nowTime = now()->format('H:i:s');

        Attendance::create([
            'id' => $customId[0],
            'staff_id' => $user->staff_id,
            'check_in' => $nowTime,
            'check_out' => null,
            'date' => $today,
            'keterangan_in' => $keteranganIn,
            'keterangan_out' => null,
            'keterangan_admin' => null,
            'period' => null,
            'status' => null,
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', "Berjaya Clock In pada jam {$nowTime} (Sesi {$customId[1]}).");
    }

    /**
     * Handle Clock Out action.
     */
    public function clockOut(Request $request): RedirectResponse
    {
        $user = Auth::user();
        Attendance::syncIncompleteSessions($user->staff_id);

        // Find active open session for today
        $activeSession = Attendance::where('staff_id', $user->staff_id)
            ->where('date', now()->format('Y-m-d'))
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if (! $activeSession) {
            return back()->with('error', 'Tiada sesi aktif ditemui untuk Clock Out hari ini.');
        }

        $request->validate([
            'keterangan_out' => ['nullable', 'string', 'max:255'],
        ]);

        $clock_in = Carbon::parse($activeSession->check_in);
        $clock_out = Carbon::now();

        $date_in = $clock_in->toDateString();
        $date_out = $clock_out->toDateString();
        $nowTime = now()->format('H:i:s');

        if ($date_out > $date_in) {
            $periodFirst = Attendance::calculatePeriod($activeSession->check_in, '00:00');
            $periodSecond = Attendance::calculatePeriod('00:00', $nowTime);

            $activeSession->update([
                'check_out' => '00:00',
                'period' => $periodFirst,
                'status' => 'completed',
                'keterangan_out' => $request->keterangan_out,
            ]);

            $today = now()->format('Y-m-d');
            $datePart = now()->format('Ymd');
            $sessionCount = Attendance::where('staff_id', $user->staff_id)
                ->where('date', $today)
                ->count();
            $customId = $this->genId($user->staff_id, $datePart, $sessionCount);

            Attendance::create([
                'id' => $customId[0],
                'staff_id' => $user->staff_id,
                'check_in' => '00:00',
                'check_out' => $nowTime,
                'date' => $date_out,
                'keterangan_in' => 'OT',
                'keterangan_out' => null,
                'keterangan_admin' => null,
                'period' => $periodSecond,
                'status' => 'completed',
            ]);
            $period = Attendance::calculatePeriod($activeSession->check_in, $nowTime);
        } else {
            $period = Attendance::calculatePeriod($activeSession->check_in, $nowTime);
            $activeSession->update([
                'check_out' => $nowTime,
                'period' => $period,
                'status' => 'completed',
                'keterangan_out' => $request->keterangan_out,
            ]);
        }

        return redirect()->route('staff.dashboard')
            ->with('success', "Berjaya Clock Out pada jam {$nowTime}. Tempoh masa bekerja sesi ini: {$period}.");
    }

    /**
     * Display staff attendance history with date, month & year filter.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        Attendance::syncIncompleteSessions($user->staff_id);

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
            // Only calculate completed sessions with valid check_out (sessions with check_out = NULL are ignored)
            if ($rec->period && ! is_null($rec->check_out) && $rec->status !== 'incomplete') {
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

        // Incomplete sessions count for this user
        $incompleteCount = Attendance::where('staff_id', $user->staff_id)
            ->where('status', 'incomplete')
            ->count();

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
            'totalSessions',
            'incompleteCount'
        ));
    }

    /**
     * Handle manual clock out update for incomplete session.
     */
    public function updateClockOut(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        Attendance::syncIncompleteSessions($user->staff_id);

        $attendance = Attendance::where('staff_id', $user->staff_id)
            ->where('id', $id)
            ->firstOrFail();

        // Ensure record is incomplete or missing check_out
        if (! $attendance->isIncomplete() && ! is_null($attendance->check_out)) {
            return back()->with('error', 'Rekod ini bukan sesi tidak lengkap atau telah pun mempunyai masa Clock Out.');
        }

        // Monthly cutoff check: Cannot update if month has already ended
        $recordMonth = Carbon::parse($attendance->date)->format('Y-m');
        $currentMonth = now()->format('Y-m');
        if ($recordMonth < $currentMonth) {
            return back()->with('error', 'Tempoh akhir bulan telah tamat bagi rekod ini. Sesi ini tidak lagi boleh dikemaskini secara manual oleh staf. Sila rujuk pihak HR/Admin.');
        }

        $request->validate([
            'check_out' => ['required', 'date_format:H:i'],
            'keterangan_out' => ['nullable', 'string', 'max:255'],
        ], [
            'check_out.required' => 'Sila masukkan masa Clock Out.',
            'check_out.date_format' => 'Format masa tidak sah (HH:mm).',
        ]);

        $checkOutTime = $request->input('check_out').':00';

        $inTime = Carbon::createFromTimeString($attendance->check_in);
        $outTime = Carbon::createFromTimeString($checkOutTime);

        if ($outTime->lessThanOrEqualTo($inTime)) {
            return back()->with('error', 'Masa Clock Out mestilah selepas masa Clock In ('.Carbon::createFromTimeString($attendance->check_in)->format('h:i A').').');
        }

        $period = Attendance::calculatePeriod($attendance->check_in, $checkOutTime);

        $note = trim((string) $request->input('keterangan_out', ''));
        $keteranganOut = $note !== '' ? "[Kemaskini Lupa Clock Out] {$note}" : '[Kemaskini Lupa Clock Out]';

        $attendance->update([
            'check_out' => $checkOutTime,
            'period' => $period,
            'status' => 'completed',
            'keterangan_out' => $keteranganOut,
        ]);

        return redirect()->route('staff.attendance.history')
            ->with('success', "Masa Clock Out bagi sesi {$attendance->id} berjaya dikemaskini ({$checkOutTime}). Tempoh masa bekerja sesi ini: {$period}.");
    }
}
