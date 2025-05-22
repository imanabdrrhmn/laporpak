<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;



class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->join(', '),
                'avatar_url' => $user && $user->avatar
                    ? asset('storage/' . $user->avatar)
                    : asset('/Default-Profile.png'),
            ];
        });

        return inertia('Admin/UserIndex', [
            'users' => $users,
            'roles' => Role::pluck('name'),
        ]);
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role berhasil diperbarui.');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        Role::create([
            'name' => Str::slug($request->name, '_'), 
        ]);

        return redirect()->back()->with('success', 'Role berhasil dibuat.');
    }
    public function destroyRole( Role $role)
    {
        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }

    public function editPermissions(User $user)
    {
        $permissions = Permission::all();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'permissions' => $user->permissions->pluck('name'),
            ],
            'allPermissions' => $permissions->pluck('name'),
        ]);
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncPermissions($request->input('permissions', []));

        return response()->json(['success' => true]);
    }
}
