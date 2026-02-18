<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\RewardRuleDto;
use App\Models\Merchant;
use App\Models\RewardRule;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class RewardRuleRepository extends BaseRepository
{
    use HasMerchantScope;

    protected array $with = ['merchant'];

    public function __construct(RewardRule $model)
    {
        $this->setModel($model);
    }

    public function listWithFilters(array $filters = []): LengthAwarePaginator
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
            if (empty($merchantIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($merchantIds) {
                    $q->whereIn('merchant_id', $merchantIds)->orWhereNull('merchant_id');
                });
            }
        }

        if (!empty($filters['merchant_id'])) {
            if ($filters['merchant_id'] === 'global') {
                $query->whereNull('merchant_id');
            } else {
                $query->where('merchant_id', $filters['merchant_id']);
            }
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('action_type', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function show($id)
    {
        return $this->_model->with($this->with)->find($id);
    }

    public function store(RewardRuleDto $data): RewardRule
    {
        return $this->add($this->_model, $data->toArray());
    }

    public function update($id, RewardRuleDto $data): RewardRule
    {
        $rule = $this->checkRecord($id);
        if ($rule === null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Reward rule not found');
        }
        $rule->update($data->toArray());
        return $rule->fresh($this->with);
    }

    public function formOptions($user): array
    {
        $isSuperAdmin = $user?->hasRole(Constants::SUPERADMIN) ?? false;

        if ($isSuperAdmin) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
        } else {
            $merchantIds = $this->accessibleMerchantIds();
            $merchants = !empty($merchantIds)
                ? Merchant::whereIn('id', $merchantIds)->select('id', 'name')->orderBy('name')->get()
                : collect();
        }

        return [
            'merchants' => $merchants,
            'actionTypes' => RewardRule::defaultActionTypes(),
            'triggerConditions' => RewardRule::triggerConditions(),
        ];
    }
}
