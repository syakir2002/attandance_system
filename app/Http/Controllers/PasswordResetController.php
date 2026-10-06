<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Display the reset password form.
     */
    public function showResetForm(): View
    {
        return view('auth.reset-password');
    }

    /**
     * Reset the user's password directly without tokens.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'login.required' => 'Sila masukkan Emel atau Staff ID anda.',
            'password.required' => 'Sila masukkan kata laluan baharu.',
            'password.min' => 'Kata laluan baharu mestilah sekurang-kurangnya 6 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan baharu tidak sepadan.',
        ]);

        $loginInput = trim($validated['login']);

        // Check if user exists by email or staff_id
        $user = User::where('email', $loginInput)->first();
        if (! $user) {
            $user = User::where('staff_id', $loginInput)->first();
        }

        if (! $user) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Tiada akaun ditemui dengan Emel atau Staff ID tersebut.',
                ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with(
            'success',
            "Kata laluan untuk akaun {$user->staff_id} ({$user->name}) telah berjaya ditetapkan semula. Sila log masuk menggunakan kata laluan baharu."
        );
    }
}
