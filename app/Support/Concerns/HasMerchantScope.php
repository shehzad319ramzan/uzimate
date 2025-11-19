<?php

namespace App\Support\Concerns;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SiteUser;
use Illuminate\Support\Facades\Auth;

trait HasMerchantScope
{
    protected function shouldLimitByMerchant(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return true;
        }

        return ! $user->hasRole(Constants::SUPERADMIN);
    }

    /**
     * @return array<int, string>
     */
    protected function accessibleMerchantIds(): array
    {
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        $merchantIds = Merchant::where('user_id', $user->id)->pluck('id')->all();

        $siteUserMerchantIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('merchant_id')
            ->pluck('merchant_id')
            ->all();

        $ids = array_unique(array_filter(array_merge($merchantIds, $siteUserMerchantIds)));

        return array_values($ids);
    }

    /**
     * @return array<int, string>
     */
    protected function accessibleSiteIds(): array
    {
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        // First, check if user has specific site assignments via SiteUser
        $siteUserSiteIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('site_id')
            ->pluck('site_id')
            ->all();

        // If user has specific site assignments, ONLY return those sites
        // (Site users should only see their assigned sites, not all merchant sites)
        if (! empty($siteUserSiteIds)) {
            return array_values(array_unique(array_filter($siteUserSiteIds)));
        }

        // Otherwise, return sites from owned merchants or sites they created
        $merchantIds = $this->accessibleMerchantIds();

        $siteIds = Site::where('user_id', $user->id)
            ->pluck('id')
            ->all();

        if (! empty($merchantIds)) {
            $merchantSiteIds = Site::whereIn('merchant_id', $merchantIds)
                ->pluck('id')
                ->all();
            $siteIds = array_merge($siteIds, $merchantSiteIds);
        }

        return array_values(array_unique(array_filter($siteIds)));
    }
}

