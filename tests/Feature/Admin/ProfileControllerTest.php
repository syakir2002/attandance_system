<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createAdminForProfileTests(array $attributes = []): User
{
    return User::create(array_merge([
        'staff_id' => 'HR_01',
        'name' => 'Admin Sedia Ada',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '0123456789',
        'role' => 'admin',
    ], $attributes));
}

test('admin can view their profile page', function () {
    $admin = createAdminForProfileTests();

    $response = $this->actingAs($admin)->get(route('admin.profile.show'));

    $response->assertOk()
        ->assertSee('Profil Saya')
        ->assertSee('Admin Sedia Ada')
        ->assertSee('Tukar Kata Laluan');
});

test('admin can update profile details and upload a profile image', function () {
    Storage::fake('public');
    $admin = createAdminForProfileTests();

    $response = $this->actingAs($admin)->put(route('admin.profile.update'), [
        'name' => 'Nama Admin Baharu',
        'email' => 'ADMIN.NEW@EXAMPLE.COM',
        'phone_number' => '0198765432',
        'img_profile' => UploadedFile::fake()->image('admin.png'),
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success');

    $admin->refresh();

    expect($admin->name)->toBe('Nama Admin Baharu')
        ->and($admin->email)->toBe('admin.new@example.com')
        ->and($admin->phone_number)->toBe('0198765432')
        ->and($admin->img)->toContain('profile-images/');

    Storage::disk('public')->assertExists($admin->img);
});

test('admin profile update rejects an email address already used by another account', function () {
    $admin = createAdminForProfileTests();
    User::create([
        'staff_id' => 'STF_01',
        'name' => 'Nama Staf',
        'email' => 'staff@example.com',
        'password' => Hash::make('password123'),
        'role' => 'staff',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.profile.update'), [
        'name' => 'Nama Admin Baharu',
        'email' => 'staff@example.com',
        'phone_number' => '0198765432',
    ]);

    $response->assertSessionHasErrors('email');

    expect($admin->fresh()->name)->toBe('Admin Sedia Ada')
        ->and($admin->fresh()->email)->toBe('admin@example.com');
});

test('admin profile update rejects a non-image profile picture', function () {
    $admin = createAdminForProfileTests();

    $response = $this->actingAs($admin)->put(route('admin.profile.update'), [
        'name' => $admin->name,
        'email' => $admin->email,
        'phone_number' => $admin->phone_number,
        'img_profile' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('img_profile');

    expect($admin->fresh()->img)->toBeNull();
});

test('admin can change their password and wrong current passwords are rejected', function () {
    $admin = createAdminForProfileTests();

    $this->actingAs($admin)->put(route('admin.profile.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect()
        ->assertSessionHas('success');

    expect(Hash::check('newpassword123', $admin->fresh()->password))->toBeTrue();

    $this->actingAs($admin)->put(route('admin.profile.password'), [
        'current_password' => 'incorrect-password',
        'password' => 'anotherpassword123',
        'password_confirmation' => 'anotherpassword123',
    ])->assertSessionHasErrors('current_password');

    expect(Hash::check('newpassword123', $admin->fresh()->password))->toBeTrue();
});

test('staff cannot access the admin profile page', function () {
    $staff = User::create([
        'staff_id' => 'STF_01',
        'name' => 'Nama Staf',
        'email' => 'staff@example.com',
        'password' => Hash::make('password123'),
        'role' => 'staff',
    ]);

    $this->actingAs($staff)
        ->get(route('admin.profile.show'))
        ->assertRedirect(route('staff.dashboard'));
});
