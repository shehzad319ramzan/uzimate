<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing merchants and sites
        $merchants = Merchant::all();
        $sites = Site::all();

        if ($merchants->isEmpty() || $sites->isEmpty()) {
            $this->command->warn('No merchants or sites found. Please run MerchantSeeder and SiteSeeder first.');
            return;
        }

        $merchantIds = $merchants->pluck('id')->toArray();
        $siteIds = $sites->pluck('id')->toArray();

        // Offer 1: Weekend Special
        $offer1 = new Offer();
        $offer1->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer1->site_id = $siteIds[array_rand($siteIds)];
        $offer1->title = 'Weekend Special Offer';
        $offer1->points_required = 100;
        $offer1->expires_on = Carbon::now()->addDays(30)->format('Y-m-d');
        $offer1->weekdays = ['Sat', 'Sun'];
        $offer1->description = 'Get 20% off on all items during weekends. Valid only on Saturdays and Sundays.';
        $offer1->status = '1';
        $offer1->created_at = Carbon::now()->subDays(10);
        $offer1->updated_at = Carbon::now()->subDays(10);
        $offer1->save();
        $offer1->files()->create([
            'name' => 'weekend-offer.jpg',
            'path' => 'offers/weekend-offer.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 2: Weekday Lunch Deal
        $offer2 = new Offer();
        $offer2->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer2->site_id = $siteIds[array_rand($siteIds)];
        $offer2->title = 'Weekday Lunch Deal';
        $offer2->points_required = 50;
        $offer2->expires_on = Carbon::now()->addDays(60)->format('Y-m-d');
        $offer2->weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        $offer2->description = 'Special lunch menu available Monday to Friday. Perfect for your workday lunch break!';
        $offer2->status = '1';
        $offer2->created_at = Carbon::now()->subDays(8);
        $offer2->updated_at = Carbon::now()->subDays(8);
        $offer2->save();
        $offer2->files()->create([
            'name' => 'lunch-deal.jpg',
            'path' => 'offers/lunch-deal.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 3: All Week Offer
        $offer3 = new Offer();
        $offer3->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer3->site_id = $siteIds[array_rand($siteIds)];
        $offer3->title = 'All Week Special';
        $offer3->points_required = 200;
        $offer3->expires_on = Carbon::now()->addDays(90)->format('Y-m-d');
        $offer3->weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $offer3->description = 'This amazing offer is valid all week long! Use your points to get exclusive discounts.';
        $offer3->status = '1';
        $offer3->created_at = Carbon::now()->subDays(5);
        $offer3->updated_at = Carbon::now()->subDays(5);
        $offer3->save();
        $offer3->files()->create([
            'name' => 'all-week-offer.jpg',
            'path' => 'offers/all-week-offer.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 4: Monday Madness
        $offer4 = new Offer();
        $offer4->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer4->site_id = $siteIds[array_rand($siteIds)];
        $offer4->title = 'Monday Madness';
        $offer4->points_required = 75;
        $offer4->expires_on = Carbon::now()->addDays(45)->format('Y-m-d');
        $offer4->weekdays = ['Mon'];
        $offer4->description = 'Start your week with a bang! Special Monday-only discounts.';
        $offer4->status = '1';
        $offer4->created_at = Carbon::now()->subDays(3);
        $offer4->updated_at = Carbon::now()->subDays(3);
        $offer4->save();
        $offer4->files()->create([
            'name' => 'monday-madness.jpg',
            'path' => 'offers/monday-madness.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 5: Friday Happy Hour
        $offer5 = new Offer();
        $offer5->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer5->site_id = $siteIds[array_rand($siteIds)];
        $offer5->title = 'Friday Happy Hour';
        $offer5->points_required = 150;
        $offer5->expires_on = Carbon::now()->addDays(20)->format('Y-m-d');
        $offer5->weekdays = ['Fri'];
        $offer5->description = 'Celebrate the end of the week with our Friday specials. Happy hour discounts available!';
        $offer5->status = '1';
        $offer5->created_at = Carbon::now()->subDays(2);
        $offer5->updated_at = Carbon::now()->subDays(2);
        $offer5->save();
        $offer5->files()->create([
            'name' => 'friday-happy-hour.jpg',
            'path' => 'offers/friday-happy-hour.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 6: Midweek Special
        $offer6 = new Offer();
        $offer6->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer6->site_id = $siteIds[array_rand($siteIds)];
        $offer6->title = 'Midweek Special';
        $offer6->points_required = 80;
        $offer6->expires_on = Carbon::now()->addDays(35)->format('Y-m-d');
        $offer6->weekdays = ['Tue', 'Wed', 'Thu'];
        $offer6->description = 'Beat the midweek blues with our special Tuesday, Wednesday, and Thursday offers.';
        $offer6->status = '1';
        $offer6->created_at = Carbon::now()->subDays(1);
        $offer6->updated_at = Carbon::now()->subDays(1);
        $offer6->save();
        $offer6->files()->create([
            'name' => 'midweek-special.jpg',
            'path' => 'offers/midweek-special.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 7: No Expiry Offer
        $offer7 = new Offer();
        $offer7->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer7->site_id = $siteIds[array_rand($siteIds)];
        $offer7->title = 'Permanent Offer';
        $offer7->points_required = 300;
        $offer7->expires_on = null;
        $offer7->weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $offer7->description = 'This is a permanent offer with no expiry date. Available all week long!';
        $offer7->status = '1';
        $offer7->created_at = Carbon::now();
        $offer7->updated_at = Carbon::now();
        $offer7->save();
        $offer7->files()->create([
            'name' => 'permanent-offer.jpg',
            'path' => 'offers/permanent-offer.jpg',
            'type' => Constants::IMAGETYPE,
        ]);

        // Offer 8: Inactive Offer
        $offer8 = new Offer();
        $offer8->merchant_id = $merchantIds[array_rand($merchantIds)];
        $offer8->site_id = $siteIds[array_rand($siteIds)];
        $offer8->title = 'Expired Offer';
        $offer8->points_required = 120;
        $offer8->expires_on = Carbon::now()->subDays(5)->format('Y-m-d');
        $offer8->weekdays = ['Sat', 'Sun'];
        $offer8->description = 'This offer has expired and is no longer active.';
        $offer8->status = '0';
        $offer8->created_at = Carbon::now()->subDays(30);
        $offer8->updated_at = Carbon::now()->subDays(5);
        $offer8->save();
    }
}

