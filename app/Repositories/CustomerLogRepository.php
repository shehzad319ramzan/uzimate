<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\CustomerLogDto;
use App\Models\CustomerLog;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\User;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerLogRepository extends BaseRepository
{
    use HasMerchantScope;

    protected array $with = ['site.merchant', 'user', 'performedBy'];

    /**
     * Create a new service instance.
     */
    public function __construct(CustomerLog $model)
    {
        $this->setModel($model);
    }

    public function listWithFilters(array $filters = [])
    {
        return $this->buildFilteredQuery($filters)
            ->paginate(20)
            ->withQueryString();
    }


    public function listScanLogs(array $filters = []): LengthAwarePaginator
    {
        $filters['action_type'] = 'qr_code_scanned';
        return $this->buildFilteredQuery($filters)
            ->with(array_merge($this->with, ['relatedModel', 'offer']))
            ->paginate(20)
            ->withQueryString();
    }

    protected function buildFilteredQuery(array $filters = []): Builder
    {
        $query = $this->_model->newQuery()->with($this->with)->latest();

        if ($this->shouldLimitByMerchant()) {
            $merchantIds = $this->accessibleMerchantIds();
            $siteIds = $this->accessibleSiteIds();

            if (empty($merchantIds) && empty($siteIds)) {
                $query->whereRaw('1 = 0');
            } else {
                if (! empty($merchantIds)) {
                    $query->whereIn('merchant_id', $merchantIds);
                }
                if (! empty($siteIds)) {
                    $query->whereIn('site_id', $siteIds);
                }
            }
        }

        if (! empty($filters['merchant_id'])) {
            $query->where('merchant_id', $filters['merchant_id']);
        }

        if (! empty($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }

        if (! empty($filters['action_type'])) {
            $query->where('action_type', $filters['action_type']);
        }

        if (! empty($filters['action_category'])) {
            $query->where('action_category', $filters['action_category']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function show($id)
    {
        return $this->_model->with($this->with)->find($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerLogDto $data)
    {
        $payload = $this->preparePayload($data);
        return $this->add($this->_model, $payload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, CustomerLogDto $data)
    {
        $result = $this->checkRecord($id);

        if ($result === null) {
            throw new NotFoundHttpException('Customer log not found');
        }

        $payload = $this->preparePayload($data);
        $result->update($payload);

        return $result->fresh($this->with);
    }

    protected function preparePayload(CustomerLogDto $data): array
    {
        $payload = $data->toArray();

        // If site_id is provided, derive merchant_id from site
        if (isset($payload['site_id']) && !isset($payload['merchant_id'])) {
            $site = Site::select('id', 'merchant_id')->find($payload['site_id']);
            if ($site && $site->merchant_id) {
                $payload['merchant_id'] = $site->merchant_id;
            }
        }

        return $payload;
    }

    public function formOptions($user, ?string $currentSiteId = null, ?string $currentMerchantId = null, ?int $currentUserId = null): array
    {
        $isSuperAdmin = $user?->hasRole(Constants::SUPERADMIN) ?? false;

        if ($isSuperAdmin) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
            $sites = Site::select('id', 'name', 'merchant_id')->orderBy('name')->get();
        } else {
            $merchantIds = $this->accessibleMerchantIds();
            $siteIds = $this->accessibleSiteIds();

            $merchants = ! empty($merchantIds)
                ? Merchant::whereIn('id', $merchantIds)->select('id', 'name')->orderBy('name')->get()
                : collect();

            $sites = ! empty($siteIds)
                ? Site::whereIn('id', $siteIds)->select('id', 'name', 'merchant_id')->orderBy('name')->get()
                : collect();
        }

        if ($currentSiteId && $sites->where('id', $currentSiteId)->isEmpty()) {
            $site = Site::select('id', 'name', 'merchant_id')->find($currentSiteId);
            if ($site) {
                $sites->push($site);
            }
        }

        if ($currentMerchantId && $merchants->where('id', $currentMerchantId)->isEmpty()) {
            $merchant = Merchant::select('id', 'name')->find($currentMerchantId);
            if ($merchant) {
                $merchants->push($merchant);
            }
        }

        $customers = User::role(Constants::CUSTOMER)
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($customer) {
                $customer->name = trim($customer->first_name . ' ' . $customer->last_name) ?: $customer->email;
                return $customer;
            });

        if ($currentUserId && $customers->where('id', $currentUserId)->isEmpty()) {
            $customer = User::select('id', 'first_name', 'last_name', 'email')->find($currentUserId);
            if ($customer) {
                $customer->name = trim($customer->first_name . ' ' . $customer->last_name) ?: $customer->email;
                $customers->push($customer);
            }
        }

        return [
            'merchants' => $merchants,
            'sites' => $sites,
            'customers' => $customers,
            'isSuperAdmin' => $isSuperAdmin,
            'actionTypes' => [
                'point_earned' => 'Point Earned',
                'point_redeemed' => 'Point Redeemed',
                'point_expired' => 'Point Expired',
                'point_adjusted' => 'Point Adjusted',
                'spin_completed' => 'Spin Completed',
                'offer_viewed' => 'Offer Viewed',
                'offer_redeemed' => 'Offer Redeemed',
                'qr_code_scanned' => 'QR Code Scanned',
                'check_in' => 'Check In',
                'profile_updated' => 'Profile Updated',
                'login' => 'Login',
                'logout' => 'Logout',
                'account_created' => 'Account Created',
                'custom' => 'Custom',
            ],
            'actionCategories' => [
                'points' => 'Points',
                'spins' => 'Spins',
                'offers' => 'Offers',
                'scans' => 'Scans',
                'profile' => 'Profile',
                'system' => 'System',
            ],
        ];
    }
}
