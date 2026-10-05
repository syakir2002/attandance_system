<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with overall statistics and today's activity.
     */
    public function index(): View
    {
        $admin = Auth::user();
        $today = now()->format('Y-m-d');

        // Total staff count (excluding admins)
        $totalStaff = User::where('role', 'staff')->count();

        // Today's attendance records
        $todayRecords = Attendance::with('user')
            ->where('date', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        // Staff currently clocked in (active sessions)
        $activeSessions = Attendance::with('user')
            ->where('date', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->get();

        $clockedInStaffIds = $todayRecords->pluck('staff_id')->unique()->toArray();
        $currentlyClockedInCount = $activeSessions->pluck('staff_id')->unique()->count();

        // Staff who have not clocked in at all today
        $notClockedInCount = max(0, $totalStaff - count($clockedInStaffIds));

        // Total sessions recorded today
        $totalSessionsToday = $todayRecords->count();

        // Staff who have not clocked in list
        $absentStaff = User::where('role', 'staff')
            ->whereNotIn('staff_id', $clockedInStaffIds)
            ->get();

        return view('admin.dashboard', compact(
            'admin',
            'today',
            'totalStaff',
            'currentlyClockedInCount',
            'notClockedInCount',
            'totalSessionsToday',
            'activeSessions',
            'todayRecords',
            'absentStaff'
        ));
    }
}
