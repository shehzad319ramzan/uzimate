<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\CustomerLog;
use App\Models\Merchant;
use App\Models\PointAward;
use App\Models\Site;
use App\Models\SpinHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CustomerLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $merchants = Merchant::all();
        $sites = Site::all();
        $customers = User::role(Constants::CUSTOMER)->get();
        $pointAwards = PointAward::all();
        $spinHistories = SpinHistory::all();

        if ($merchants->isEmpty() || $sites->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('No merchants, sites, or customers found. Please run other seeders first.');
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

        // Create logs from existing Point Awards
        foreach ($pointAwards->take(5) as $pointAward) {
            $log = new CustomerLog();
            $log->merchant_id = $pointAward->merchant_id;
            $log->site_id = $pointAward->site_id;
            $log->user_id = $pointAward->user_id;
            $log->action_type = 'point_earned';
            $log->action_category = 'points';
            $log->description = "Earned {$pointAward->points_earned} points from Point Award";
            $log->points_affected = $pointAward->points_earned;
            $log->related_model_type = PointAward::class;
            $log->related_model_id = $pointAward->id;
            $log->performed_by_id = $pointAward->awarded_by_id;
            $log->metadata = [
                'awarded_by' => $pointAward->awardedBy?->full_name,
                'notes' => $pointAward->notes,
            ];
            $log->ip_address = '192.168.1.' . rand(100, 150);
            $log->created_at = $pointAward->created_at;
            $log->updated_at = $pointAward->updated_at;
            $log->save();
        }

        // Create logs from existing Spin Histories
        foreach ($spinHistories->take(5) as $spinHistory) {
            $description = match($spinHistory->spin_result_type) {
                'points' => "Won {$spinHistory->points_earned} points from spin wheel (Spin #{$spinHistory->spin_number})",
                'offer' => "Won offer from spin wheel (Spin #{$spinHistory->spin_number})",
                'discount' => "Won " . ($spinHistory->reward_value ? number_format($spinHistory->reward_value, 2) . '%' : '') . " discount from spin wheel (Spin #{$spinHistory->spin_number})",
                'nothing' => "Spin completed - no reward (Spin #{$spinHistory->spin_number})",
                default => "Spin completed (Spin #{$spinHistory->spin_number})",
            };

            $log = new CustomerLog();
            $log->merchant_id = $spinHistory->merchant_id;
            $log->site_id = $spinHistory->site_id;
            $log->user_id = $spinHistory->user_id;
            $log->action_type = 'spin_completed';
            $log->action_category = 'spins';
            $log->description = $description;
            $log->points_affected = $spinHistory->points_earned ?? 0;
            $log->related_model_type = SpinHistory::class;
            $log->related_model_id = $spinHistory->id;
            $log->performed_by_id = auth()->id();
            $log->metadata = [
                'spin_result_type' => $spinHistory->spin_result_type,
                'spin_number' => $spinHistory->spin_number,
                'is_eligible' => $spinHistory->is_eligible,
                'offer_id' => $spinHistory->offer_id,
                'reward_value' => $spinHistory->reward_value,
            ];
            $log->ip_address = $spinHistory->ip_address ?? '192.168.1.' . rand(100, 150);
            $log->user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
            $log->location_data = $spinHistory->device_info;
            $log->created_at = $spinHistory->created_at;
            $log->updated_at = $spinHistory->updated_at;
            $log->save();
        }

        // Create some additional standalone customer log entries
        $customer1 = $customers->random();
        $merchant1 = $merchants->random();
        $site1 = $getRandomSiteForMerchant($merchant1->id);
        if (!$site1) {
            $site1 = $sites->random();
        }

        // Log 1: QR Code Scanned
        $log1 = new CustomerLog();
        $log1->merchant_id = $merchant1->id;
        $log1->site_id = $site1->id;
        $log1->user_id = $customer1->id;
        $log1->action_type = 'qr_code_scanned';
        $log1->action_category = 'scans';
        $log1->description = "QR code scanned at {$site1->name}";
        $log1->ip_address = '192.168.1.200';
        $log1->user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)';
        $log1->location_data = ['latitude' => 51.5074, 'longitude' => -0.1278, 'accuracy' => 10];
        $log1->created_at = Carbon::now()->subDays(2);
        $log1->updated_at = Carbon::now()->subDays(2);
        $log1->save();

        // Log 2: Profile Updated
        $log2 = new CustomerLog();
        $log2->merchant_id = $merchant1->id;
        $log2->user_id = $customer1->id;
        $log2->action_type = 'profile_updated';
        $log2->action_category = 'profile';
        $log2->description = "Customer profile information updated";
        $log2->ip_address = '192.168.1.201';
        $log2->user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
        $log2->created_at = Carbon::now()->subDays(3);
        $log2->updated_at = Carbon::now()->subDays(3);
        $log2->save();

        // Log 3: Login
        $customer2 = $customers->random();
        $log3 = new CustomerLog();
        $log3->user_id = $customer2->id;
        $log3->action_type = 'login';
        $log3->action_category = 'system';
        $log3->description = "Customer logged in";
        $log3->ip_address = '192.168.1.202';
        $log3->user_agent = 'Mozilla/5.0 (Android 10; Mobile)';
        $log3->created_at = Carbon::now()->subHours(5);
        $log3->updated_at = Carbon::now()->subHours(5);
        $log3->save();

        // Log 4: Account Created
        $customer3 = $customers->random();
        $log4 = new CustomerLog();
        $log4->user_id = $customer3->id;
        $log4->action_type = 'account_created';
        $log4->action_category = 'system';
        $log4->description = "New customer account created";
        $log4->ip_address = '192.168.1.203';
        $log4->created_at = Carbon::now()->subDays(10);
        $log4->updated_at = Carbon::now()->subDays(10);
        $log4->save();

        // Log 5: Check In
        $customer4 = $customers->random();
        $merchant4 = $merchants->random();
        $site4 = $getRandomSiteForMerchant($merchant4->id);
        if (!$site4) {
            $site4 = $sites->random();
        }

        $log5 = new CustomerLog();
        $log5->merchant_id = $merchant4->id;
        $log5->site_id = $site4->id;
        $log5->user_id = $customer4->id;
        $log5->action_type = 'check_in';
        $log5->action_category = 'scans';
        $log5->description = "Customer checked in at {$site4->name}";
        $log5->ip_address = '192.168.1.204';
        $log5->location_data = ['latitude' => 40.7128, 'longitude' => -74.0060];
        $log5->created_at = Carbon::now()->subDays(1);
        $log5->updated_at = Carbon::now()->subDays(1);
        $log5->save();

        $this->command->info('Customer Logs seeded successfully!');
    }
}

