<?php

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed test users
    $this->admin = User::create([
        'staff_id' => 'HR_01',
        'name' => 'Abdul Bin Ghazali',
        'email' => 'hr@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '010213123',
        'role' => 'admin',
    ]);

    $this->staff = User::create([
        'staff_id' => 'STF_01',
        'name' => 'Khairul Amin Bin Mamat',
        'email' => 'STF_01@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '010213123',
        'role' => 'staff',
    ]);
});

test('redirects guest to login page', function () {
    $response = $this->get('/');
    $response->assertRedirect(route('login'));
});

test('can login as admin with staff_id or email and redirects to admin dashboard', function () {
    // Login with staff_id
    $response = $this->post(route('login.post'), [
        'login' => 'HR_01',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($this->admin);

    // Logout
    $this->post(route('logout'));
    $this->assertGuest();

    // Login with email
    $response = $this->post(route('login.post'), [
        'login' => 'hr@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});

test('can login as staff and redirects to staff dashboard', function () {
    $response = $this->post(route('login.post'), [
        'login' => 'STF_01',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('staff.dashboard'));
    $this->assertAuthenticatedAs($this->staff);
});

test('staff id login is case-sensitive', function () {
    $response = $this->post(route('login.post'), [
        'login' => 'stf_01',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

test('remember my login does not create a persistent authentication cookie', function () {
    $response = $this->post(route('login.post'), [
        'login' => 'HR_01',
        'password' => 'password123',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertCookieMissing(Auth::guard()->getRecallerName());
    expect($this->admin->fresh()->getRememberToken())->toBeEmpty();
});

test('role middleware prevents unauthorized access', function () {
    // Staff trying to access admin dashboard
    $this->actingAs($this->staff)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('staff.dashboard'));

    // Admin trying to access staff dashboard
    $this->actingAs($this->admin)
        ->get(route('staff.dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('staff dashboard renders properly with status', function () {
    $response = $this->actingAs($this->staff)->get(route('staff.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Khairul Amin Bin Mamat');
    $response->assertSee('STF_01');
    $response->assertSee('CLOCKED OUT');
});

test('staff can clock in with auto generated custom id format and keterangan_in', function () {
    $response = $this->actingAs($this->staff)
        ->post(route('staff.attendance.clock_in'), [
            'keterangan_in' => 'Masuk waktu pagi',
        ]);

    $response->assertRedirect(route('staff.dashboard'));

    $todayPart = now()->format('Ymd');
    $expectedId = "STF_01-{$todayPart}-01";

    $attendance = Attendance::where('id', $expectedId)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->staff_id)->toBe('STF_01');
    expect($attendance->keterangan_in)->toBe('Masuk waktu pagi');
    expect($attendance->check_out)->toBeNull();
});

test('staff cannot clock in twice while an active session is open', function () {
    // First clock in
    $this->actingAs($this->staff)
        ->post(route('staff.attendance.clock_in'));

    // Second clock in without clock out
    $response = $this->actingAs($this->staff)
        ->post(route('staff.attendance.clock_in'));

    $response->assertSessionHas('error');
    expect(Attendance::where('staff_id', 'STF_01')->count())->toBe(1);
});

test('staff can clock out with keterangan_out and system calculates period', function () {
    $today = now()->format('Y-m-d');
    $todayPart = now()->format('Ymd');
    $customId = "STF_01-{$todayPart}-01";

    Attendance::create([
        'id' => $customId,
        'staff_id' => 'STF_01',
        'check_in' => '09:00:00',
        'check_out' => null,
        'date' => $today,
        'keterangan_in' => 'Masuk kerja',
        'period' => null,
    ]);

    $response = $this->actingAs($this->staff)
        ->post(route('staff.attendance.clock_out'), [
            'keterangan_out' => 'Selesai tugas harian',
        ]);

    $response->assertRedirect(route('staff.dashboard'));

    $attendance = Attendance::find($customId);
    expect($attendance->check_out)->not->toBeNull();
    expect($attendance->period)->not->toBeNull();
    expect($attendance->keterangan_out)->toBe('Selesai tugas harian');
});

test('staff can view attendance history filtered by month and year', function () {
    Attendance::create([
        'id' => 'STF_01-20260101-01',
        'staff_id' => 'STF_01',
        'check_in' => '09:00:00',
        'check_out' => '17:00:00',
        'date' => '2026-01-01',
        'period' => '08:00:00',
    ]);

    $response = $this->actingAs($this->staff)
        ->get(route('staff.attendance.history', ['month' => 1, 'year' => 2026]));

    $response->assertStatus(200);
    $response->assertSee('STF_01-20260101-01');
    $response->assertSee('08:00:00');
});

test('staff can view attendance history filtered by specific date', function () {
    Attendance::create([
        'id' => 'STF_01-20260315-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => '16:30:00',
        'date' => '2026-03-15',
        'period' => '08:00:00',
    ]);
    Attendance::create([
        'id' => 'STF_01-20260316-01',
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => '16:30:00',
        'date' => '2026-03-16',
        'period' => '08:00:00',
    ]);

    $response = $this->actingAs($this->staff)
        ->get(route('staff.attendance.history', ['date' => '2026-03-15']));

    $response->assertStatus(200);
    $response->assertSee('STF_01-20260315-01');
    $response->assertDontSee('STF_01-20260316-01');
});

test('staff can update profile and password', function () {
    $response = $this->actingAs($this->staff)
        ->put(route('staff.profile.update'), [
            'name' => 'Khairul Amin Kemaskini',
            'email' => 'khairul.new@gmail.com',
            'phone_number' => '0199999999',
        ]);

    $response->assertSessionHas('success');
    expect($this->staff->fresh()->name)->toBe('Khairul Amin Kemaskini');

    // Update password
    $pwdResponse = $this->actingAs($this->staff)
        ->put(route('staff.profile.password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $pwdResponse->assertSessionHas('success');
    expect(Hash::check('newpassword123', $this->staff->fresh()->password))->toBeTrue();
});

test('admin can search staff by typing in the attendance filter', function () {
    User::create([
        'staff_id' => 'STF_02',
        'name' => 'Aisyah Binti Ali',
        'email' => 'STF_02@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '0123456789',
        'role' => 'staff',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('staffSearchInput', false);
    $response->assertSee('staffSearchOptions', false);
    $response->assertSee('Cari staf');
});

test('system auto closes open attendance at 12:00 and saves a default 12:00 clock out', function () {
    Carbon::setTestNow(Carbon::parse('2026-09-30 12:05:00'));

    $today = now()->format('Y-m-d');
    $customId = 'STF_01-'.now()->format('Ymd').'-01';

    Attendance::create([
        'id' => $customId,
        'staff_id' => 'STF_01',
        'check_in' => '08:30:00',
        'check_out' => null,
        'date' => $today,
        'keterangan_in' => 'Masuk kerja',
        'period' => null,
    ]);

    $this->artisan('attendance:auto-close')->assertSuccessful();

    $attendance = Attendance::find($customId);
    expect($attendance->check_out)->toBe('12:00:00');
    expect($attendance->period)->toBe('03:30:00');
    expect($attendance->keterangan_system)->toBe('Auto cut off oleh sistem pada 12:00. Check-out default diset ke 12:00.');

    Carbon::setTestNow();
});

test('admin can view dashboard statistics and activities', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Ringkasan &amp; Dashboard Utama', false);
    $response->assertSee('Abdul Bin Ghazali');
});

test('admin can create, update, and manage staff accounts', function () {
    // Create new staff
    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), [
            'staff_id' => 'STF_02',
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@gmail.com',
            'phone_number' => '0123456789',
            'password' => 'password123',
            'role' => 'staff',
        ]);

    $response->assertRedirect(route('admin.staff.index'));
    $newStaff = User::where('staff_id', 'STF_02')->first();
    expect($newStaff)->not->toBeNull();
    expect($newStaff->name)->toBe('Siti Nurhaliza');

    // Update staff
    $updateResponse = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', 'STF_02'), [
            'name' => 'Siti Nurhaliza Updated',
            'email' => 'siti.updated@gmail.com',
            'phone_number' => '0123456789',
            'role' => 'staff',
        ]);

    $updateResponse->assertRedirect(route('admin.staff.index'));
    expect($newStaff->fresh()->name)->toBe('Siti Nurhaliza Updated');

    // Reset password
    $resetResponse = $this->actingAs($this->admin)
        ->post(route('admin.staff.reset_password', 'STF_02'), [
            'new_password' => 'resetPass456',
        ]);

    $resetResponse->assertRedirect(route('admin.staff.index'));
    expect(Hash::check('resetPass456', $newStaff->fresh()->password))->toBeTrue();

    // Delete staff
    $deleteResponse = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', 'STF_02'));

    $deleteResponse->assertRedirect(route('admin.staff.index'));
    expect(User::where('staff_id', 'STF_02')->exists())->toBeFalse();
});

test('admin can reset staff filters to show all roles', function () {
    $filteredResponse = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', [
            'search' => 'no-matching-staff',
            'role' => 'staff',
        ]));

    $filteredResponse->assertOk();
    $filteredResponse->assertSee('Reset');

    $resetResponse = $this->actingAs($this->admin)
        ->get(route('admin.staff.index'));

    $resetResponse->assertOk();
    $resetResponse->assertSee('HR_01');
    $resetResponse->assertSee('STF_01');
});

test('selecting all roles clears the staff search filter', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', [
            'search' => 'no-matching-staff',
            'role' => 'all',
        ]));

    $response->assertOk();
    $response->assertSee('HR_01');
    $response->assertSee('STF_01');
    $response->assertDontSee('no-matching-staff');
});

test('admin can add manual attendance and correct records recalculating period', function () {
    // Add manual attendance
    $response = $this->actingAs($this->admin)
        ->post(route('admin.attendance.store'), [
            'staff_id' => 'STF_01',
            'date' => '2026-02-10',
            'check_in' => '08:30',
            'check_out' => '17:30',
            'keterangan_staff' => 'Terlupa clock in kad tertinggal',
            'keterangan_admin' => 'Diluluskan oleh pentadbir',
        ]);

    $response->assertRedirect(route('admin.attendance.index'));

    $expectedId = 'STF_01-20260210-01';
    $record = Attendance::find($expectedId);
    expect($record)->not->toBeNull();
    expect($record->check_in)->toBe('08:30:00');
    expect($record->check_out)->toBe('17:30:00');
    expect($record->period)->toBe('09:00:00');

    // Admin updates / corrects record
    $updateResponse = $this->actingAs($this->admin)
        ->put(route('admin.attendance.update', $expectedId), [
            'date' => '2026-02-10',
            'check_in' => '09:00',
            'check_out' => '17:00',
            'keterangan_staff' => 'Pembetulan masa',
            'keterangan_admin' => 'Admin adjust masa rehat',
        ]);

    $updateResponse->assertRedirect(route('admin.attendance.index'));
    $updated = $record->fresh();
    expect($updated->check_in)->toBe('09:00:00');
    expect($updated->check_out)->toBe('17:00:00');
    expect($updated->period)->toBe('08:00:00');
});

test('admin can view monthly summary report', function () {
    User::create([
        'staff_id' => 'STF_02',
        'name' => 'Siti Binti Ahmad',
        'email' => 'STF_02@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '010213124',
        'role' => 'staff',
    ]);

    Attendance::create([
        'id' => 'STF_01-20260301-01',
        'staff_id' => 'STF_01',
        'check_in' => '09:00:00',
        'check_out' => '17:00:00',
        'date' => '2026-03-01',
        'period' => '08:00:00',
    ]);
    Attendance::create([
        'id' => 'STF_01-20260301-02',
        'staff_id' => 'STF_01',
        'check_in' => '17:00:00',
        'check_out' => '19:00:00',
        'date' => '2026-03-01',
        'period' => '02:00:00',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.attendance.summary', [
            'month' => 3,
            'year' => 2026,
            'staff_id' => '',
        ]));

    $response->assertStatus(200);
    $response->assertSee('Laporan Ringkasan Bulanan Staf');
    $response->assertSee('STF_01');
    $response->assertSee('STF_02');
    $response->assertSee('Khairul Amin Bin Mamat');
    $response->assertSee('10 Jam 00 Minit');
    $response->assertSee('2 Orang');
    $response->assertSee('1 Hari / 23 Hari');
    $response->assertSee('1 / 23');
    $response->assertSee('Score');
    $response->assertSee('Muat Turun PDF');
    $response->assertSee(route('admin.attendance.summary', [
        'month' => 3,
        'year' => 2026,
        'staff_id' => '',
        'download' => 'pdf',
    ]));
    $response->assertDontSee('window.print', false);
    $response->assertDontSee('attendance-print-layout', false);
});

test('admin can filter monthly summary report by staff', function () {
    User::create([
        'staff_id' => 'STF_02',
        'name' => 'Siti Binti Ahmad',
        'email' => 'STF_02@gmail.com',
        'password' => Hash::make('password123'),
        'phone_number' => '010213124',
        'role' => 'staff',
    ]);

    Attendance::create([
        'id' => 'STF_02-20260301-01',
        'staff_id' => 'STF_02',
        'check_in' => '09:00:00',
        'check_out' => '11:00:00',
        'date' => '2026-03-01',
        'period' => '02:00:00',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.attendance.summary', [
            'month' => 3,
            'year' => 2026,
            'staff_id' => 'STF_02',
        ]));

    $response->assertSee('1 Orang');
    $response->assertSee('02 Jam 00 Minit');
});

test('admin can download monthly summary as TCPDF PDF', function () {
    $monthStart = Carbon::create(2026, 3, 1);

    for ($day = 1; $day <= $monthStart->daysInMonth; $day++) {
        $date = $monthStart->copy()->day($day);
        Attendance::create([
            'id' => 'STF_01-'.$date->format('Ymd').'-01',
            'staff_id' => 'STF_01',
            'check_in' => '09:00:00',
            'check_out' => '17:00:00',
            'date' => $date->format('Y-m-d'),
            'period' => '08:00:00',
        ]);
    }

    $response = $this->actingAs($this->admin)
        ->get(route('admin.attendance.summary', [
            'month' => 3,
            'year' => 2026,
            'download' => 'pdf',
        ]));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
    $response->assertHeader('content-disposition', 'attachment; filename="attendance-summary-2026-03.pdf"');
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');

    preg_match_all('/\/MediaBox\s+\[([^\]]+)\]/', $response->getContent(), $mediaBoxes);
    expect($mediaBoxes[1])->toHaveCount(2);

    foreach ($mediaBoxes[1] as $mediaBox) {
        $dimensions = array_map('floatval', preg_split('/\s+/', trim($mediaBox)));
        expect($dimensions[2])->toBeLessThan($dimensions[3]);
    }
});
