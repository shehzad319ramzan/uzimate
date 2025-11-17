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

        $crudPermissions = function (array $prefixes, array $actions = ['view', 'add', 'edit', 'delete']) {
            return Permission::where(function ($query) use ($prefixes, $actions) {
                foreach ($prefixes as $prefix) {
                    foreach ($actions as $action) {
                        $query->orWhere('name', "{$action}_{$prefix}");
                    }
                }
            })->get();
        };

        // Business Admin / Owner — can set up loyalty program, define rewards,
        // manage customers and view reports
        $businessAdmin = Role::create([
            'name' => 'merchant',
            'title' => 'Merchant',
            'color' => '#4ECDC4'
        ]);
        // Modules used inside PermissionSeeder; keep same prefixes so sidebar @can checks stay in sync.
        $crudModules = [
            'merchant',
            'site',
            'site_user',
            'offer',
            'customer_scan',
            'offer_scan',
            'point_award',
            'spin_history',
            'customer_log',
            'inbox',
            'feedback',
            'permission',
        ];

        $merchantModulePrefixes = $crudModules;

        $merchantPermissions = $crudPermissions($merchantModulePrefixes);

        $businessAdmin->availablePermissions()->sync($merchantPermissions);
        $businessAdmin->syncPermissions($merchantPermissions);

        // Manager / Staff — manages day-to-day tasks like checking customers in,
        // scanning codes, or issuing rewards at point of sale
        $manager = Role::create([
            'name' => 'admin',
            'title' => 'Admin',
            'color' => '#95E1D3'
        ]);
        // Manager gets limited permissions for day-to-day operations
        $managerModulePrefixes = [
            'offer',
            'customer_scan',
            'offer_scan',
            'point_award',
            'spin_history',
            'customer_log',
            'inbox',
            'feedback',
        ];

        $managerPermissions = $crudPermissions($managerModulePrefixes);
        $manager->availablePermissions()->sync($managerPermissions);
        $manager->syncPermissions($managerPermissions);

        // Customer (Member) — end-user who signs up for loyalty program,
        // collects points, and redeems rewards
        $customer = Role::create([
            'name' => 'customer',
            'title' => 'Customer',
            'color' => '#F38181'
        ]);
        // Customers only get site settings permission for now
        $customerPermissions = Permission::where('name', 'site_setting')->get();
        $customer->availablePermissions()->sync($customerPermissions);
        $customer->syncPermissions($customerPermissions);
    }
}
