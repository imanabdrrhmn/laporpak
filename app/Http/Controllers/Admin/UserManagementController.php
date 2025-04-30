<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        return inertia('Admin/UserIndex', [
            'users' => User::with('roles')->get(),
            'roles' => Role::pluck('name')  
        ]);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role berhasil diperbarui.');
    }
}
