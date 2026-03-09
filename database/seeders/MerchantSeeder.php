<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Helper\QrCodeHelper;
use App\Models\Merchant;
use App\Models\MerchantCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $defaultUserId = 2;
        $categoryIds = MerchantCategory::pluck('id')->toArray();

        $assignCategory = function () use ($categoryIds) {
            if (empty($categoryIds) || random_int(0, 2) === 0) {
                return null;
            }
            return $categoryIds[array_rand($categoryIds)];
        };

        // Merchant 1
        $merchant1 = new Merchant();
        $merchant1->name = 'McDonald';
        $merchant1->merchant_category_id = $assignCategory();
        $merchant1->description = '<p>McDonald\'s is one of the world\'s largest and most recognizable fast-food restaurant chains. It is known for its quick service and consistent food quality across all locations. The restaurant offers a wide range of menu items including burgers, fries, chicken products, and beverages. McDonald\'s signature items such as the Big Mac and French Fries are popular worldwide. It also provides breakfast options like muffins, hash browns, and hotcakes. The brand focuses on affordability and convenience for customers of all ages. Many outlets offer dine-in, takeaway, drive-thru, and delivery services. McDonald\'s restaurants are designed to be clean, modern, and family-friendly. The company continuously updates its menu to match local tastes and preferences. McDonald\'s emphasizes food safety and quality standards globally.</p>';
        $merchant1->max_sites = 3;
        $merchant1->spin_after_days = 7;
        $merchant1->scan_after_hours = 12;
        $merchant1->use_other_merchant_points = true;
        $merchant1->status = '1';
        $merchant1->qr_scan_points = 10;
        $merchant1->user_id = $defaultUserId;
        $merchant1->created_at = now();
        $merchant1->updated_at = now();
        $merchant1->save();
        $merchant1->update(['qr_code' => QrCodeHelper::generateAndSave($merchant1->id, 'merchants/qr/')]);
        $merchant1->file()->create([
            'name' => 'logo1.png',
            'path' => 'merchants/logos/logo1.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 2
        $merchant2 = new Merchant();
        $merchant2->name = 'KFC';
        $merchant2->merchant_category_id = $assignCategory();
        $merchant2->description = '<p>KFC is a global fast-food restaurant chain specializing in fried chicken. Famous for its secret blend of 11 herbs and spices, KFC offers a variety of chicken products, sides, and beverages. The brand is known for its Colonel Sanders legacy and iconic bucket meals. KFC provides dine-in, takeaway, drive-thru, and delivery services. Restaurants are designed to be family-friendly with consistent quality and service worldwide.</p>';
        $merchant2->max_sites = 5;
        $merchant2->spin_after_days = 5;
        $merchant2->scan_after_hours = 8;
        $merchant2->use_other_merchant_points = false;
        $merchant2->status = '1';
        $merchant2->qr_scan_points = 15;
        $merchant2->user_id = $defaultUserId;
        $merchant2->created_at = now();
        $merchant2->updated_at = now();
        $merchant2->save();
        $merchant2->update(['qr_code' => QrCodeHelper::generateAndSave($merchant2->id, 'merchants/qr/')]);
        $merchant2->file()->create([
            'name' => 'logo2.png',
            'path' => 'merchants/logos/logo2.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 3
        $merchant3 = new Merchant();
        $merchant3->name = 'Nirala Sweets';
        $merchant3->merchant_category_id = $assignCategory();
        $merchant3->description = '<p>Nirala Sweets is a renowned sweet shop offering traditional and modern Indian sweets, snacks, and dairy products. Known for quality ingredients and authentic recipes, Nirala provides a wide range of mithai, chaat, and baked goods. The brand emphasizes freshness and customer satisfaction. Multiple locations offer dine-in and takeaway services.</p>';
        $merchant3->max_sites = 2;
        $merchant3->spin_after_days = 3;
        $merchant3->scan_after_hours = 6;
        $merchant3->use_other_merchant_points = true;
        $merchant3->status = '1';
        $merchant3->qr_scan_points = 20;
        $merchant3->user_id = $defaultUserId;
        $merchant3->created_at = now();
        $merchant3->updated_at = now();
        $merchant3->save();
        $merchant3->update(['qr_code' => QrCodeHelper::generateAndSave($merchant3->id, 'merchants/qr/')]);
        $merchant3->file()->create([
            'name' => 'logo3.png',
            'path' => 'merchants/logos/logo3.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 4
        $merchant4 = new Merchant();
        $merchant4->name = 'Dixy Chicken';
        $merchant4->merchant_category_id = $assignCategory();
        $merchant4->description = '<p>Dixy Chicken is a popular fast-food chain specializing in halal fried chicken. Offering a variety of chicken meals, wraps, burgers, and sides, Dixy Chicken focuses on quality halal food at affordable prices. Locations provide dine-in, takeaway, and delivery options. The brand is known for its family-friendly atmosphere and consistent taste.</p>';
        $merchant4->max_sites = 1;
        $merchant4->spin_after_days = 10;
        $merchant4->scan_after_hours = 24;
        $merchant4->use_other_merchant_points = false;
        $merchant4->status = '1';
        $merchant4->qr_scan_points = 25;
        $merchant4->user_id = $defaultUserId;
        $merchant4->created_at = now();
        $merchant4->updated_at = now();
        $merchant4->save();
        $merchant4->update(['qr_code' => QrCodeHelper::generateAndSave($merchant4->id, 'merchants/qr/')]);
        $merchant4->file()->create([
            'name' => 'logo4.png',
            'path' => 'merchants/logos/logo4.png',
            'type' => Constants::LOGOTYPE
        ]);

        // Merchant 5
        $merchant5 = new Merchant();
        $merchant5->name = 'Burgies';
        $merchant5->merchant_category_id = $assignCategory();
        $merchant5->description = '<p>Burgies offers delicious burgers and American-style fast food. Known for fresh ingredients and generous portions, the menu includes classic burgers, cheeseburgers, and loaded options. The brand focuses on quality, value, and quick service. Dine-in, takeaway, and delivery available at all locations.</p>';
        $merchant5->max_sites = 4;
        $merchant5->spin_after_days = 14;
        $merchant5->scan_after_hours = 12;
        $merchant5->use_other_merchant_points = true;
        $merchant5->status = '1';
        $merchant5->qr_scan_points = 10;
        $merchant5->user_id = $defaultUserId;
        $merchant5->created_at = now();
        $merchant5->updated_at = now();
        $merchant5->save();
        $merchant5->update(['qr_code' => QrCodeHelper::generateAndSave($merchant5->id, 'merchants/qr/')]);
        $merchant5->file()->create([
            'name' => 'logo5.png',
            'path' => 'merchants/logos/logo5.png',
            'type' => Constants::LOGOTYPE
        ]);
    }
}

