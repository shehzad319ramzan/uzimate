<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\CustomerLog;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationMerchantScopeTestSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::role(Constants::CUSTOMER)->first();
        if (! $customer) {
            $this->command->warn('No user with CUSTOMER role found. Create a customer first.');
            return;
        }

        $merchant = Merchant::first();
        if (! $merchant) {
            $this->command->warn('No merchant found. Run MerchantSeeder first.');
            return;
        }

        $site = Site::where('merchant_id', $merchant->id)->first();

        $exists = CustomerLog::where('merchant_id', $merchant->id)
            ->where('user_id', $customer->id)
            ->where('action_type', 'qr_code_scanned')
            ->exists();

        if ($exists) {
            $this->command->info('Customer log for this merchant and customer already exists. Notification segment should show this customer.');
            return;
        }

        CustomerLog::create([
            'merchant_id' => $merchant->id,
            'site_id' => $site?->id,
            'user_id' => $customer->id,
            'action_type' => 'qr_code_scanned',
            'action_category' => 'scans',
            'description' => 'Test scan for notification merchant scope',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'NotificationMerchantScopeTestSeeder',
        ]);

        $this->command->info("Created customer_log: customer_id={$customer->id}, merchant_id={$merchant->id}. Log in as the merchant that owns this merchant to see 1 customer in notification segments.");
    }
}
