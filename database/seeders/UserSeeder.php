<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0500000000',
            'is_active' => true,
        ]);

        // Create accountant user
        User::create([
            'name' => 'المحاسب',
            'email' => 'accountant@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'accountant',
            'phone' => '0500000001',
            'is_active' => true,
        ]);

        // Create seller user
        User::create([
            'name' => 'البائع',
            'email' => 'seller@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '0500000002',
            'is_active' => true,
        ]);

        // Create kitchen user
        User::create([
            'name' => 'عامل المطبخ',
            'email' => 'kitchen@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'kitchen',
            'phone' => '0500000003',
            'is_active' => true,
        ]);
    }
}
