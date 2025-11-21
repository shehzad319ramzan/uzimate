<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->roles->first()?->name ?? '';

        [$filters, $filterOptions] = $this->prepareFilters($request, $userRole, $user);

        switch ($userRole) {
            case Constants::SUPERADMIN:
                $data = $this->getSuperAdminStats($filters);
                break;
            case Constants::Manager:
                $data = $this->getMerchantStats($user, $filters);
                break;
            case Constants::Admin:
                $data = $this->getAdminStats($user, $filters);
                break;
            case Constants::CUSTOMER:
                $data = $this->getCustomerStats($user, $filters);
                break;
            default:
                $data = $this->getSuperAdminStats($filters);
        }

        Log::info('Dashboard view payload', $data);
        return view('auth.pages.dashboard', compact('data', 'userRole', 'filters', 'filterOptions'));
    }

    /**
     * Get statistics for Super Admin dashboard
     */
    private function getSuperAdminStats(array $filters): array
    {
        [$startDate, $endDate] = $this->getDateRange($filters);
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $merchantFilter = $filters['merchant_id'] ?? null;
        $module = $filters['module'] ?? 'merchants';

        // Merchants statistics - Total counts (no date filter for totals)
        $merchantQuery = Merchant::query();
        if ($merchantFilter) {
            $merchantQuery->where('id', $merchantFilter);
        }
        $totalMerchants = $merchantQuery->count();
        $merchantsThisMonth = $this->applyDateFilter(
            Merchant::when($merchantFilter, fn ($q) => $q->where('id', $merchantFilter)),
            'created_at',
            $startOfMonth,
            Carbon::now()
        )->count();
        $merchantsThisYear = $this->applyDateFilter(
            Merchant::when($merchantFilter, fn ($q) => $q->where('id', $merchantFilter)),
            'created_at',
            $startOfYear,
            Carbon::now()
        )->count();

        // Sites statistics - Total counts (no date filter for totals)
        $sitesQuery = Site::query();
        if ($merchantFilter) {
            $sitesQuery->where('merchant_id', $merchantFilter);
        }
        $totalSites = $sitesQuery->count();

        // Site Users statistics - Total counts (no date filter for totals)
        $siteUserQuery = SiteUser::query();
        if ($merchantFilter) {
            $siteUserQuery->where('merchant_id', $merchantFilter);
        }
        $totalSiteUsers = $siteUserQuery->count();
        $merchantRole = Role::where('name', Constants::Manager)->first();
        $adminRole = Role::where('name', Constants::Admin)->first();
        $superAdminRole = Role::where('name', Constants::SUPERADMIN)->first();

        $merchantUsers = 0;
        $adminUsers = 0;
        $superAdminUsers = 0;

        if ($merchantRole) {
            $merchantUsers = $this->applyRoleDateFilter($merchantRole->name, $startDate, $endDate);
        }
        if ($adminRole) {
            $adminUsers = $this->applyRoleDateFilter($adminRole->name, $startDate, $endDate);
        }
        if ($superAdminRole) {
            $superAdminUsers = $this->applyRoleDateFilter($superAdminRole->name, $startDate, $endDate);
        }

        // Offers statistics (if Offer model exists) - Total counts (no date filter for totals)
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $offerQuery = Offer::query();
            if ($merchantFilter && Schema::hasColumn((new Offer())->getTable(), 'merchant_id')) {
                $offerQuery->where('merchant_id', $merchantFilter);
            }
            $totalOffers = $offerQuery->count();

            $activeOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'active')->orWhere('status', 1);
            })->count();

            $expiredOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'expired')->orWhere('status', 0);
            })->count();
        }

        // Customers statistics - Total counts (no date filter for totals)
        $customerRole = Role::where('name', Constants::CUSTOMER)->first();
        $totalCustomers = 0;
        $activeCustomers = 0;
        $inactiveCustomers = 0;
        if ($customerRole) {
            $customerQuery = User::role($customerRole->name);
            $totalCustomers = $customerQuery->count();

            if (Schema::hasColumn('users', 'status')) {
                $activeCustomers = User::role($customerRole->name)->where(function ($query) {
                    $query->where('status', 1)->orWhere('status', 'active');
                })->count();
                $inactiveCustomers = $totalCustomers - $activeCustomers;
            } else {
                $activeCustomers = $totalCustomers;
                $inactiveCustomers = 0;
            }
        }

        // For charts, if no date range selected, show last 30 days
        $chartStartDate = $startDate ?? Carbon::now()->subDays(29);
        $chartEndDate = $endDate ?? Carbon::now();
        $chart = $this->buildSuperAdminChart($module, $merchantFilter, $chartStartDate, $chartEndDate);

        Log::info('Dashboard Super Admin Stats', [
            'filters' => [
                'start_date' => $startDate ? $startDate->toDateString() : 'all',
                'end_date' => $endDate ? $endDate->toDateString() : 'all',
                'merchant' => $merchantFilter,
                'module' => $module,
            ],
            'totals' => [
                'merchants' => $totalMerchants,
                'merchants_this_month' => $merchantsThisMonth,
                'merchants_this_year' => $merchantsThisYear,
                'sites' => $totalSites,
                'site_users' => $totalSiteUsers,
                'merchants_role_users' => $merchantUsers,
                'admin_role_users' => $adminUsers,
                'super_admin_role_users' => $superAdminUsers,
                'offers' => $totalOffers,
                'offers_active' => $activeOffers,
                'offers_expired' => $expiredOffers,
                'customers' => $totalCustomers,
                'customers_active' => $activeCustomers,
                'customers_inactive' => $inactiveCustomers,
            ],
        ]);

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
            'chart' => $chart,
        ];
    }

    /**
     * Get statistics for Merchant dashboard
     */
    private function getMerchantStats($user, array $filters): array
    {
        $siteUser = SiteUser::where('user_id', $user->id)->first();
        $merchantId = $siteUser?->merchant_id;

        if (!$merchantId) {
            return $this->getEmptyStats();
        }

        [$startDate, $endDate] = $this->getDateRange($filters);
        $siteFilter = $filters['site_id'] ?? 'all';

        // Merchant's sites
        $siteQuery = $this->applyDateFilter(
            Site::where('merchant_id', $merchantId),
            'created_at',
            $startDate,
            $endDate
        );
        if ($siteFilter !== 'all') {
            $siteQuery->where('id', $siteFilter);
        }
        $totalSites = $siteQuery->count();
        $sitesThisMonth = $totalSites;
        $sitesThisYear = $totalSites;

        // Merchant's site users
        $siteUserQuery = $this->applyDateFilter(
            SiteUser::where('merchant_id', $merchantId),
            'created_at',
            $startDate,
            $endDate
        );
        if ($siteFilter !== 'all') {
            $siteUserQuery->where('site_id', $siteFilter);
        }
        $totalSiteUsers = $siteUserQuery->count();
        $siteUsersThisMonth = $totalSiteUsers;

        // Merchant's offers
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $offerQuery = $this->applyDateFilter(Offer::query(), 'created_at', $startDate, $endDate);
            if (Schema::hasColumn((new Offer())->getTable(), 'merchant_id')) {
                $offerQuery->where('merchant_id', $merchantId);
            }
            if ($siteFilter !== 'all' && Schema::hasColumn((new Offer())->getTable(), 'site_id')) {
                $offerQuery->where('site_id', $siteFilter);
            }
            $totalOffers = $offerQuery->count();
            $activeOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'active')->orWhere('status', 1);
            })->count();
            $expiredOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'expired')->orWhere('status', 0);
            })->count();
        }

        // Merchant's customers (if relationship exists)
        $totalCustomers = 0;
        $activeCustomers = 0;
        $inactiveCustomers = 0;

        // For charts, if no date range selected, show last 30 days
        $chartStartDate = $startDate ?? Carbon::now()->subDays(29);
        $chartEndDate = $endDate ?? Carbon::now();
        $chart = $this->buildChartData(
            $this->applyDateFilter(
                Site::where('merchant_id', $merchantId)->when($siteFilter !== 'all', fn ($q) => $q->where('id', $siteFilter)),
                'created_at',
                $chartStartDate,
                $chartEndDate
            ),
            'created_at',
            'd M',
            'Sites created'
        );

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
            'chart' => $chart,
        ];
    }

    /**
     * Get statistics for Admin dashboard
     */
    private function getAdminStats($user, array $filters): array
    {
        // Get admin's accessible merchants and sites using the same logic as filtering
        $accessibleMerchantIds = $this->getAccessibleMerchantIds($user);
        $accessibleSiteIds = $this->getAccessibleSiteIds($user);

        if (empty($accessibleMerchantIds) && empty($accessibleSiteIds)) {
            return $this->getEmptyStats();
        }

        [$startDate, $endDate] = $this->getDateRange($filters);
        $activity = $filters['activity'] ?? 'offers';
        $startOfMonth = Carbon::now()->startOfMonth();

        // Sites statistics - from accessible merchants
        $siteQuery = $this->applyDateFilter(Site::query(), 'created_at', $startDate, $endDate);
        if (!empty($accessibleMerchantIds)) {
            $siteQuery->whereIn('merchant_id', $accessibleMerchantIds);
        }
        if (!empty($accessibleSiteIds)) {
            $siteQuery->orWhereIn('id', $accessibleSiteIds);
        }
        $totalSites = $siteQuery->count();
        $sitesThisMonth = $this->applyDateFilter(
            Site::when(!empty($accessibleMerchantIds), fn($q) => $q->whereIn('merchant_id', $accessibleMerchantIds))
                ->when(!empty($accessibleSiteIds), fn($q) => $q->orWhereIn('id', $accessibleSiteIds)),
            'created_at',
            $startOfMonth,
            Carbon::now()
        )->count();

        // Site Users statistics - from accessible merchants/sites
        $siteUserQuery = $this->applyDateFilter(SiteUser::query(), 'created_at', $startDate, $endDate);
        if (!empty($accessibleMerchantIds)) {
            $siteUserQuery->whereIn('merchant_id', $accessibleMerchantIds);
        }
        if (!empty($accessibleSiteIds)) {
            $siteUserQuery->orWhereIn('site_id', $accessibleSiteIds);
        }
        $totalSiteUsers = $siteUserQuery->count();
        $siteUsersThisMonth = $this->applyDateFilter(
            SiteUser::when(!empty($accessibleMerchantIds), fn($q) => $q->whereIn('merchant_id', $accessibleMerchantIds))
                ->when(!empty($accessibleSiteIds), fn($q) => $q->orWhereIn('site_id', $accessibleSiteIds)),
            'created_at',
            $startOfMonth,
            Carbon::now()
        )->count();

        // Offers statistics - from accessible merchants/sites
        $totalOffers = 0;
        $activeOffers = 0;
        $expiredOffers = 0;
        if (class_exists(Offer::class)) {
            $offerQuery = $this->applyDateFilter(Offer::query(), 'created_at', $startDate, $endDate);
            
            if (Schema::hasColumn((new Offer())->getTable(), 'merchant_id') && !empty($accessibleMerchantIds)) {
                $offerQuery->whereIn('merchant_id', $accessibleMerchantIds);
            }
            if (Schema::hasColumn((new Offer())->getTable(), 'site_id') && !empty($accessibleSiteIds)) {
                $offerQuery->orWhereIn('site_id', $accessibleSiteIds);
            }
            
            $totalOffers = $offerQuery->count();
            $activeOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'active')->orWhere('status', 1);
            })->count();
            $expiredOffers = (clone $offerQuery)->where(function ($q) {
                $q->where('status', 'expired')->orWhere('status', 0);
            })->count();
        }

        // Customer scans (if model exists) - from accessible sites
        $totalScans = 0;
        $scansThisMonth = 0;

        // Point awards (if model exists) - from accessible sites
        $totalPointsAwarded = 0;
        $pointsThisMonth = 0;

        // For charts, if no date range selected, show last 30 days
        $chartStartDate = $startDate ?? Carbon::now()->subDays(29);
        $chartEndDate = $endDate ?? Carbon::now();
        $chart = $this->buildAdminChart($activity, $accessibleSiteIds, $chartStartDate, $chartEndDate);

        return [
            'sites' => [
                'total' => $totalSites,
                'this_month' => $sitesThisMonth,
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
            'scans' => [
                'total' => $totalScans,
                'this_month' => $scansThisMonth,
            ],
            'points' => [
                'total' => $totalPointsAwarded,
                'this_month' => $pointsThisMonth,
            ],
            'chart' => $chart,
        ];
    }

    /**
     * Get statistics for Customer dashboard
     */
    private function getCustomerStats($user, array $filters): array
    {
        [$startDate, $endDate] = $this->getDateRange($filters);
        $activity = $filters['activity'] ?? 'points';

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

        // For charts, if no date range selected, show last 30 days
        $chartStartDate = $startDate ?? Carbon::now()->subDays(29);
        $chartEndDate = $endDate ?? Carbon::now();
        $chart = $this->buildCustomerChart($activity, $user->id, $chartStartDate, $chartEndDate);

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
            'chart' => $chart,
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
            'chart' => [
                'labels' => [],
                'values' => [],
                'title' => 'Activity',
            ],
        ];
    }

    private function prepareFilters(Request $request, string $role, $user): array
    {
        $dateRange = $request->input('date_range', 'all'); // Default to 'all'
        
        // Set dates based on selected range
        switch ($dateRange) {
            case 'this_month':
                $defaultStart = Carbon::now()->startOfMonth()->format('Y-m-d');
                $defaultEnd = Carbon::now()->format('Y-m-d');
                break;
            case 'this_year':
                $defaultStart = Carbon::now()->startOfYear()->format('Y-m-d');
                $defaultEnd = Carbon::now()->format('Y-m-d');
                break;
            case 'custom':
                $defaultStart = $request->input('start_date', Carbon::now()->subDays(29)->format('Y-m-d'));
                $defaultEnd = $request->input('end_date', Carbon::now()->format('Y-m-d'));
                break;
            case 'all':
            default:
                $defaultStart = null;
                $defaultEnd = null;
                break;
        }

        $filters = [
            'date_range' => $dateRange,
            'start_date' => $defaultStart,
            'end_date' => $defaultEnd,
        ];
        
        // Add date range options for all roles
        $filterOptions = [
            'date_ranges' => [
                'all' => 'All Time',
                'this_month' => 'This Month',
                'this_year' => 'This Year',
                'custom' => 'Custom Range',
            ]
        ];

        if ($role === Constants::SUPERADMIN) {
            $filters['merchant_id'] = $request->input('merchant_id');
            $filters['module'] = $request->input('module', 'merchants');
            $filterOptions['merchants'] = Merchant::select('id', 'name')->orderBy('name')->get();
            $filterOptions['modules'] = [
                'merchants' => 'Merchants',
                'sites' => 'Sites',
                'site_users' => 'Site Users',
                'offers' => 'Offers',
                'customers' => 'Customers',
            ];
        } elseif ($role === Constants::Manager) {
            $siteUser = SiteUser::where('user_id', $user->id)->first();
            $merchantId = $siteUser?->merchant_id;
            $filters['site_id'] = $request->input('site_id', 'all');
            $filterOptions['sites'] = Site::where('merchant_id', $merchantId)->select('id', 'name')->orderBy('name')->get();
        } elseif ($role === Constants::Admin) {
            $filters['activity'] = $request->input('activity', 'offers');
            $filters['site_id'] = $request->input('site_id', 'all');
            
            // Get admin's accessible sites for filtering
            $accessibleSiteIds = $this->getAccessibleSiteIds($user);
            $filterOptions['sites'] = Site::whereIn('id', $accessibleSiteIds)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            $filterOptions['activities'] = [
                'offers' => 'Offers',
                'sites' => 'Sites',
                'scans' => 'Customer Scans',
                'points' => 'Point Awards',
            ];
        } elseif ($role === Constants::CUSTOMER) {
            $filters['activity'] = $request->input('activity', 'points');
            $filterOptions['activities'] = [
                'points' => 'Points',
                'scans' => 'Scans',
            ];
        }

        return [$filters, $filterOptions];
    }

    private function getDateRange(array $filters): array
    {
        // If no date filtering (All Time), return null values
        if ($filters['date_range'] === 'all' || empty($filters['start_date']) || empty($filters['end_date'])) {
            return [null, null];
        }

        $start = Carbon::parse($filters['start_date']);
        $end = Carbon::parse($filters['end_date']);

        return [$start->startOfDay(), $end->endOfDay()];
    }

    private function buildChartData($query, string $dateColumn = 'created_at', string $labelFormat = 'd M', string $title = 'Activity'): array
    {
        $records = (clone $query)
            ->selectRaw("DATE($dateColumn) as chart_date, COUNT(*) as total")
            ->groupBy('chart_date')
            ->orderBy('chart_date')
            ->get();

        return [
            'labels' => $records->map(fn ($item) => Carbon::parse($item->chart_date)->format($labelFormat)),
            'values' => $records->pluck('total'),
            'title' => $title,
        ];
    }

    private function buildSuperAdminChart(string $module, ?string $merchantId, ?Carbon $startDate, ?Carbon $endDate): array
    {
        switch ($module) {
            case 'sites':
                $query = $this->applyDateFilter(Site::query(), 'created_at', $startDate, $endDate);
                if ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }
                return $this->buildChartData($query, 'created_at', 'd M', 'Sites created');
            case 'site_users':
                $query = $this->applyDateFilter(SiteUser::query(), 'created_at', $startDate, $endDate);
                if ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }
                return $this->buildChartData($query, 'created_at', 'd M', 'Site users added');
            case 'offers':
                if (class_exists(Offer::class)) {
                    $query = $this->applyDateFilter(Offer::query(), 'created_at', $startDate, $endDate);
                    if ($merchantId && Schema::hasColumn((new Offer())->getTable(), 'merchant_id')) {
                        $query->where('merchant_id', $merchantId);
                    }
                    return $this->buildChartData($query, 'created_at', 'd M', 'Offers created');
                }
                break;
            case 'customers':
                $customerRole = Role::where('name', Constants::CUSTOMER)->first();
                if ($customerRole) {
                    $query = $this->applyDateFilter(User::role($customerRole->name), 'created_at', $startDate, $endDate);
                    return $this->buildChartData($query, 'created_at', 'd M', 'Customers added');
                }
                break;
            case 'merchants':
            default:
                $query = $this->applyDateFilter(Merchant::query(), 'created_at', $startDate, $endDate);
                if ($merchantId) {
                    $query->where('id', $merchantId);
                }
                return $this->buildChartData($query, 'created_at', 'd M', 'Merchants onboarded');
        }

        return [
            'labels' => [],
            'values' => [],
            'title' => 'Activity',
        ];
    }

    private function buildAdminChart(string $activity, array $accessibleSiteIds, ?Carbon $startDate, ?Carbon $endDate): array
    {
        if ($activity === 'offers' && class_exists(Offer::class) && !empty($accessibleSiteIds)) {
            $query = $this->applyDateFilter(Offer::query(), 'created_at', $startDate, $endDate);
            if (Schema::hasColumn((new Offer())->getTable(), 'site_id')) {
                $query->whereIn('site_id', $accessibleSiteIds);
            }
            return $this->buildChartData($query, 'created_at', 'd M', 'Offers activity');
        }

        if ($activity === 'sites' && !empty($accessibleSiteIds)) {
            $query = $this->applyDateFilter(Site::whereIn('id', $accessibleSiteIds), 'created_at', $startDate, $endDate);
            return $this->buildChartData($query, 'created_at', 'd M', 'Sites activity');
        }

        return [
            'labels' => [],
            'values' => [],
            'title' => 'Activity',
        ];
    }

    private function buildCustomerChart(string $activity, int $userId, ?Carbon $startDate, ?Carbon $endDate): array
    {
        return [
            'labels' => [],
            'values' => [],
            'title' => ucfirst($activity) . ' activity',
        ];
    }

    private function applyDateFilter($query, string $column, ?Carbon $startDate, ?Carbon $endDate)
    {
        // If no date filtering (All Time), return query unchanged
        if ($startDate === null || $endDate === null) {
            return $query;
        }

        return $query
            ->whereDate($column, '>=', $startDate->toDateString())
            ->whereDate($column, '<=', $endDate->toDateString());
    }

    private function applyRoleDateFilter(string $roleName, ?Carbon $startDate, ?Carbon $endDate): int
    {
        return $this->applyDateFilter(User::role($roleName), 'created_at', $startDate, $endDate)->count();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Get accessible merchant IDs for the current user (same logic as HasMerchantScope trait)
     */
    private function getAccessibleMerchantIds($user): array
    {
        $merchantIds = Merchant::where('user_id', $user->id)->pluck('id')->all();

        $siteUserMerchantIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('merchant_id')
            ->pluck('merchant_id')
            ->all();

        $ids = array_unique(array_filter(array_merge($merchantIds, $siteUserMerchantIds)));

        return array_values($ids);
    }

    /**
     * Get accessible site IDs for the current user (same logic as HasMerchantScope trait)
     */
    private function getAccessibleSiteIds($user): array
    {
        // Check if user is ADMIN role - they get broader access
        $isAdmin = $user->hasRole(Constants::Admin);

        if ($isAdmin) {
            // ADMIN: Get all sites from owned merchants + sites they created + specific assignments
            $merchantIds = $this->getAccessibleMerchantIds($user);

            $siteIds = Site::where('user_id', $user->id)
                ->pluck('id')
                ->all();

            if (!empty($merchantIds)) {
                $merchantSiteIds = Site::whereIn('merchant_id', $merchantIds)
                    ->pluck('id')
                    ->all();
                $siteIds = array_merge($siteIds, $merchantSiteIds);
            }

            // Also include specific site assignments via SiteUser
            $siteUserSiteIds = SiteUser::where('user_id', $user->id)
                ->whereNotNull('site_id')
                ->pluck('site_id')
                ->all();

            if (!empty($siteUserSiteIds)) {
                $siteIds = array_merge($siteIds, $siteUserSiteIds);
            }

            return array_values(array_unique(array_filter($siteIds)));
        } else {
            // OTHER ROLES: Only specific site assignments
            $siteUserSiteIds = SiteUser::where('user_id', $user->id)
                ->whereNotNull('site_id')
                ->pluck('site_id')
                ->all();

            if (!empty($siteUserSiteIds)) {
                return array_values(array_unique(array_filter($siteUserSiteIds)));
            }

            // Fallback: sites they created directly
            $siteIds = Site::where('user_id', $user->id)
                ->pluck('id')
                ->all();

            return array_values(array_unique(array_filter($siteIds)));
        }
    }
}
