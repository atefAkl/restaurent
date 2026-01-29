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
        // Get first user ID for created_by/updated_by fields
        $creatorId = User::first()->id;

        // Temporarily set auth user for the Blameable trait
        \Illuminate\Support\Facades\Auth::loginUsingId($creatorId);

        // Create roles with proper firstOrCreate syntax
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'created_by' => $creatorId,
                'updated_by' => $creatorId
            ]
        );

        $accountant = Role::firstOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'Accountant',
                'created_by' => $creatorId,
                'updated_by' => $creatorId
            ]
        );

        $seller = Role::firstOrCreate(
            ['name' => 'seller'],
            [
                'display_name' => 'Seller',
                'created_by' => $creatorId,
                'updated_by' => $creatorId
            ]
        );

        $kitchen = Role::firstOrCreate(
            ['name' => 'kitchen'],
            [
                'display_name' => 'Kitchen',
                'created_by' => $creatorId,
                'updated_by' => $creatorId
            ]
        );

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
            Permission::firstOrCreate(
                ['name' => $perm],
                [
                    'created_by' => $creatorId,
                    'updated_by' => $creatorId
                ]
            );
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
