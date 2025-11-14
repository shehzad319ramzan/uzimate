<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Merchant 1
        $merchant1 = new Merchant();
        $merchant1->name = 'McDonald';
        $merchant1->max_sites = 3;
        $merchant1->spin_after_days = 7;
        $merchant1->scan_after_hours = 12;
        $merchant1->use_other_merchant_points = true;
        $merchant1->status = '1';
        $merchant1->created_at = now();
        $merchant1->updated_at = now();
        $merchant1->save();
        $merchant1->file()->create([
            'name' => 'logo1.png',
            'path' => 'merchants/logos/logo1.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 2
        $merchant2 = new Merchant();
        $merchant2->name = 'KFC';
        $merchant2->max_sites = 5;
        $merchant2->spin_after_days = 5;
        $merchant2->scan_after_hours = 8;
        $merchant2->use_other_merchant_points = false;
        $merchant2->status = '1';
        $merchant2->created_at = now();
        $merchant2->updated_at = now();
        $merchant2->save();
        $merchant2->file()->create([
            'name' => 'logo2.png',
            'path' => 'merchants/logos/logo2.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 3
        $merchant3 = new Merchant();
        $merchant3->name = 'Nirala Sweets';
        $merchant3->max_sites = 2;
        $merchant3->spin_after_days = 3;
        $merchant3->scan_after_hours = 6;
        $merchant3->use_other_merchant_points = true;
        $merchant3->status = '1';
        $merchant3->created_at = now();
        $merchant3->updated_at = now();
        $merchant3->save();
        $merchant3->file()->create([
            'name' => 'logo3.png',
            'path' => 'merchants/logos/logo3.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 4
        $merchant4 = new Merchant();
        $merchant4->name = 'Dixy Chicken';
        $merchant4->max_sites = 1;
        $merchant4->spin_after_days = 10;
        $merchant4->scan_after_hours = 24;
        $merchant4->use_other_merchant_points = false;
        $merchant4->status = '1';
        $merchant4->created_at = now();
        $merchant4->updated_at = now();
        $merchant4->save();
        $merchant4->file()->create([
            'name' => 'logo4.png',
            'path' => 'merchants/logos/logo4.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 5
        $merchant5 = new Merchant();
        $merchant5->name = 'Burgies';
        $merchant5->max_sites = 4;
        $merchant5->spin_after_days = 14;
        $merchant5->scan_after_hours = 12;
        $merchant5->use_other_merchant_points = true;
        $merchant5->status = '1';
        $merchant5->created_at = now();
        $merchant5->updated_at = now();
        $merchant5->save();
        $merchant5->file()->create([
            'name' => 'logo5.png',
            'path' => 'merchants/logos/logo5.png',
            'type' => Constants::LOGOTYPE
        ]);
    }
}

