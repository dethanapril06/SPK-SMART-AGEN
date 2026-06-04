<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->get();

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', Rule::in(['admin', 'calon_agen'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', Rule::in(['admin', 'calon_agen'])],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
            'role.required'  => 'Role wajib dipilih.',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah hapus akun admin default (id: 1)
        if ($user->id === 1) {
            return redirect()
                ->route('admin.user.index')
                ->with('error', 'Akun admin default tidak dapat dihapus.');
        }

        // Cegah hapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.user.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        // Cegah reset akun admin default (id: 1)
        if ($user->id === 1) {
            return redirect()
                ->route('admin.user.index')
                ->with('error', 'Password akun admin default tidak dapat direset.');
        }

        $user->update([
            'password' => Hash::make('password'),
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with('success', "Password akun {$user->name} berhasil direset ke password default.");
    }
}