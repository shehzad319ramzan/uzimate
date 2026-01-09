<?php

namespace Database\Seeders;

use App\Models\Inbox;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inboxItems = [
            [
                'name' => 'Customer Support Inbox',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'General Inquiries',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'name' => 'Technical Support',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'name' => 'Sales Inquiries',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'name' => 'Billing Questions',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'name' => 'Account Issues',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'name' => 'Feature Requests',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'name' => 'Partnership Inquiries',
                'status' => '1',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'name' => 'Archived Inbox',
                'status' => '0',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'name' => 'Old Inquiries',
                'status' => '0',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
        ];

        foreach ($inboxItems as $item) {
            Inbox::create($item);
        }

        $this->command->info('Inbox seeded successfully! Created ' . count($inboxItems) . ' inbox items.');
    }
}

