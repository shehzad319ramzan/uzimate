<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VoucherSeeder extends Seeder
{
    /**
     * Copy default voucher.png to storage and return the path for DB.
     */
    private function getDefaultVoucherImagePath(): string
    {
        $sourcePath = public_path('dashboard/images/default/voucher.png');
        $storagePath = 'vouchers/voucher.png';

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put(
                $storagePath,
                File::get($sourcePath)
            );
        }

        return $storagePath;
    }

    public function run(): void
    {
        $merchants = Merchant::limit(5)->get();
        if ($merchants->isEmpty()) {
            return;
        }

        $defaultImagePath = $this->getDefaultVoucherImagePath();

        $vouchers = [
            [
                'title' => 'Pizza Party Anyone? Get',
                'description' => '<p>Bring everyone together with a delicious pizza treat made for sharing and celebration. This voucher unlocks freshly baked pizzas loaded with flavor and fun. Perfect for team get-togethers, casual hangouts, or spontaneous celebrations. Enjoy great taste, good company, and an easy excuse to party. Because every memorable moment is better with pizza.</p>',
                'terms_and_conditions' => '<ol><li>Available to members with a minimum of 200 points.</li><li>Redeemable once within 30 days.</li><li>E-voucher valid for 30 days from redemption.</li><li>Cannot be combined with other promotions.</li><li>Non-transferable, for member use only.</li><li>E-voucher expires if points are insufficient.</li></ol>',
                'points_required' => 200,
                'valid_until_offset' => 30,
            ],
            [
                'title' => 'Uncover Fashion Secrets of',
                'description' => '<p>Get exclusive access to fashion deals and style tips. Use your points to unlock special offers and enjoy a better shopping experience.</p>',
                'terms_and_conditions' => '<ol><li>Minimum 200 points required to redeem.</li><li>One redemption per user per month.</li><li>Valid at participating locations only.</li><li>Subject to availability.</li></ol>',
                'points_required' => 200,
                'valid_until_offset' => 45,
            ],
            [
                'title' => 'Coffee Lover’s Delight',
                'description' => '<p>Enjoy a premium coffee or beverage on us. Perfect for your morning boost or an afternoon break.</p>',
                'terms_and_conditions' => '<ol><li>Redeem with 150 points.</li><li>Valid for one standard size drink.</li><li>Not valid with other offers.</li><li>Valid until expiry date shown.</li></ol>',
                'points_required' => 150,
                'valid_until_offset' => 60,
            ],
            [
                'title' => 'Free Side or Dessert',
                'description' => '<p>Add a free side or dessert to your order. Make your meal complete with this reward.</p>',
                'terms_and_conditions' => '<ol><li>100 points required.</li><li>One per transaction.</li><li>Select locations only.</li></ol>',
                'points_required' => 100,
                'valid_until_offset' => 20,
            ],
            [
                'title' => '10% Off Your Next Order',
                'description' => '<p>Save 10% on your next purchase. Stack your points and enjoy more savings.</p>',
                'terms_and_conditions' => '<ol><li>Minimum 50 points to redeem.</li><li>Maximum discount may apply.</li><li>Excludes certain items.</li></ol>',
                'points_required' => 50,
                'valid_until_offset' => 90,
            ],
        ];

        $createdVouchers = [];

        foreach ($merchants as $index => $merchant) {
            $merchantOfferIds = Offer::where('merchant_id', $merchant->id)->pluck('id')->toArray();
            if (empty($merchantOfferIds)) {
                continue;
            }

            $voucherData = $vouchers[$index % count($vouchers)];
            $voucher = Voucher::create([
                'merchant_id' => $merchant->id,
                'title' => $voucherData['title'],
                'description' => $voucherData['description'],
                'terms_and_conditions' => $voucherData['terms_and_conditions'],
                'points_required' => $voucherData['points_required'],
                'valid_until' => now()->addDays($voucherData['valid_until_offset']),
                'status' => '1',
            ]);

            $voucher->file()->create([
                'name' => 'voucher.png',
                'path' => $defaultImagePath,
                'type' => Constants::IMAGETYPE,
            ]);

            shuffle($merchantOfferIds);
            $count = min(rand(1, 3), count($merchantOfferIds));
            $offerIds = array_slice($merchantOfferIds, 0, $count);
            $voucher->offers()->sync($offerIds);
            $voucher->update([
                'points_required' => (int) Offer::whereIn('id', $offerIds)->sum('points_required'),
            ]);

            $createdVouchers[] = $voucher;
        }

        if (count($createdVouchers) >= 1) {
            $createdVouchers[0]->update(['valid_until' => now()->subDays(5)]);
        }
        if (count($createdVouchers) >= 2) {
            $createdVouchers[1]->update(['status' => '0']);
        }
    }
}
