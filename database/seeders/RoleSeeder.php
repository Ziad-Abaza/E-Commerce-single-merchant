<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $allPermissions = Permission::all();
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions($allPermissions);
    }
}
