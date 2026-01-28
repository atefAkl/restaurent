<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        $accountant = Role::firstOrCreate(['name' => 'accountant'], ['display_name' => 'Accountant']);
        $seller = Role::firstOrCreate(['name' => 'seller'], ['display_name' => 'Seller']);
        $kitchen = Role::firstOrCreate(['name' => 'kitchen'], ['display_name' => 'Kitchen']);

        // Example permissions
        $permissions = [
            'manage_users',
            'manage_settings',
            'view_reports',
            'manage_orders',
            'manage_inventory',
            'manage_cashier_sessions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Attach permissions to roles (basic mapping)
        $admin->permissions()->sync(Permission::pluck('id')->toArray());
        $accountant->permissions()->sync(Permission::whereIn('name', ['view_reports', 'manage_inventory'])->pluck('id')->toArray());
        $seller->permissions()->sync(Permission::whereIn('name', ['manage_orders', 'manage_cashier_sessions'])->pluck('id')->toArray());

        // Attach roles to existing seeded users by email (if present)
        $adminUser = User::where('email', 'admin@restaurant.com')->first();
        if ($adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$admin->id]);
        }

        $sellerUser = User::where('email', 'seller@restaurant.com')->first();
        if ($sellerUser) {
            $sellerUser->roles()->syncWithoutDetaching([$seller->id]);
        }

        $accountantUser = User::where('email', 'accountant@restaurant.com')->first();
        if ($accountantUser) {
            $accountantUser->roles()->syncWithoutDetaching([$accountant->id]);
        }
    }
}
