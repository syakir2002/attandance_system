<?php

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
        'status' => 1,
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
