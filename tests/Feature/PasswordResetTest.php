<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'staff_id' => 'STF_99',
        'name' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'password' => Hash::make('oldpassword123'),
        'phone_number' => '0123456789',
        'role' => 'staff',
    ]);
});

test('login page displays forgot password link', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee(route('password.request'));
    $response->assertSee('Lupa kata laluan?');
});

test('guest can view reset password page', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
    $response->assertSee('Tetapkan Semula Kata Laluan');
    $response->assertDontSee('name="token"', false);
});

test('guest can reset password directly using registered email', function () {
    $response = $this->post(route('password.update'), [
        'login' => 'siti@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');

    $this->user->refresh();
    expect(Hash::check('newpassword123', $this->user->password))->toBeTrue();
});

test('guest can reset password directly using staff_id', function () {
    $response = $this->post(route('password.update'), [
        'login' => 'STF_99',
        'password' => 'brandnewpass123',
        'password_confirmation' => 'brandnewpass123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');

    $this->user->refresh();
    expect(Hash::check('brandnewpass123', $this->user->password))->toBeTrue();
});

test('cannot reset password with non-existent email or staff_id', function () {
    $response = $this->from(route('password.request'))->post(route('password.update'), [
        'login' => 'unknown_staff_id',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHasErrors(['login']);

    $this->user->refresh();
    expect(Hash::check('oldpassword123', $this->user->password))->toBeTrue();
});

test('cannot reset password with password confirmation mismatch', function () {
    $response = $this->from(route('password.request'))->post(route('password.update'), [
        'login' => 'STF_99',
        'password' => 'newpassword123',
        'password_confirmation' => 'mismatch123',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHasErrors(['password']);

    $this->user->refresh();
    expect(Hash::check('oldpassword123', $this->user->password))->toBeTrue();
});
