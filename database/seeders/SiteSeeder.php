<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing merchants
        $merchants = Merchant::all();

        if ($merchants->isEmpty()) {
            $this->command->warn('No merchants found. Please run MerchantSeeder first.');
            return;
        }

        // Get random merchant IDs
        $merchantIds = $merchants->pluck('id')->toArray();
        $merchantOwners = $merchants->pluck('user_id', 'id');

        // Site 1: ISB2
        $site1 = new Site();
        $site1->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site1->user_id = $merchantOwners[$site1->merchant_id] ?? null;
        $site1->name = 'ISB2';
        $site1->phone = '044';
        $site1->points = 10;
        $site1->postcode = '44000';
        $site1->country = 'United Kingdom';
        $site1->use_merchant_logo = false;
        $site1->status = '1';
        $site1->created_at = Carbon::createFromFormat('d/m/Y H:i', '02/10/2024 17:26');
        $site1->updated_at = Carbon::createFromFormat('d/m/Y H:i', '02/10/2024 17:26');
        $site1->save();

        // Site 2: Super Market Branch
        $site2 = new Site();
        $site2->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site2->user_id = $merchantOwners[$site2->merchant_id] ?? null;
        $site2->name = 'Super Market Branch';
        $site2->phone = '0778';
        $site2->points = 10;
        $site2->postcode = 'RM6 4EJ';
        $site2->country = 'United Kingdom';
        $site2->use_merchant_logo = false;
        $site2->status = '1';
        $site2->created_at = Carbon::createFromFormat('d/m/Y H:i', '05/06/2024 21:05');
        $site2->updated_at = Carbon::createFromFormat('d/m/Y H:i', '05/06/2024 21:05');
        $site2->save();

        // Site 3: Seven Kings Branch
        $site3 = new Site();
        $site3->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site3->user_id = $merchantOwners[$site3->merchant_id] ?? null;
        $site3->name = 'Seven Kings Branch';
        $site3->phone = '07789652562';
        $site3->points = 10;
        $site3->postcode = 'IG11 9AH';
        $site3->country = 'United Kingdom';
        $site3->use_merchant_logo = false;
        $site3->status = '1';
        $site3->created_at = Carbon::createFromFormat('d/m/Y H:i', '15/08/2024 14:30');
        $site3->updated_at = Carbon::createFromFormat('d/m/Y H:i', '15/08/2024 14:30');
        $site3->save();

        // Site 4: Ilford Lane Branch
        $site4 = new Site();
        $site4->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site4->user_id = $merchantOwners[$site4->merchant_id] ?? null;
        $site4->name = 'Ilford Lane Branch';
        $site4->phone = '07789652563';
        $site4->points = 10;
        $site4->postcode = 'TW9 2ND';
        $site4->country = 'United Kingdom';
        $site4->use_merchant_logo = false;
        $site4->status = '1';
        $site4->created_at = Carbon::createFromFormat('d/m/Y H:i', '20/08/2024 16:45');
        $site4->updated_at = Carbon::createFromFormat('d/m/Y H:i', '20/08/2024 16:45');
        $site4->save();

        // Site 5: Richmond
        $site5 = new Site();
        $site5->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site5->user_id = $merchantOwners[$site5->merchant_id] ?? null;
        $site5->name = 'Richmond';
        $site5->phone = '07789652564';
        $site5->points = 10;
        $site5->postcode = 'SW5 9RL';
        $site5->country = 'United Kingdom';
        $site5->use_merchant_logo = false;
        $site5->status = '1';
        $site5->created_at = Carbon::createFromFormat('d/m/Y H:i', '25/08/2024 11:20');
        $site5->updated_at = Carbon::createFromFormat('d/m/Y H:i', '25/08/2024 11:20');
        $site5->save();

        // Site 6: Earl's Court
        $site6 = new Site();
        $site6->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site6->user_id = $merchantOwners[$site6->merchant_id] ?? null;
        $site6->name = 'Earl\'s Court';
        $site6->phone = '07789652565';
        $site6->points = 10;
        $site6->postcode = '44623';
        $site6->country = 'United Kingdom';
        $site6->use_merchant_logo = false;
        $site6->status = '1';
        $site6->created_at = Carbon::createFromFormat('d/m/Y H:i', '30/08/2024 13:15');
        $site6->updated_at = Carbon::createFromFormat('d/m/Y H:i', '30/08/2024 13:15');
        $site6->save();

        // Site 7: Bahria Town Phase
        $site7 = new Site();
        $site7->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site7->user_id = $merchantOwners[$site7->merchant_id] ?? null;
        $site7->name = 'Bahria Town Phase';
        $site7->phone = '07789652566';
        $site7->points = 10;
        $site7->postcode = 'RM6';
        $site7->country = 'United Kingdom';
        $site7->use_merchant_logo = false;
        $site7->status = '1';
        $site7->created_at = Carbon::createFromFormat('d/m/Y H:i', '05/09/2024 10:00');
        $site7->updated_at = Carbon::createFromFormat('d/m/Y H:i', '05/09/2024 10:00');
        $site7->save();

        // Site 8: Romford Branch
        $site8 = new Site();
        $site8->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site8->user_id = $merchantOwners[$site8->merchant_id] ?? null;
        $site8->name = 'Romford Branch';
        $site8->phone = '07789652567';
        $site8->points = 10;
        $site8->postcode = 'E1 6QL';
        $site8->country = 'United Kingdom';
        $site8->use_merchant_logo = false;
        $site8->status = '1';
        $site8->created_at = Carbon::createFromFormat('d/m/Y H:i', '10/09/2024 15:30');
        $site8->updated_at = Carbon::createFromFormat('d/m/Y H:i', '10/09/2024 15:30');
        $site8->save();

        // Site 9: Newbury Branch
        $site9 = new Site();
        $site9->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site9->user_id = $merchantOwners[$site9->merchant_id] ?? null;
        $site9->name = 'Newbury Branch';
        $site9->phone = '07789652568';
        $site9->points = 10;
        $site9->postcode = 'IG11 9HQ';
        $site9->country = 'United Kingdom';
        $site9->use_merchant_logo = false;
        $site9->status = '1';
        $site9->created_at = Carbon::createFromFormat('d/m/Y H:i', '15/09/2024 09:45');
        $site9->updated_at = Carbon::createFromFormat('d/m/Y H:i', '15/09/2024 09:45');
        $site9->save();

        // Site 10: Becton KFC
        $site10 = new Site();
        $site10->merchant_id = $merchantIds[array_rand($merchantIds)];
        $site10->user_id = $merchantOwners[$site10->merchant_id] ?? null;
        $site10->name = 'Becton KFC';
        $site10->phone = '07789652569';
        $site10->points = 10;
        $site10->postcode = 'E16 4RT';
        $site10->country = 'United Kingdom';
        $site10->use_merchant_logo = false;
        $site10->status = '1';
        $site10->created_at = Carbon::createFromFormat('d/m/Y H:i', '20/09/2024 12:00');
        $site10->updated_at = Carbon::createFromFormat('d/m/Y H:i', '20/09/2024 12:00');
        $site10->save();
    }
}

