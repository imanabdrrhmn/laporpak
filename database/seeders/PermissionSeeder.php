<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::firstOrCreate(['name' => 'verify_reports']);
        Permission::firstOrCreate(['name' => 'view_reports_penipuan']);
        Permission::firstOrCreate(['name' => 'view_reports_infrastruktur']);
    }
}
