<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    /**
     * Display a listing of staff with search and pagination.
     */
    public function index(Request $request): View
    {
        $showAllRoles = $request->input('role') === 'all';
        $search = $showAllRoles ? '' : trim($request->input('search', ''));
        $roleFilter = 'staff';
        $statusFilter = $request->input('status', 'active');

        $query = User::query();

        if ($statusFilter === 'trashed') {
            $query->onlyTrashed();
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('staff_id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $trashedCount = User::onlyTrashed()->where('role', 'staff')->count();

        return view('admin.staff.index', compact('users', 'search', 'roleFilter', 'statusFilter', 'trashedCount'));
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $trashedStaff = User::onlyTrashed()
            ->where(function ($q) use ($request) {
                $q->where('staff_id', $request->input('staff_id'))
                    ->orWhere('email', $request->input('email'));
            })
            ->first();

        if ($trashedStaff) {
            return back()->withInput()->with('error', "Staff ID atau Emel ini pernah didaftarkan tetapi telah dipadam (soft delete). Sila pulihkan akaun tersebut daripada senarai 'Staf Dipadam'.");
        }

        $validated = $request->validate([
            'staff_id' => ['required', 'string', 'max:50', 'unique:user,staff_id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['nullable', 'in:staff'],
        ], [
            'staff_id.unique' => 'Staff ID ini telah digunakan.',
            'email.unique' => 'Emel ini telah digunakan.',
            'password.min' => 'Kata laluan mestilah sekurang-kurangnya 6 aksara.',
        ]);

        User::create([
            'staff_id' => strtoupper(trim($validated['staff_id'])),
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => trim($validated['phone_number'] ?? ''),
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Staf baharu ({$validated['staff_id']}) berjaya didaftarkan.");
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, string $staffId): RedirectResponse
    {
        $user = User::findOrFail($staffId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('user', 'email')->ignore($user->staff_id, 'staff_id')],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'in:staff'],
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => trim($validated['phone_number'] ?? ''),
            'role' => $user->isAdmin() ? 'admin' : 'staff',
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Maklumat staf {$user->staff_id} berjaya dikemas kini.");
    }

    /**
     * Reset staff password.
     */
    public function resetPassword(Request $request, string $staffId): RedirectResponse
    {
        $user = User::findOrFail($staffId);

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.min' => 'Kata laluan baharu mestilah sekurang-kurangnya 6 aksara.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Kata laluan untuk staf {$user->staff_id} ({$user->name}) berjaya ditetapkan semula.");
    }

    /**
     * Remove the specified staff member from storage (Soft Delete).
     */
    public function destroy(string $staffId): RedirectResponse
    {
        $user = User::findOrFail($staffId);

        // Check if deleting self
        if (auth()->id() === $user->staff_id) {
            return back()->with('error', 'Anda tidak boleh memadam akaun anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', "Akaun staf {$user->staff_id} ({$userName}) telah dipadam.");
    }

    /**
     * Restore the specified soft-deleted staff member.
     */
    public function restore(string $staffId): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($staffId);
        $user->restore();

        return redirect()->route('admin.staff.index', ['status' => 'active'])
            ->with('success', "Akaun staf {$user->staff_id} ({$user->name}) telah berjaya dipulihkan.");
    }
}
