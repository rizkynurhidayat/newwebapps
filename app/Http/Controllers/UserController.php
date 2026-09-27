<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $allUsers = User::all();
        $summary = [
            'total' => $allUsers->count(),
            'admins' => $allUsers->where('role', UserRole::Admin)->count(),
            'staff_qc' => $allUsers->where('role', UserRole::Staff)->count(),
            'supervisors' => $allUsers->where('role', UserRole::Employee)->count(),
        ];

        return view('admin.users.index', compact('users', 'summary'));
    }

    public function create(): View
    {
        $roles = UserRole::cases();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna [{$user->name}] berhasil didaftarkan sebagai {$user->role->label()}.");
    }

    public function edit(User $user): View
    {
        $roles = UserRole::cases();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna [{$user->name}] berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tindakan ditolak! Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isAdmin() && User::where('role', UserRole::Admin)->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tindakan ditolak! Sistem harus memiliki minimal satu Super Administrator aktif.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna [{$name}] berhasil dihapus.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tindakan ditolak! Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
            ->with('success', "Status akun [{$user->name}] berhasil {$statusText}.");
    }
}
