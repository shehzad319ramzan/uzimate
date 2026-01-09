<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Site;
use App\Models\SpinHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SpinHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing merchants, sites, offers, and customers
        $merchants = Merchant::all();
        $sites = Site::all();
        $offers = Offer::where('status', 'active')->get();
        $customers = User::role(Constants::CUSTOMER)->get();

        if ($merchants->isEmpty() || $sites->isEmpty()) {
            $this->command->warn('No merchants or sites found. Please run MerchantSeeder and SiteSeeder first.');
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please create customers first.');
            return;
        }

        $merchantIds = $merchants->pluck('id')->toArray();
        $siteIds = $sites->pluck('id')->toArray();
        $customerIds = $customers->pluck('id')->toArray();

        // Helper function to get a random site for a merchant
        $getRandomSiteForMerchant = function ($merchantId) use ($sites) {
            $merchantSites = $sites->where('merchant_id', $merchantId);
            if ($merchantSites->isEmpty() && !$sites->isEmpty()) {
                return $sites->random();
            }
            if (!$merchantSites->isEmpty()) {
                return $merchantSites->random();
            }
            return null;
        };

        // Helper function to get a random offer for a site
        $getRandomOfferForSite = function ($siteId) use ($offers) {
            if ($offers->isEmpty()) {
                return null;
            }
            $siteOffers = $offers->where('site_id', $siteId);
            if (!$siteOffers->isEmpty()) {
                return $siteOffers->random();
            }
            return $offers->random();
        };

        // Helper function to get next spin number for customer+merchant
        $getNextSpinNumber = function ($customerId, $merchantId) {
            $lastSpin = SpinHistory::where('user_id', $customerId)
                ->where('merchant_id', $merchantId)
                ->orderBy('spin_number', 'desc')
                ->first();
            return $lastSpin ? $lastSpin->spin_number + 1 : 1;
        };

        // Spin History 1: Customer wins points
        $merchant1 = $merchants->random();
        $site1 = $getRandomSiteForMerchant($merchant1->id);
        if (!$site1) {
            $site1 = $sites->random();
        }
        $customer1 = $customers->random();
        $spinNumber1 = $getNextSpinNumber($customer1->id, $merchant1->id);

        $spin1 = new SpinHistory();
        $spin1->merchant_id = $merchant1->id;
        $spin1->site_id = $site1->id;
        $spin1->user_id = $customer1->id;
        $spin1->spin_result_type = 'points';
        $spin1->points_earned = 50;
        $spin1->spin_number = $spinNumber1;
        $spin1->is_eligible = true;
        $spin1->last_spin_date = Carbon::now()->subDays(5)->toDateString();
        $spin1->notes = 'Customer won 50 points from spin wheel';
        $spin1->ip_address = '192.168.1.100';
        $spin1->device_info = ['platform' => 'iOS', 'app_version' => '1.2.3'];
        $spin1->created_at = Carbon::now()->subDays(5);
        $spin1->updated_at = Carbon::now()->subDays(5);
        $spin1->save();

        // Spin History 2: Customer wins an offer (only if offers exist)
        if (!$offers->isEmpty()) {
            $merchant2 = $merchants->random();
            $site2 = $getRandomSiteForMerchant($merchant2->id);
            if (!$site2) {
                $site2 = $sites->random();
            }
            $customer2 = $customers->random();
            $spinNumber2 = $getNextSpinNumber($customer2->id, $merchant2->id);
            $offer2 = $getRandomOfferForSite($site2->id);

            $spin2 = new SpinHistory();
            $spin2->merchant_id = $merchant2->id;
            $spin2->site_id = $site2->id;
            $spin2->user_id = $customer2->id;
            $spin2->spin_result_type = 'offer';
            $spin2->offer_id = $offer2?->id;
            $spin2->spin_number = $spinNumber2;
            $spin2->is_eligible = true;
            $spin2->last_spin_date = Carbon::now()->subDays(3)->toDateString();
            $spin2->notes = 'Customer won an offer from spin wheel';
            $spin2->ip_address = '192.168.1.101';
            $spin2->device_info = ['platform' => 'Android', 'app_version' => '2.1.0'];
            $spin2->created_at = Carbon::now()->subDays(3);
            $spin2->updated_at = Carbon::now()->subDays(3);
            $spin2->save();
        }

        // Spin History 3: Customer wins nothing
        $merchant3 = $merchants->random();
        $site3 = $getRandomSiteForMerchant($merchant3->id);
        if (!$site3) {
            $site3 = $sites->random();
        }
        $customer3 = $customers->random();
        $spinNumber3 = $getNextSpinNumber($customer3->id, $merchant3->id);

        $spin3 = new SpinHistory();
        $spin3->merchant_id = $merchant3->id;
        $spin3->site_id = $site3->id;
        $spin3->user_id = $customer3->id;
        $spin3->spin_result_type = 'nothing';
        $spin3->spin_number = $spinNumber3;
        $spin3->is_eligible = true;
        $spin3->last_spin_date = Carbon::now()->subDays(2)->toDateString();
        $spin3->notes = 'No reward this time';
        $spin3->ip_address = '192.168.1.102';
        $spin3->created_at = Carbon::now()->subDays(2);
        $spin3->updated_at = Carbon::now()->subDays(2);
        $spin3->save();

        // Spin History 4: Customer wins discount
        $merchant4 = $merchants->random();
        $site4 = $getRandomSiteForMerchant($merchant4->id);
        if (!$site4) {
            $site4 = $sites->random();
        }
        $customer4 = $customers->random();
        $spinNumber4 = $getNextSpinNumber($customer4->id, $merchant4->id);

        $spin4 = new SpinHistory();
        $spin4->merchant_id = $merchant4->id;
        $spin4->site_id = $site4->id;
        $spin4->user_id = $customer4->id;
        $spin4->spin_result_type = 'discount';
        $spin4->reward_value = 15.50;
        $spin4->spin_number = $spinNumber4;
        $spin4->is_eligible = true;
        $spin4->last_spin_date = Carbon::now()->subDays(1)->toDateString();
        $spin4->notes = 'Customer won 15.5% discount';
        $spin4->ip_address = '192.168.1.103';
        $spin4->device_info = ['platform' => 'Web', 'browser' => 'Chrome'];
        $spin4->created_at = Carbon::now()->subDays(1);
        $spin4->updated_at = Carbon::now()->subDays(1);
        $spin4->save();

        // Spin History 5: Customer wins big points (2nd spin for same customer+merchant)
        $merchant5 = $merchant1; // Same merchant as spin1
        $site5 = $site1; // Same site
        $customer5 = $customer1; // Same customer
        $spinNumber5 = $getNextSpinNumber($customer5->id, $merchant5->id);

        $spin5 = new SpinHistory();
        $spin5->merchant_id = $merchant5->id;
        $spin5->site_id = $site5->id;
        $spin5->user_id = $customer5->id;
        $spin5->spin_result_type = 'points';
        $spin5->points_earned = 100;
        $spin5->spin_number = $spinNumber5;
        $spin5->is_eligible = true;
        $spin5->last_spin_date = Carbon::now()->toDateString();
        $spin5->notes = 'Second spin - won 100 points';
        $spin5->ip_address = '192.168.1.104';
        $spin5->device_info = ['platform' => 'iOS', 'app_version' => '1.3.0'];
        $spin5->created_at = Carbon::now();
        $spin5->updated_at = Carbon::now();
        $spin5->save();

        // Spin History 6: Not eligible spin (admin override or exception)
        $merchant6 = $merchants->random();
        $site6 = $getRandomSiteForMerchant($merchant6->id);
        if (!$site6) {
            $site6 = $sites->random();
        }
        $customer6 = $customers->random();
        $spinNumber6 = $getNextSpinNumber($customer6->id, $merchant6->id);

        $spin6 = new SpinHistory();
        $spin6->merchant_id = $merchant6->id;
        $spin6->site_id = $site6->id;
        $spin6->user_id = $customer6->id;
        $spin6->spin_result_type = 'points';
        $spin6->points_earned = 25;
        $spin6->spin_number = $spinNumber6;
        $spin6->is_eligible = false;
        $spin6->last_spin_date = Carbon::now()->subDays(1)->toDateString();
        $spin6->notes = 'Manual override by admin - not eligible but allowed';
        $spin6->ip_address = '192.168.1.105';
        $spin6->created_at = Carbon::now()->subHours(12);
        $spin6->updated_at = Carbon::now()->subHours(12);
        $spin6->save();

        // Spin History 7: Another offer win (only if offers exist)
        if (!$offers->isEmpty()) {
            $merchant7 = $merchants->random();
            $site7 = $getRandomSiteForMerchant($merchant7->id);
            if (!$site7) {
                $site7 = $sites->random();
            }
            $customer7 = $customers->random();
            $spinNumber7 = $getNextSpinNumber($customer7->id, $merchant7->id);
            $offer7 = $getRandomOfferForSite($site7->id);

            $spin7 = new SpinHistory();
            $spin7->merchant_id = $merchant7->id;
            $spin7->site_id = $site7->id;
            $spin7->user_id = $customer7->id;
            $spin7->spin_result_type = 'offer';
            $spin7->offer_id = $offer7?->id;
            $spin7->spin_number = $spinNumber7;
            $spin7->is_eligible = true;
            $spin7->last_spin_date = Carbon::now()->subHours(6)->toDateString();
            $spin7->notes = 'Won special weekend offer';
            $spin7->ip_address = '192.168.1.106';
            $spin7->device_info = ['platform' => 'Android', 'app_version' => '2.2.1'];
            $spin7->created_at = Carbon::now()->subHours(6);
            $spin7->updated_at = Carbon::now()->subHours(6);
            $spin7->save();
        }

        // Spin History 8: Multiple spins for same customer (showing progression)
        $merchant8 = $merchants->random();
        $site8 = $getRandomSiteForMerchant($merchant8->id);
        if (!$site8) {
            $site8 = $sites->random();
        }
        $customer8 = $customers->random();

        // Create 3 spins for the same customer at the same merchant (showing progression)
        for ($i = 1; $i <= 3; $i++) {
            $spinNumber = $getNextSpinNumber($customer8->id, $merchant8->id);
            $daysAgo = 7 - ($i * 2); // 7 days, 5 days, 3 days ago

            $spin = new SpinHistory();
            $spin->merchant_id = $merchant8->id;
            $spin->site_id = $site8->id;
            $spin->user_id = $customer8->id;
            // Only use 'offer' if offers are available
            if ($i === 3 && !$offers->isEmpty()) {
                $spin->spin_result_type = 'offer';
                $offer = $getRandomOfferForSite($site8->id);
                $spin->offer_id = $offer?->id;
            } else {
                $spin->spin_result_type = $i === 1 ? 'nothing' : 'points';
            }
            $spin->points_earned = $i === 2 ? 75 : 0;
            $spin->spin_number = $spinNumber;
            $spin->is_eligible = true;
            $spin->last_spin_date = Carbon::now()->subDays($daysAgo)->toDateString();
            $spin->notes = "Spin #{$i} - " . ($i === 1 ? 'No reward' : ($i === 2 ? 'Won points' : ($i === 3 && !$offers->isEmpty() ? 'Won offer' : 'Won points')));
            $spin->ip_address = '192.168.1.' . (107 + $i);
            $spin->device_info = ['platform' => 'iOS', 'app_version' => '1.4.0'];
            $spin->created_at = Carbon::now()->subDays($daysAgo);
            $spin->updated_at = Carbon::now()->subDays($daysAgo);
            $spin->save();
        }

        $this->command->info('Spin History seeded successfully!');
    }
}

