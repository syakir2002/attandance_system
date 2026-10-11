<?php

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = User::create([
        'staff_id' => 'STF_99',
        'name' => 'Ahmad Staf',
        'email' => 'ahmad_staff@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '0198765432',
        'role' => 'staff',
    ]);
});

test('staff can access read-only calendar page', function () {
    $response = $this->actingAs($this->staff)->get(route('staff.calendar.index'));

    $response->assertStatus(200);
    $response->assertSee('Kalendar & Jadual Aktiviti Syarikat', false);
    $response->assertSee('id="staff-calendar"', false);
    $response->assertSee('Deskripsi & Butiran Acara', false);
    $response->assertSee('id="detail-description"', false);
    $response->assertDontSee('Tambah Tugasan Baharu');
});

test('staff can only fetch public calendar events via json', function () {
    // 1. Public event (status = 1)
    CalendarEvent::create([
        'title' => 'Hari Keluarga Syarikat',
        'description' => 'Acara tahunan untuk semua kakitangan.',
        'start_at' => now()->startOfDay(),
        'end_at' => now()->startOfDay()->addHours(8),
        'status' => 1,
        'display_style' => 'block',
        'timestamps' => now(),
    ]);

    // 2. Private event (status = 0)
    CalendarEvent::create([
        'title' => 'Mesyuarat Sulit Lembaga Pengarah',
        'description' => 'Perbincangan dalaman tertutup.',
        'start_at' => now()->startOfDay(),
        'end_at' => now()->startOfDay()->addHour(),
        'status' => 0,
        'display_style' => 'dot',
        'timestamps' => now(),
    ]);

    $response = $this->actingAs($this->staff)->getJson(route('staff.calendar.events'));

    $response->assertStatus(200);
    $response->assertJsonCount(1);
    $response->assertJsonFragment([
        'raw_title' => 'Hari Keluarga Syarikat',
        'description' => 'Acara tahunan untuk semua kakitangan.',
        'is_public' => true,
    ]);
    $response->assertJsonMissing([
        'raw_title' => 'Mesyuarat Sulit Lembaga Pengarah',
    ]);
});

test('guest cannot access staff calendar', function () {
    $response = $this->get(route('staff.calendar.index'));
    $response->assertRedirect(route('login'));

    $responseEvents = $this->getJson(route('staff.calendar.events'));
    $responseEvents->assertStatus(401);
});
