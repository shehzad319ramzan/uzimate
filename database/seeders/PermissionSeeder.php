<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basicPermissions = [
            'Users' => [
                ['name' => 'all_user', 'title' => 'View all users'],
                ['name' => 'add_user', 'title' => 'Add new user'],
                ['name' => 'view_user', 'title' => 'View user details'],
                ['name' => 'edit_user', 'title' => 'Edit user'],
                ['name' => 'delete_user', 'title' => 'Delete user'],
            ],
            'Roles' => [
                ['name' => 'all_role', 'title' => 'All Roles'],
                ['name' => 'add_role', 'title' => 'Add Role'],
                ['name' => 'edit_role', 'title' => 'Edit Role'],
                ['name' => 'delete_role', 'title' => 'Delete Role'],
                ['name' => 'assign_permission', 'title' => 'Assign Permission'],
            ],
            'Website Settings' => [
                ['name' => 'website_setting', 'title' => 'Website Setting'],
            ],
        ];

        foreach ($basicPermissions as $category => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    ['title' => $permission['title'], 'category' => $category]
                );
            }
        }

        $crudModules = [
            'App User' => 'app_user',
            'Merchants' => 'merchant',
            'Sites' => 'site',
            'Site Users' => 'site_user',
            'Offers' => 'offer',
            'Customer Scans' => 'customer_scan',
            'Offer Scans' => 'offer_scan',
            'Point Awards' => 'point_award',
            'Spin History' => 'spin_history',
            'Customer Logs' => 'customer_log',
            'Inbox' => 'inbox',
            'Feedbacks' => 'feedback',
            'Permissions' => 'permission',
        ];

        foreach ($crudModules as $category => $prefix) {
            $crudPermissions = [
                ['name' => "view_{$prefix}", 'title' => "View {$category}"],
                ['name' => "add_{$prefix}", 'title' => "Add {$category}"],
                ['name' => "edit_{$prefix}", 'title' => "Edit {$category}"],
                ['name' => "delete_{$prefix}", 'title' => "Delete {$category}"],
            ];

            foreach ($crudPermissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    ['title' => $permission['title'], 'category' => $category]
                );
            }
        }
    }
}
