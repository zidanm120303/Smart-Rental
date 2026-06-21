<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('users.manage') || auth()->user()->hasPermission('dashboard.view'), 403, 'Anda tidak memiliki akses ke Staff.');

        return view('pages.staff.index', [
            'staff' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::withCount('users')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403, 'Anda tidak memiliki akses menambah staf.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);
        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('staff.index')->with('success', 'Staf berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403, 'Anda tidak memiliki akses mengubah staf.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('staff.index')->with('success', 'Staf berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403, 'Anda tidak memiliki akses menghapus staf.');

        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('staff.index')->with('success', 'Staf berhasil dihapus.');
    }
}
