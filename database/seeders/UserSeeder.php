<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $owner = User::firstOrCreate(
            ['email' => 'ziad.h.abaza@gmail.com'],
            [
                'name' => 'Store Owner',
                'password' => Hash::make('123456789'),
                'phone' => '0100640397',
                'is_active' => true,
                'address' => 'alex',
                'email_verified_at' => now(),
            ]
        );

        $owner->assignRole('owner');

        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '010064039'.$i,
                    'is_active' => true,
                    'address' => 'alex, egypt',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
