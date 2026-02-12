<?php

namespace App\Repositories;

use App\Dto\MerchantCategoryDto;
use App\Models\MerchantCategory;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MerchantCategoryRepository extends BaseRepository
{
    public function __construct(MerchantCategory $model)
    {
        $this->setModel($model);
    }

    public function listWithFilters(array $filters = [])
    {
        return $this->buildFilteredQuery($filters)->withCount('merchants')->paginate(20)->withQueryString();
    }

    protected function buildFilteredQuery(array $filters = []): Builder
    {
        $query = $this->_model->newQuery()->latest();
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        return $query;
    }

    public function store(MerchantCategoryDto $data)
    {
        return $this->add($this->_model, $data->toArray());
    }

    public function update($id, MerchantCategoryDto $data)
    {
        $result = $this->checkRecord($id);
        if ($result === null) {
            throw new NotFoundHttpException('Merchant category not found');
        }
        $result->update($data->toArray());
        return $result;
    }

    public function getAllActive()
    {
        return $this->_model->where('status', '1')->orderBy('name')->get();
    }
}
