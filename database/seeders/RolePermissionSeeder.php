<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        // $permissions = [
        //     'manage users',
        //     'verify documents',
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate(['name' => $permission]);
        // }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $verifier = Role::firstOrCreate(['name' => 'verifier']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // $admin->givePermissionTo(Permission::all());
        // $verifier->givePermissionTo('verify documents');

        // $firstUser = User::find(1);
        // if ($firstUser && !$firstUser->hasRole('admin')) {
        //     $firstUser->assignRole('admin');
        // }
    }
}
