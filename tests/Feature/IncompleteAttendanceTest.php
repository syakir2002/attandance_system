<?php

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = User::create([
        'staff_id' => 'STF_01',
        'name' => 'Khairul Amin Bin Mamat',
        'email' => 'STF_01@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '010213123',
        'role' => 'staff',
    ]);
});

test('unclosed session from yesterday is marked incomplete with null checkout and does not block clock in today', function () {
    $yesterday = now()->subDay()->format('Y-m-d');
    $yesterdayPart = now()->subDay()->format('Ymd');
    $yesterdaySessionId = "STF_01-{$yesterdayPart}-01";

    // Session yesterday where staff forgot to clock out
    $yesterdayAttendance = Attendance::create([
        'id' => $yesterdaySessionId,
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'keterangan_in' => 'Masuk pagi semalam',
        'period' => null,
        'status' => null,
    ]);

    // Staff visits dashboard today
    $this->actingAs($this->staff)
        ->get(route('staff.dashboard'))
        ->assertOk();

    // Check yesterday's record: check_out remains NULL and status is now incomplete
    $yesterdayAttendance->refresh();
    expect($yesterdayAttendance->check_out)->toBeNull();
    expect($yesterdayAttendance->status)->toBe('incomplete');
    expect($yesterdayAttendance->period)->toBeNull();

    // Staff can clock in today without any block
    $response = $this->actingAs($this->staff)
        ->post(route('staff.attendance.clock_in'), [
            'keterangan_in' => 'Masuk pagi hari ini',
        ]);

    $response->assertRedirect(route('staff.dashboard'));

    $todayPart = now()->format('Ymd');
    $todayAttendance = Attendance::where('id', "STF_01-{$todayPart}-01")->first();
    expect($todayAttendance)->not->toBeNull();
    expect($todayAttendance->check_in)->not->toBeNull();
    expect($todayAttendance->check_out)->toBeNull();
});

test('working hours ignores incomplete sessions with null checkout', function () {
    $yesterday = now()->subDay()->format('Y-m-d');

    // Incomplete session (8:30 in, no out)
    Attendance::create([
        'id' => 'STF_01-yesterday-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'period' => null,
        'status' => 'incomplete',
    ]);

    // Completed session today (9:00 to 12:00 = 3 hours)
    Attendance::create([
        'id' => 'STF_01-today-01',
        'staff_id' => 'STF_01',
        'check_in' => '09:00:00',
        'check_out' => '12:00:00',
        'date' => now()->format('Y-m-d'),
        'period' => '03:00:00',
        'status' => 'completed',
    ]);

    $response = $this->actingAs($this->staff)
        ->get(route('staff.attendance.history'));

    $response->assertOk();
    // Only 3 hours should be calculated, not the incomplete session
    $response->assertViewHas('totalHours', 3);
    $response->assertViewHas('totalMinutes', 0);
    $response->assertViewHas('incompleteCount', 1);
});

test('incomplete sessions trigger navbar red dot count and banner alert', function () {
    $yesterday = now()->subDay()->format('Y-m-d');

    Attendance::create([
        'id' => 'STF_01-yesterday-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'period' => null,
        'status' => null,
    ]);

    expect($this->staff->incompleteAttendancesCount())->toBe(1);

    $response = $this->actingAs($this->staff)
        ->get(route('staff.attendance.history'));

    $response->assertOk();
    $response->assertSee('Anda mempunyai 1 sesi \'Incomplete\' (terlupa Clock Out).', false);
    $response->assertSee('Kemaskini Clock Out');
});

test('staff can manually update clock out for incomplete session within current month', function () {
    $yesterday = now()->subDay()->format('Y-m-d');

    $attendance = Attendance::create([
        'id' => 'STF_01-yesterday-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'period' => null,
        'status' => 'incomplete',
    ]);

    expect($this->staff->incompleteAttendancesCount())->toBe(1);

    $response = $this->actingAs($this->staff)
        ->put(route('staff.attendance.update_clock_out', $attendance->id), [
            'check_out' => '17:30',
            'keterangan_out' => 'Terlupa tekan butang semalam',
        ]);

    $response->assertRedirect(route('staff.attendance.history'));
    $response->assertSessionHas('success');

    $attendance->refresh();
    expect($attendance->check_out)->toBe('17:30:00');
    expect($attendance->period)->toBe('09:00:00');
    expect($attendance->status)->toBe('completed');
    expect($attendance->keterangan_out)->toContain('Terlupa tekan butang semalam');

    // Incomplete count is now 0 (red dot disappears)
    expect($this->staff->fresh()->incompleteAttendancesCount())->toBe(0);
});

test('staff cannot update clock out with time earlier than check in', function () {
    $yesterday = now()->subDay()->format('Y-m-d');

    $attendance = Attendance::create([
        'id' => 'STF_01-yesterday-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'period' => null,
        'status' => 'incomplete',
    ]);

    $response = $this->actingAs($this->staff)
        ->put(route('staff.attendance.update_clock_out', $attendance->id), [
            'check_out' => '08:00',
        ]);

    $response->assertSessionHas('error');
    $attendance->refresh();
    expect($attendance->check_out)->toBeNull();
    expect($attendance->status)->toBe('incomplete');
});

test('staff cannot update clock out if month cutoff has passed', function () {
    $pastMonthDate = now()->subMonths(2)->format('Y-m-15');

    $attendance = Attendance::create([
        'id' => 'STF_01-pastmonth-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $pastMonthDate,
        'period' => null,
        'status' => 'incomplete',
    ]);

    $response = $this->actingAs($this->staff)
        ->put(route('staff.attendance.update_clock_out', $attendance->id), [
            'check_out' => '17:30',
        ]);

    $response->assertSessionHas('error');
    $attendance->refresh();
    expect($attendance->check_out)->toBeNull();
});

test('staff dashboard renders incomplete attendance alert modal only once right after login', function () {
    $yesterday = now()->subDay()->format('Y-m-d');

    Attendance::create([
        'id' => 'STF_01-yesterday-modal-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $yesterday,
        'period' => null,
        'status' => 'incomplete',
    ]);

    // 1. Visiting dashboard normally (e.g. clicking dashboard navbar) does NOT show modal
    $response = $this->actingAs($this->staff)
        ->get(route('staff.dashboard'));

    $response->assertOk();
    $response->assertViewHas('showIncompleteModal', false);
    $response->assertDontSee('id="incompleteAttendanceModal"', false);

    // 2. Logging in sets just_logged_in flag in session
    Auth::logout();
    $loginResponse = $this->post(route('login.post'), [
        'login' => 'STF_01',
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect(route('staff.dashboard'));
    $loginResponse->assertSessionHas('just_logged_in', true);

    // Initial landing on dashboard right after login renders the modal
    $dashboardResponse = $this->actingAs($this->staff)
        ->withSession(['just_logged_in' => true])
        ->get(route('staff.dashboard'));

    $dashboardResponse->assertOk();
    $dashboardResponse->assertViewHas('showIncompleteModal', true);
    $dashboardResponse->assertSee('id="incompleteAttendanceModal"', false);
    $dashboardResponse->assertSee('Peringatan Kehadiran');
    $dashboardResponse->assertSee('Pergi ke Sejarah Kehadiran');

    // 3. Visiting dashboard again (clicking dashboard link or refresh) pulls the flag and no longer shows modal
    $subsequentResponse = $this->actingAs($this->staff)
        ->get(route('staff.dashboard'));

    $subsequentResponse->assertOk();
    $subsequentResponse->assertViewHas('showIncompleteModal', false);
    $subsequentResponse->assertDontSee('id="incompleteAttendanceModal"', false);
});
