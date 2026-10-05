<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the authenticated admin profile.
     */
    public function show(): View
    {
        $user = Auth::user();

        return view('admin.profile', compact('user'));
    }

    /**
     * Update the authenticated admin profile details.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('user', 'email')->ignore($user->staff_id, 'staff_id')],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'img_profile' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $updates = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => trim($validated['phone_number'] ?? ''),
        ];
        $previousProfileImage = null;

        if ($request->hasFile('img_profile')) {
            $previousProfileImage = $user->img;
            $updates['img'] = $request->file('img_profile')->store('profile-images', 'public');
        }

        $user->update($updates);

        if ($previousProfileImage !== null) {
            Storage::disk('public')->delete($previousProfileImage);
        }

        return back()->with('success', 'Maklumat profil berjaya dikemas kini.');
    }

    /**
     * Update the authenticated admin password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Sila masukkan kata laluan semasa.',
            'password.required' => 'Sila masukkan kata laluan baharu.',
            'password.min' => 'Kata laluan baharu mestilah sekurang-kurangnya 6 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan baharu tidak sepadan.',
        ]);

        $isCurrentPasswordValid = Hash::check($validated['current_password'], $user->password)
            || $user->password === $validated['current_password'];

        if (! $isCurrentPasswordValid) {
            return back()->withErrors(['current_password' => 'Kata laluan semasa tidak tepat.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata laluan berjaya dikemas kini.');
    }
}
