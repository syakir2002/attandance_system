<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('staff.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle user login with email or staff_id.
     */
    public function login(Request $request): RedirectResponse
    {

        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your Email or Staff ID.',
            'password.required' => 'Please enter your password.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];

        // Email comparison follows the database collation; Staff ID must match exactly.
        $user = User::where('email', $loginInput)->first();
        if (! $user) {
            $staffUser = User::where('staff_id', $loginInput)->first();
            $user = $staffUser?->staff_id === $loginInput ? $staffUser : null;
        }

        if ($user) {
            $isValidPassword = false;

            if (Hash::check($password, $user->password)) {
                $isValidPassword = true;
            } elseif ($user->password === $password) {
                // If legacy plain text password, rehash and update
                $user->password = Hash::make($password);
                $user->save();
                $isValidPassword = true;
            }

            if ($isValidPassword) {
                Auth::login($user);
                $request->session()->regenerate();

                if ($user->isAdmin()) {
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('success', "Selamat Datang, {$user->name}!");
                }

                $request->session()->put('just_logged_in', true);

                return redirect()->intended(route('staff.dashboard'))
                    ->with('success', "Selamat Datang, {$user->name}!");
            }
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors([
                'login' => 'Email or Staff ID or password is incorrect.',
            ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Telah berjaya log keluar.');
    }
}
