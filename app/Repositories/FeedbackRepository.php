<?php

namespace App\Repositories;

use App\Dto\FeedbackDto;
use App\Models\Feedback;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeedbackRepository extends BaseRepository
{
    use HasMerchantScope;

    protected array $with = [];

    /**
     * Create a new service instance.
     */
    public function __construct(Feedback $model)
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

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
    public function store(FeedbackDto $data)
    {
        $payload = $data->toArray();
        return $this->add($this->_model, $payload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, FeedbackDto $data)
    {
        $result = $this->checkRecord($id);

        if ($result === null) {
            throw new NotFoundHttpException('Feedback not found');
        }

        $payload = $data->toArray();
        $result->update($payload);

        return $result->fresh($this->with);
    }
}
