<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\PointAwardDto;
use App\Models\Merchant;
use App\Models\PointAward;
use App\Models\Site;
use App\Models\User;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PointAwardRepository extends BaseRepository
{
    use HasMerchantScope;

    protected array $with = ['site.merchant', 'user', 'awardedBy'];

    /**
     * Create a new service instance.
     */
    public function __construct(PointAward $model)
    {
        $this->setModel($model);
    }

    public function listWithFilters(array $filters = [])
    {
        return $this->buildFilteredQuery($filters)
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

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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
    public function store(PointAwardDto $data)
    {
        $payload = $this->preparePayload($data);
        return $this->add($this->_model, $payload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, PointAwardDto $data)
    {
        $result = $this->checkRecord($id);

        if ($result === null) {
            throw new NotFoundHttpException('Point award not found');
        }

        $payload = $this->preparePayload($data);
        $result->update($payload);

        return $result->fresh($this->with);
    }

    protected function preparePayload(PointAwardDto $data): array
    {
        $payload = $data->toArray();
        $site = Site::select('id', 'merchant_id')->findOrFail($payload['site_id']);
        $payload['merchant_id'] = $site->merchant_id;

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
        ];
    }
}
