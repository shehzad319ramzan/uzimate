<?php

namespace App\Repositories;

use App\Dto\InviteFriendDto;
use App\Models\InviteFriend;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class InviteFriendRepository extends BaseRepository
{
    protected array $with = ['referrer', 'referredUser'];

    public function __construct(InviteFriend $model)
    {
        $this->setModel($model);
    }

    public function index()
    {
        return $this->_model->with($this->with)->latest()->paginate(20);
    }

    public function listWithFilters(array $filters = [])
    {
        return $this->buildFilteredQuery($filters)->paginate(20)->withQueryString();
    }

    protected function buildFilteredQuery(array $filters = []): Builder
    {
        $query = $this->_model->newQuery()->with($this->with)->latest();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('referrer', function (Builder $qb) use ($search) {
                    $qb->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('referredUser', function (Builder $qb) use ($search) {
                    $qb->where('first_name', 'like', "%{$search}%")
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

    public function store(InviteFriendDto $data)
    {
        return $this->_model->create($data->toArray());
    }

    public function formOptions(): array
    {
        return [];
    }
}
