<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

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
            SettingSeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
            // ProductDetailSeeder::class,
            // ReviewSeeder::class,
            // OrderSeeder::class,
            // CartSeeder::class,
            WishlistCategorySeeder::class,
            WishlistItemSeeder::class,
            PolicySeeder::class,
            ProductionSeeder::class,
        ]);

        // After seeding, reset demo folder
        $demoPath = public_path('images/demo');
        $backupPath = public_path('images/demo-backup');

        // Delete old demo folder if exists
        if (File::exists($demoPath)) {
            File::deleteDirectory($demoPath);
        }

        // Copy backup as demo
        if (File::exists($backupPath)) {
            File::copyDirectory($backupPath, $demoPath);
        }
    }
}
