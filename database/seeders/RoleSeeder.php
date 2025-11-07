<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()['cache']->forget('spatie.permission.cache');

        // Super Admin — oversees all accounts, sets global settings, monitors activity
        // Full access to everything
        $superAdmin = Role::create([
            'name' => 'super_admin',
            'title' => 'Super Admin',
            'color' => '#FF6B6B'
        ]);
        $permissions = Permission::all();
        $superAdmin->availablePermissions()->sync($permissions);
        $superAdmin->syncPermissions($permissions);

        // Business Admin / Owner — can set up loyalty program, define rewards, 
        // manage customers and view reports
        $businessAdmin = Role::create([
            'name' => 'business_admin',
            'title' => 'Business Admin',
            'color' => '#4ECDC4'
        ]);
        // Business Admin gets most permissions except super admin level settings
        $businessAdmin->availablePermissions()->sync($permissions);
        $businessAdmin->syncPermissions($permissions);

        // Manager / Staff — manages day-to-day tasks like checking customers in, 
        // scanning codes, or issuing rewards at point of sale
        $manager = Role::create([
            'name' => 'manager',
            'title' => 'Manager / Staff',
            'color' => '#95E1D3'
        ]);
        // Manager gets limited permissions for day-to-day operations
        $managerPermissions = Permission::whereIn('name', [
            'view_user',
            'edit_user',
            'all_user'
        ])->get();
        $manager->availablePermissions()->sync($managerPermissions);
        $manager->syncPermissions($managerPermissions);

        // Customer (Member) — end-user who signs up for loyalty program, 
        // collects points, and redeems rewards
        $customer = Role::create([
            'name' => 'customer',
            'title' => 'Customer',
            'color' => '#F38181'
        ]);
        // Customers have minimal permissions - mainly viewing their own data
        // No permissions assigned by default for customers
    }
}
