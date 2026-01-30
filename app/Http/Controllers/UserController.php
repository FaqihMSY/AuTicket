<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('department')
            ->orderByRaw("FIELD(role, 'admin', 'pengawas', 'reviewer', 'staff')")
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $departments = Department::all();

        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,pengawas,reviewer,staff',
            'department_id' => 'nullable|exists:departments,id',
            'specialization' => 'nullable|string|max:255|required_if:role,staff',
        ]);

        $userData = collect($validated)->except('specialization')->toArray();
        $userData['password'] = Hash::make($userData['password']);

        if ($validated['role'] === 'staff') {
            $userData['department_id'] = null;
        }

        $user = User::create($userData);

        if ($validated['role'] === 'staff') {
            $user->auditor()->create([
                'specialization' => $validated['specialization'] ?? null,
                'is_active' => true,
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $departments = Department::all();

        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,pengawas,reviewer,staff',
            'department_id' => 'nullable|exists:departments,id',
            'specialization' => 'nullable|string|max:255|required_if:role,staff',
        ]);

        $userData = collect($validated)->except('specialization')->toArray();

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }

        // Set department_id to NULL for staff (Auditor) role
        if ($validated['role'] === 'staff') {
            $userData['department_id'] = null;
        }

        $user->update($userData);

        // Handle Auditor Profile
        if ($validated['role'] === 'staff') {
            // Create or update auditor profile
            $user->auditor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $validated['specialization'] ?? null,
                    'is_active' => true,
                ]
            );
        } else {
            // If outdated auditor profile exists but role is no longer staff, we might keep it but deactivate, 
            // or just leave it. For now, let's leave it but it won't be used since checks are usually role-based.
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
