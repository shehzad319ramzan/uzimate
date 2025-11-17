<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->roles->first()?->name ?? '';

        $data = [];

        // Super Admin Dashboard - All statistics
        if ($userRole === Constants::SUPERADMIN) {
            $data = $this->getSuperAdminStats();
        }
        // Merchant Dashboard - Merchant-specific statistics
        elseif ($userRole === Constants::Merchant) {
            $data = $this->getMerchantStats($user);
        }
        // Admin Dashboard - Operational statistics
        elseif ($userRole === Constants::Admin) {
            $data = $this->getAdminStats($user);
        }
        // Customer Dashboard - Customer-specific data
        elseif ($userRole === Constants::CUSTOMER) {
            $data = $this->getCustomerStats($user);
        }
        // Default to Super Admin if role not recognized
        else {
            $data = $this->getSuperAdminStats();
        }

        return view('auth.pages.dashboard', compact('data', 'userRole'));
    }

    /**
     * Get statistics for Super Admin dashboard
     */
    private function getSuperAdminStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Merchants statistics
        $totalMerchants = Merchant::count();
        $merchantsThisMonth = Merchant::where('created_at', '>=', $startOfMonth)->count();
        $merchantsThisYear = Merchant::where('created_at', '>=', $startOfYear)->count();
        $totalSites = Site::count();

        // Site Users statistics
        $totalSiteUsers = SiteUser::count();
        $merchantRole = Role::where('name', Constants::Merchant)->first();
        $adminRole = Role::where('name', Constants::Admin)->first();
        $superAdminRole = Role::where('name', Constants::SUPERADMIN)->first();
        
        $merchantUsers = 0;
        $adminUsers = 0;
        $superAdminUsers = 0;

        if ($merchantRole) {
            $merchantUsers = User::role($merchantRole->name)->count();
        }
        if ($adminRole) {
            $adminUsers = User::role($adminRole->name)->count();
        }
        if ($superAdminRole) {
            $superAdminUsers = User::role($superAdminRole->name)->count();
        }

        // Offers statistics (if Offer model exists)
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $totalOffers = Offer::count();
            $activeOffers = Offer::where('status', 'active')->orWhere('status', 1)->count();
            $expiredOffers = Offer::where('status', 'expired')->orWhere('status', 0)->count();
        }

        // Customers statistics
        $customerRole = Role::where('name', Constants::CUSTOMER)->first();
        $totalCustomers = 0;
        $activeCustomers = 0;
        $inactiveCustomers = 0;
        if ($customerRole) {
            $customerQuery = User::role($customerRole->name);
            $totalCustomers = $customerQuery->count();

            if (Schema::hasColumn('users', 'status')) {
                $activeCustomers = User::role($customerRole->name)
                    ->where(function ($query) {
                        $query->where('status', 1)->orWhere('status', 'active');
                    })->count();
                $inactiveCustomers = $totalCustomers - $activeCustomers;
            } else {
                $activeCustomers = $totalCustomers;
                $inactiveCustomers = 0;
            }
        }

        return [
            'merchants' => [
                'total' => $totalMerchants,
                'this_month' => $merchantsThisMonth,
                'this_year' => $merchantsThisYear,
                'sites' => $totalSites,
            ],
            'site_users' => [
                'total' => $totalSiteUsers,
                'merchants' => $merchantUsers,
                'admins' => $adminUsers,
                'super_admins' => $superAdminUsers,
            ],
            'offers' => [
                'total' => $totalOffers,
                'active' => $activeOffers,
                'expired' => $expiredOffers,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'active' => $activeCustomers,
                'inactive' => $inactiveCustomers,
            ],
        ];
    }

    /**
     * Get statistics for Merchant dashboard
     */
    private function getMerchantStats($user): array
    {
        $siteUser = SiteUser::where('user_id', $user->id)->first();
        $merchantId = $siteUser?->merchant_id;

        if (!$merchantId) {
            return $this->getEmptyStats();
        }

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Merchant's sites
        $totalSites = Site::where('merchant_id', $merchantId)->count();
        $sitesThisMonth = Site::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $startOfMonth)->count();
        $sitesThisYear = Site::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $startOfYear)->count();

        // Merchant's site users
        $totalSiteUsers = SiteUser::where('merchant_id', $merchantId)->count();
        $siteUsersThisMonth = SiteUser::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $startOfMonth)->count();

        // Merchant's offers
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $totalOffers = Offer::where('merchant_id', $merchantId)->count();
            $activeOffers = Offer::where('merchant_id', $merchantId)
                ->where('status', 'active')->orWhere('status', 1)->count();
            $expiredOffers = Offer::where('merchant_id', $merchantId)
                ->where('status', 'expired')->orWhere('status', 0)->count();
        }

        // Merchant's customers (if relationship exists)
        $totalCustomers = 0;
        $activeCustomers = 0;
        $inactiveCustomers = 0;

        return [
            'sites' => [
                'total' => $totalSites,
                'this_month' => $sitesThisMonth,
                'this_year' => $sitesThisYear,
                'site_users' => $totalSiteUsers,
            ],
            'site_users' => [
                'total' => $totalSiteUsers,
                'this_month' => $siteUsersThisMonth,
            ],
            'offers' => [
                'total' => $totalOffers,
                'active' => $activeOffers,
                'expired' => $expiredOffers,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'active' => $activeCustomers,
                'inactive' => $inactiveCustomers,
            ],
        ];
    }

    /**
     * Get statistics for Admin dashboard
     */
    private function getAdminStats($user): array
    {
        $siteUser = SiteUser::where('user_id', $user->id)->first();
        $siteId = $siteUser?->site_id;
        $merchantId = $siteUser?->merchant_id;

        if (!$siteId) {
            return $this->getEmptyStats();
        }

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Site-specific offers
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $totalOffers = Offer::where('site_id', $siteId)->count();
            $activeOffers = Offer::where('site_id', $siteId)
                ->where('status', 'active')->orWhere('status', 1)->count();
            $expiredOffers = Offer::where('site_id', $siteId)
                ->where('status', 'expired')->orWhere('status', 0)->count();
        }

        // Customer scans (if model exists)
        $totalScans = 0;
        $scansThisMonth = 0;

        // Point awards (if model exists)
        $totalPointsAwarded = 0;
        $pointsThisMonth = 0;

        return [
            'offers' => [
                'total' => $totalOffers,
                'active' => $activeOffers,
                'expired' => $expiredOffers,
            ],
            'scans' => [
                'total' => $totalScans,
                'this_month' => $scansThisMonth,
            ],
            'points' => [
                'total' => $totalPointsAwarded,
                'this_month' => $pointsThisMonth,
            ],
        ];
    }

    /**
     * Get statistics for Customer dashboard
     */
    private function getCustomerStats($user): array
    {
        // Customer-specific data
        $totalPoints = 0;
        $availablePoints = 0;
        $redeemedPoints = 0;

        // Customer scans
        $totalScans = 0;
        $scansThisMonth = 0;

        // Customer offers/rewards
        $availableOffers = 0;
        $redeemedOffers = 0;

        return [
            'points' => [
                'total' => $totalPoints,
                'available' => $availablePoints,
                'redeemed' => $redeemedPoints,
            ],
            'scans' => [
                'total' => $totalScans,
                'this_month' => $scansThisMonth,
            ],
            'offers' => [
                'available' => $availableOffers,
                'redeemed' => $redeemedOffers,
            ],
        ];
    }

    /**
     * Get empty statistics structure
     */
    private function getEmptyStats(): array
    {
        return [
            'sites' => ['total' => 0, 'this_month' => 0, 'this_year' => 0, 'site_users' => 0],
            'site_users' => ['total' => 0, 'this_month' => 0],
            'offers' => ['total' => 0, 'active' => 0, 'expired' => 0],
            'customers' => ['total' => 0, 'active' => 0, 'inactive' => 0],
        ];
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
