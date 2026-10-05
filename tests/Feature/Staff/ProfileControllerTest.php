<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('staff can upload a profile image while updating profile details', function () {
    Storage::fake('public');

    $user = User::create([
        'staff_id' => 'STF_01',
        'name' => 'Nama Staf',
        'email' => 'staff@example.com',
        'password' => 'password',
        'role' => 'staff',
    ]);

    $response = $this->actingAs($user)->put(route('staff.profile.update'), [
        'name' => 'Nama Staf Baharu',
        'email' => 'staff@example.com',
        'phone_number' => '0123456789',
        'img_profile' => UploadedFile::fake()->image('profile.png'),
    ]);

    $response->assertRedirect();

    $user->refresh();

    expect($user->name)->toBe('Nama Staf Baharu')
        ->and($user->img)->toContain('profile-images/');

    Storage::disk('public')->assertExists($user->img);
});

test('staff cannot upload a non-image as a profile picture', function () {
    $user = User::create([
        'staff_id' => 'STF_02',
        'name' => 'Nama Staf',
        'email' => 'staff2@example.com',
        'password' => 'password',
        'role' => 'staff',
    ]);

    $response = $this->actingAs($user)->put(route('staff.profile.update'), [
        'name' => 'Nama Staf',
        'email' => 'staff2@example.com',
        'phone_number' => '',
        'img_profile' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('img_profile');

    expect($user->fresh()->img)->toBeNull();
});
