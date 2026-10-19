<?php

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'staff_id' => 'ADMIN_01',
        'name' => 'Pengurus Admin',
        'email' => 'admin_test@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '0123456789',
        'role' => 'admin',
    ]);
});

test('admin can access calendar page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.calendar.index'));

    $response->assertStatus(200);
    $response->assertSee('Calendar');
    $response->assertSee('Tambah Tugasan Baharu');
    $response->assertSee('id="calendar"', false);
    $response->assertSee('Public (1)');
    $response->assertSee('Private (0)');
});

test('admin can fetch calendar events json with status filtering', function () {
    CalendarEvent::create([
        'title' => 'Tugasan Awam',
        'start_at' => now(),
        'end_at' => now()->addHour(),
        'status' => 1,
        'timestamps' => now(),
    ]);

    CalendarEvent::create([
        'title' => 'Tugasan Peribadi',
        'start_at' => now(),
        'end_at' => now()->addHour(),
        'status' => 0,
        'timestamps' => now(),
    ]);

    $allResponse = $this->actingAs($this->admin)->getJson(route('admin.calendar.events'));
    $allResponse->assertStatus(200);
    $allResponse->assertJsonCount(2);

    $publicResponse = $this->actingAs($this->admin)->getJson(route('admin.calendar.events', ['status' => 'public']));
    $publicResponse->assertStatus(200);
    $publicResponse->assertJsonCount(1);
    $publicResponse->assertJsonFragment(['raw_title' => 'Tugasan Awam']);

    $privateResponse = $this->actingAs($this->admin)->getJson(route('admin.calendar.events', ['status' => 'private']));
    $privateResponse->assertStatus(200);
    $privateResponse->assertJsonCount(1);
    $privateResponse->assertJsonFragment(['raw_title' => 'Tugasan Peribadi']);
});

test('admin can store a new task with public or private status', function () {
    $payloadPublic = [
        'title' => 'Taklimat Awam Semua Staf',
        'description' => 'Sila berkumpul di Dewan Utama pada jam 9 pagi.',
        'start_at' => now()->format('Y-m-d H:i:s'),
        'end_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'status' => 1,
        'display_style' => 'block',
    ];

    $response = $this->actingAs($this->admin)->postJson(route('admin.calendar.store'), $payloadPublic);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('callendar_info', [
        'title' => 'Taklimat Awam Semua Staf',
        'description' => 'Sila berkumpul di Dewan Utama pada jam 9 pagi.',
        'status' => 1,
        'display_style' => 'block',
    ]);

    $payloadPrivate = [
        'title' => 'Catatan Peribadi Pentadbir',
        'start_at' => now()->format('Y-m-d H:i:s'),
        'end_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'status' => 0,
        'display_style' => 'dot',
    ];

    $response2 = $this->actingAs($this->admin)->postJson(route('admin.calendar.store'), $payloadPrivate);

    $response2->assertStatus(200);
    $this->assertDatabaseHas('callendar_info', [
        'title' => 'Catatan Peribadi Pentadbir',
        'status' => 0,
        'display_style' => 'dot',
    ]);
});

test('admin can delete a task via calendar events API', function () {
    $event = CalendarEvent::create([
        'title' => 'Tugasan Untuk Dipadam',
        'start_at' => now(),
        'end_at' => now()->addHour(),
        'status' => 0,
        'timestamps' => now(),
    ]);

    $response = $this->actingAs($this->admin)->deleteJson(route('admin.calendar.destroy', $event->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseMissing('callendar_info', [
        'id' => $event->id,
    ]);
});

test('admin can fetch terengganu public holidays in calendar events', function () {
    Http::fake([
        '*holidays*' => Http::response([
            'data' => [
                [
                    'name' => 'Hari Ulang Tahun Pertabalan Sultan Terengganu',
                    'date' => '2026-03-04',
                    'day_name' => 'Wednesday',
                    'is_subject_to_change' => false,
                ],
            ],
            'meta' => [
                'year' => 2026,
                'count' => 1,
                'state' => 'TRG',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)->getJson(route('admin.calendar.events', [
        'start' => '2026-03-01T00:00:00',
        'end' => '2026-03-31T23:59:59',
        'status' => 'all',
    ]));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'raw_title' => 'Hari Ulang Tahun Pertabalan Sultan Terengganu',
        'source' => 'holiday',
        'state_code' => 'TRG',
    ]);
});

test('admin can filter calendar events to only holidays', function () {
    CalendarEvent::create([
        'title' => 'Tugasan Pejabat',
        'start_at' => '2026-03-05 10:00:00',
        'end_at' => '2026-03-05 11:00:00',
        'status' => 1,
        'timestamps' => now(),
    ]);

    Http::fake([
        '*holidays*' => Http::response([
            'data' => [
                [
                    'name' => 'Hari Ulang Tahun Pertabalan Sultan Terengganu',
                    'date' => '2026-03-04',
                    'day_name' => 'Wednesday',
                    'is_subject_to_change' => false,
                ],
            ],
            'meta' => [
                'year' => 2026,
                'count' => 1,
                'state' => 'TRG',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)->getJson(route('admin.calendar.events', [
        'start' => '2026-03-01T00:00:00',
        'end' => '2026-03-31T23:59:59',
        'status' => 'holidays',
    ]));

    $response->assertStatus(200);
    $response->assertJsonCount(1);
    $response->assertJsonFragment([
        'source' => 'holiday',
        'raw_title' => 'Hari Ulang Tahun Pertabalan Sultan Terengganu',
    ]);
    $response->assertJsonMissing([
        'raw_title' => 'Tugasan Pejabat',
    ]);
});
