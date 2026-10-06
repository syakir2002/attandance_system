<?php

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

test('admin layout renders responsive collapsible sidebar and mobile toggle controls', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);

    // Mobile backdrop overlay
    $response->assertSee('id="admin-sidebar-backdrop"', false);

    // Desktop sidebar spacer element
    $response->assertSee('id="admin-sidebar-spacer"', false);

    // Collapsible sidebar drawer with responsive translation classes
    $response->assertSee('id="admin-sidebar"', false);
    $response->assertSee('-translate-x-full lg:translate-x-0', false);

    // Mobile hamburger toggle button in header
    $response->assertSee('id="admin-sidebar-toggle"', false);
    $response->assertSee('aria-controls="admin-sidebar"', false);

    // Mobile close button in sidebar header
    $response->assertSee('id="admin-sidebar-close"', false);

    // Navigation links present
    $response->assertSee(route('admin.dashboard'));
    $response->assertSee(route('admin.staff.index'));
    $response->assertSee(route('admin.profile.show'));
    $response->assertSee(route('admin.attendance.index'));
    $response->assertSee(route('admin.attendance.summary'));
});

test('all admin pages include mobile responsive sidebar controls', function (string $routeName) {
    $response = $this->actingAs($this->admin)->get(route($routeName));

    $response->assertStatus(200);
    $response->assertSee('id="admin-sidebar-toggle"', false);
    $response->assertSee('id="admin-sidebar"', false);
    $response->assertSee('id="admin-sidebar-backdrop"', false);
})->with([
    'admin.dashboard',
    'admin.staff.index',
    'admin.profile.show',
    'admin.attendance.index',
    'admin.attendance.summary',
]);
