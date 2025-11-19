<?php

namespace Database\Seeders;

use App\Constants\Constants;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin User
        $user1 = new User();
        $user1->first_name = 'Super';
        $user1->last_name = 'Admin';
        $user1->about = "This is the super admin of the Uzimate loyalty platform.";
        $user1->email = 'superadmin@gmail.com';
        $user1->password = Hash::make('admin123');
        $user1->email_verified_at = now();
        $user1->created_at = now();
        $user1->save();
        $user1->assignRole(Constants::SUPERADMIN);
        $user1->file()->create(['name' => 'avatar.png', 'path' => 'users/avatar.png', 'type' => 'profile']);

        // Business Admin User
        $user2 = new User();
        $user2->first_name = 'Merchant';
        $user2->last_name = 'Merchant';
        $user2->about = "This is a Merchant  who can set up loyalty programs and manage customers.";
        $user2->email = 'merchant@gmail.com';
        $user2->password = Hash::make('test123');
        $user2->email_verified_at = now();
        $user2->created_at = now();
        $user2->save();
        $user2->assignRole(Constants::Manager);
        $user2->file()->create(['name' => 'avatar.png', 'path' => 'users/avatar.png', 'type' => 'profile']);

        // Manager/Staff User
        $user3 = new User();
        $user3->first_name = 'admin';
        $user3->last_name = 'admin';
        $user3->about = "This is a admin member who handles day-to-day operations.";
        $user3->email = 'admin@gmail.com';
        $user3->password = Hash::make('test123');
        $user3->email_verified_at = now();
        $user3->created_at = now();
        $user3->save();
        $user3->assignRole(Constants::Admin);
        $user3->file()->create(['name' => 'avatar.png', 'path' => 'users/avatar.png', 'type' => 'profile']);

        // Customer User
        $user4 = new User();
        $user4->first_name = 'Customer';
        $user4->last_name = 'Member';
        $user4->about = "This is a customer member who collects points and redeems rewards.";
        $user4->email = 'user@gmail.com';
        $user4->password = Hash::make('test123');
        $user4->email_verified_at = now();
        $user4->created_at = now();
        $user4->save();
        $user4->assignRole(Constants::CUSTOMER);
        $user4->file()->create(['name' => 'avatar.png', 'path' => 'users/avatar.png', 'type' => 'profile']);
    }
}
