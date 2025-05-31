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

        return inertia('Admin/User/UserIndex', [
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

        $allProvinces = [
            'Aceh',
            'Bali',
            'Banten',
            'Bengkulu',
            'Gorontalo',
            'Jakarta',
            'Jambi',
            'Jawa Barat',
            'Jawa Tengah',
            'Jawa Timur',
            'Kalimantan Barat',
            'Kalimantan Selatan',
            'Kalimantan Tengah',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Kepulauan Bangka Belitung',
            'Kepulauan Riau',
            'Lampung',
            'Maluku',
            'Maluku Utara',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Papua',
            'Papua Barat',
            'Riau',
            'Sulawesi Barat',
            'Sulawesi Selatan',
            'Sulawesi Tengah',
            'Sulawesi Tenggara',
            'Sulawesi Utara',
            'Sumatera Barat',
            'Sumatera Selatan',
            'Sumatera Utara',
            'Yogyakarta',
        ];

        $allRegionsFromReports = \DB::table('reports')
            ->select('region')
            ->distinct()
            ->whereNotNull('region')
            ->pluck('region')
            ->toArray();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'permissions' => $user->permissions->pluck('name'),
                'allowed_regions' => $user->allowed_regions ?? [],
                'avatar_url' => $user->avatar 
                ? asset('storage/' . $user->avatar) 
                : asset('/Default-Profile.png'),
            ],
            'allPermissions' => $permissions->pluck('name'),
            'allRegions' => $allProvinces,
            'allRegionsFromReports' => $allRegionsFromReports,
        ]);
    }


    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'allowed_regions' => ['array'],
            'allowed_regions.*' => ['string'], 
        ]);

        $user->syncPermissions($request->input('permissions', []));
        $user->allowed_regions = $request->input('allowed_regions', []);
        $user->save();

        return response()->json(['success' => true]);
    }
}