<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\SurveyDto;
use App\Models\Merchant;
use App\Models\Survey;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SurveyRepository extends BaseRepository
{
    protected $_imgPath = '/surveys/';
    protected $_imageType = Constants::IMAGETYPE;

    protected array $with = ['merchant', 'questions.options'];

    public function __construct(Survey $model)
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
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['merchant_id']) && $filters['merchant_id'] !== '') {
            $query->where('merchant_id', $filters['merchant_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->_model->with($this->with)->find($id);
    }

    public function store(SurveyDto $data)
    {
        $dataArray = $data->toArray();
        $file = $data->file;
        $dataResult = $this->add($this->_model, $dataArray);

        if ($file) {
            $uploaded = $this->uploadFile($file, $this->_imgPath);
            $uploaded['type'] = $this->_imageType;
            $dataResult->files()->create($uploaded);
        }

        return $dataResult;
    }

    public function update($id, SurveyDto $data)
    {
        $result = $this->checkRecord($id);
        if (!$result) {
            return false;
        }
        $dataArray = $data->toArray();
        $file = $data->file;
        $result->update($dataArray);

        if ($file) {
            $result->files()->where('type', $this->_imageType)->delete();
            $uploaded = $this->uploadFile($file, $this->_imgPath);
            $uploaded['type'] = $this->_imageType;
            $result->files()->create($uploaded);
        }

        return true;
    }

    public function formOptions(?User $user = null): array
    {
        $isSuperAdmin = $user && $user->hasRole(Constants::SUPERADMIN);

        if ($isSuperAdmin || ! $user) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
        } else {
            $merchants = $this->getAccessibleMerchants($user);
        }

        return [
            'merchants' => $merchants,
            'isSuperAdmin' => $isSuperAdmin ?? false,
        ];
    }

    protected function getAccessibleMerchants(User $user): Collection
    {
        $merchantIds = Merchant::where('user_id', $user->id)->pluck('id')->all();
        $siteUserMerchantIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('merchant_id')
            ->pluck('merchant_id')
            ->all();
        $ids = array_unique(array_filter(array_merge($merchantIds, $siteUserMerchantIds)));
        if (empty($ids)) {
            return collect();
        }
        return Merchant::whereIn('id', $ids)->select('id', 'name')->orderBy('name')->get();
    }
}
