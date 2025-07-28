<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'manage_users',
            'manage_products',
            'manage_cart',
            'manage_payment',
            'manage_orders',
            'manage_reviews',
            'manage_settings',
            'manage_categories',
            'manage_wishlist',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
