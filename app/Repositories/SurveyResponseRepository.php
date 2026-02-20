<?php

namespace App\Repositories;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SurveyResponseRepository extends BaseRepository
{
    protected array $with = ['survey', 'user'];

    public function __construct(SurveyResponse $model)
    {
        $this->setModel($model);
    }

    public function index()
    {
        return $this->_model->with($this->with)
            ->latest('completed_at')
            ->paginate(20);
    }

    public function listWithFilters(array $filters = [])
    {
        return $this->buildFilteredQuery($filters)->paginate(20)->withQueryString();
    }

    protected function buildFilteredQuery(array $filters = []): Builder
    {
        $query = $this->_model->newQuery()->with($this->with)->latest('completed_at');

        if (! empty($filters['survey_id'])) {
            $query->where('survey_id', $filters['survey_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('completed_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('completed_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function formOptions(): array
    {
        $surveys = Survey::select('id', 'title')->orderBy('title')->get();
        return compact('surveys');
    }

    public function show($id)
    {
        return $this->_model->with([
            'survey',
            'user',
            'answers.question',
            'answers.option',
        ])->find($id);
    }
}
