<?php

namespace Database\Seeders;

use App\Constants\Constants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()['cache']->forget('spatie.permission.cache');


        $superAdmin = Role::create([
            'name' => Constants::SUPERADMIN,
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


        $businessAdmin = Role::create([
            'name' => Constants::Merchant,
            'title' => 'Merchant',
            'color' => '#4ECDC4'
        ]);

        $crudModules = [
            'site',
            'site_user',
            'offer',
            'customer_scan',
            'offer_scan',
            'reward_rule',
            'point_award',
            'spin_history',
            'survey',
            // 'customer_log',
            'invite_friend',
            'voucher',
            'inbox',
            'feedback',
            'permission',
            'notification',
        ];

        $merchantModulePrefixes = $crudModules;

        $merchantPermissions = $crudPermissions($merchantModulePrefixes);

        $businessAdmin->availablePermissions()->sync($merchantPermissions);
        $businessAdmin->syncPermissions($merchantPermissions);


        $customer = Role::create([
            'name' => Constants::CUSTOMER,
            'title' => 'Customer',
            'color' => '#F38181'
        ]);
        $customerPermissions = Permission::where('name', 'site_setting')->get();
        $customer->availablePermissions()->sync($customerPermissions);
        $customer->syncPermissions($customerPermissions);
    }
}
