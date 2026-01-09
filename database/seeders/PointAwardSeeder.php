<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\PointAward;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PointAwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing merchants, sites, customers, and admins
        $merchants = Merchant::all();
        $sites = Site::all();
        $customers = User::role(Constants::CUSTOMER)->get();
        $admins = User::role([Constants::SUPERADMIN, Constants::Admin, Constants::Manager])->get();

        if ($merchants->isEmpty() || $sites->isEmpty()) {
            $this->command->warn('No merchants or sites found. Please run MerchantSeeder and SiteSeeder first.');
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please create customers first.');
            return;
        }

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Please create admin users first.');
            return;
        }

        $merchantIds = $merchants->pluck('id')->toArray();
        $siteIds = $sites->pluck('id')->toArray();
        $customerIds = $customers->pluck('id')->toArray();
        $adminIds = $admins->pluck('id')->toArray();

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

        // Helper function to safely get an admin
        $getRandomAdmin = function () use ($adminIds) {
            if (empty($adminIds)) {
                return null;
            }
            return $adminIds[array_rand($adminIds)];
        };

        // Notes templates for variety
        $notesTemplates = [
            'Points awarded for purchase',
            'Reward for loyalty',
            'Special promotion bonus',
            'Referral bonus points',
            'Birthday bonus',
            'Points for completing survey',
            'Bonus for social media share',
            'Points for check-in',
            'Holiday promotion bonus',
            'Customer appreciation points',
            null, // Some awards won't have notes
            null,
        ];

        // Create point awards for different scenarios
        $pointAmounts = [10, 20, 25, 50, 50, 75, 100, 100, 100, 150, 200, 250, 500];

        // Scenario 1: Create awards with merchant and site (most common)
        for ($i = 0; $i < 10; $i++) {
            $merchant = $merchants->random();
            $site = $getRandomSiteForMerchant($merchant->id);
            
            if (!$site) {
                continue; // Skip if no site found for merchant
            }

            $customer = $customers->random();
            $adminId = $getRandomAdmin();
            $points = $pointAmounts[array_rand($pointAmounts)];
            $notes = $notesTemplates[array_rand($notesTemplates)];

            $pointAward = new PointAward();
            $pointAward->merchant_id = $merchant->id;
            $pointAward->site_id = $site->id;
            $pointAward->user_id = $customer->id;
            $pointAward->awarded_by_id = $adminId;
            $pointAward->points_earned = $points;
            $pointAward->notes = $notes;
            $pointAward->created_at = Carbon::now()->subDays(rand(0, 30));
            $pointAward->updated_at = Carbon::now()->subDays(rand(0, 30));
            $pointAward->save();
        }

        // Scenario 2: Create awards with just site (merchant_id will be auto-derived)
        for ($i = 0; $i < 5; $i++) {
            $site = $sites->random();
            $customer = $customers->random();
            $adminId = $getRandomAdmin();
            $points = $pointAmounts[array_rand($pointAmounts)];
            $notes = $notesTemplates[array_rand($notesTemplates)];

            $pointAward = new PointAward();
            $pointAward->merchant_id = $site->merchant_id; // Use site's merchant
            $pointAward->site_id = $site->id;
            $pointAward->user_id = $customer->id;
            $pointAward->awarded_by_id = $adminId;
            $pointAward->points_earned = $points;
            $pointAward->notes = $notes;
            $pointAward->created_at = Carbon::now()->subDays(rand(0, 60));
            $pointAward->updated_at = Carbon::now()->subDays(rand(0, 60));
            $pointAward->save();
        }

        // Scenario 3: Recent awards (last 7 days)
        for ($i = 0; $i < 5; $i++) {
            $merchant = $merchants->random();
            $site = $getRandomSiteForMerchant($merchant->id);
            
            if (!$site) {
                continue;
            }

            $customer = $customers->random();
            $adminId = $getRandomAdmin();
            $points = $pointAmounts[array_rand($pointAmounts)];

            $pointAward = new PointAward();
            $pointAward->merchant_id = $merchant->id;
            $pointAward->site_id = $site->id;
            $pointAward->user_id = $customer->id;
            $pointAward->awarded_by_id = $adminId;
            $pointAward->points_earned = $points;
            $pointAward->notes = 'Recent award';
            $pointAward->created_at = Carbon::now()->subDays(rand(0, 7));
            $pointAward->updated_at = Carbon::now()->subDays(rand(0, 7));
            $pointAward->save();
        }

        // Scenario 4: High-value awards (500+ points)
        for ($i = 0; $i < 3; $i++) {
            $merchant = $merchants->random();
            $site = $getRandomSiteForMerchant($merchant->id);
            
            if (!$site) {
                continue;
            }

            $customer = $customers->random();
            $adminId = $getRandomAdmin();
            $highPoints = [500, 750, 1000];

            $pointAward = new PointAward();
            $pointAward->merchant_id = $merchant->id;
            $pointAward->site_id = $site->id;
            $pointAward->user_id = $customer->id;
            $pointAward->awarded_by_id = $adminId;
            $pointAward->points_earned = $highPoints[array_rand($highPoints)];
            $pointAward->notes = 'High-value bonus award';
            $pointAward->created_at = Carbon::now()->subDays(rand(0, 45));
            $pointAward->updated_at = Carbon::now()->subDays(rand(0, 45));
            $pointAward->save();
        }

        // Scenario 5: Awards without awarded_by (nullable field)
        for ($i = 0; $i < 3; $i++) {
            $merchant = $merchants->random();
            $site = $getRandomSiteForMerchant($merchant->id);
            
            if (!$site) {
                continue;
            }

            $customer = $customers->random();
            $points = $pointAmounts[array_rand($pointAmounts)];

            $pointAward = new PointAward();
            $pointAward->merchant_id = $merchant->id;
            $pointAward->site_id = $site->id;
            $pointAward->user_id = $customer->id;
            $pointAward->awarded_by_id = null; // System-generated or unknown
            $pointAward->points_earned = $points;
            $pointAward->notes = 'System awarded points';
            $pointAward->created_at = Carbon::now()->subDays(rand(10, 90));
            $pointAward->updated_at = Carbon::now()->subDays(rand(10, 90));
            $pointAward->save();
        }

        $this->command->info('Point Awards seeded successfully! Created ' . PointAward::count() . ' point awards.');
    }
}

