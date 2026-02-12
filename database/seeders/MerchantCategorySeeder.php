<?php

namespace Database\Seeders;

use App\Models\MerchantCategory;
use Illuminate\Database\Seeder;

class MerchantCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Drinks', 'status' => '1'],
            ['name' => 'Fashion', 'status' => '1'],
            ['name' => 'Travel', 'status' => '1'],
            ['name' => 'Retail', 'status' => '1'],
            ['name' => 'Health & Beauty', 'status' => '1'],
            ['name' => 'Entertainment', 'status' => '1'],
        ];

        foreach ($categories as $cat) {
            MerchantCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['status' => $cat['status']]
            );
        }
    }
}
