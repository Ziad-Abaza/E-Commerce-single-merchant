<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // AssignUserRolesSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductDetailSeeder::class,
            ReviewSeeder::class,
            OrderSeeder::class,
            CartSeeder::class,
        ]);
    }
}
