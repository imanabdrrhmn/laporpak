<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'), // ubah password sesuai kebutuhan
                'email_verified_at' => Carbon::now(),
            ]
        );

        // Assign role admin
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
