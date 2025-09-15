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

        // Create owner role with all permissions
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions($allPermissions);

        // Create admin role with dashboard and management permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'view_dashboard',
            'manage_products',
            'manage_orders',
            'manage_categories',
            'manage_users',
            'manage_reviews',
        ];
        $admin->syncPermissions($adminPermissions);

        $customer = Role::firstOrCreate(['name' => 'customer']);
    }
}
