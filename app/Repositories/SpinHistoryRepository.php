<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\SpinHistoryDto;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SpinHistory;
use App\Models\User;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpinHistoryRepository extends BaseRepository
{
    use HasMerchantScope;

    protected array $with = ['site.merchant', 'user', 'offer'];

    /**
     * Create a new service instance.
     */
    public function __construct(SpinHistory $model)
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

        if (! empty($filters['spin_result_type'])) {
            $query->where('spin_result_type', $filters['spin_result_type']);
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
    public function store(SpinHistoryDto $data)
    {
        $payload = $this->preparePayload($data);

        // Calculate spin number
        if (!isset($payload['spin_number']) || $payload['spin_number'] <= 0) {
            $lastSpin = $this->_model
                ->where('user_id', $payload['user_id'])
                ->where('merchant_id', $payload['merchant_id'])
                ->orderBy('spin_number', 'desc')
                ->first();

            $payload['spin_number'] = $lastSpin ? $lastSpin->spin_number + 1 : 1;
        }

        // Set last spin date
        if (!isset($payload['last_spin_date'])) {
            $payload['last_spin_date'] = now()->toDateString();
        }

        return $this->add($this->_model, $payload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, SpinHistoryDto $data)
    {
        $result = $this->checkRecord($id);

        if ($result === null) {
            throw new NotFoundHttpException('Spin history not found');
        }

        $payload = $this->preparePayload($data);
        $result->update($payload);

        return $result->fresh($this->with);
    }

    protected function preparePayload(SpinHistoryDto $data): array
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

        // Get available offers for the selected sites
        $offers = collect();
        if ($currentSiteId || !$sites->isEmpty()) {
            $siteIdsForOffers = $currentSiteId ? [$currentSiteId] : $sites->pluck('id')->toArray();
            $offers = \App\Models\Offer::whereIn('site_id', $siteIdsForOffers)
                ->where('status', 'active')
                ->select('id', 'title', 'site_id')
                ->orderBy('title')
                ->get();
        }

        return [
            'merchants' => $merchants,
            'sites' => $sites,
            'customers' => $customers,
            'offers' => $offers,
            'isSuperAdmin' => $isSuperAdmin,
            'resultTypes' => [
                'points' => 'Points',
                'offer' => 'Offer',
                'nothing' => 'Nothing',
                'discount' => 'Discount',
            ],
        ];
    }
}
