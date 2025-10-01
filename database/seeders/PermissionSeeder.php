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
            'manage_orders',
            'manage_reviews',
            'manage_settings',
            'manage_categories',
            'manage_wishlist',
            'manage_roles',
            'manage_permissions',
            'manage_contact_messages',
            'manage_promo_codes',
            'manage_privacy',
            'view_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
