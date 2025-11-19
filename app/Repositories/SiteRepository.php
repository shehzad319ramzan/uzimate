<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\SiteDto;
use App\Models\Site;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Support\Facades\Auth;

class SiteRepository extends BaseRepository
{
    use HasMerchantScope;

    protected $_imgPath = 'sites/logos';
    protected $_logoType = Constants::LOGOTYPE;

    /**
     * Create a new service instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Site $model)
    {
        $this->setModel($model);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->_model->newQuery();

        if ($this->shouldLimitByMerchant()) {
            $siteIds = $this->accessibleSiteIds();
            $merchantIds = $this->accessibleMerchantIds();

            if (empty($siteIds) && empty($merchantIds)) {
                return $query->whereRaw('1 = 0')->paginate(20);
            }

            if (! empty($siteIds)) {
                $query->whereIn('id', $siteIds);
            } elseif (! empty($merchantIds)) {
                $query->whereIn('merchant_id', $merchantIds);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteDto $data)
    {
        $dataArray = $data->toArray();
        $dataArray['user_id'] = $dataArray['user_id'] ?? Auth::id();
        
        if (isset($dataArray['image'])) {
            $fileData = $dataArray['image'];
            unset($dataArray['image']);
        }

        $dataResult = $this->add($this->_model, $dataArray);

        if (isset($fileData)) {
            $logoUpload = $this->uploadFile($fileData, $this->_imgPath);
            $logoUpload['type'] = $this->_logoType;
            $dataResult->file()->create($logoUpload);
        }

        return $dataResult;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, SiteDto $data)
    {
        $result = $this->checkRecord($id);

        $dataArray = $data->toArray();
        $dataArray['user_id'] = $dataArray['user_id'] ?? $result->user_id;

        if (isset($dataArray['image'])) {
            $fileData = $dataArray['image'];
            unset($dataArray['image']);

            $this->removeExistingLogo($result);

            $logoUpload = $this->uploadFile($fileData, $this->_imgPath);
            $logoUpload['type'] = $this->_logoType;
            $result->file()->create($logoUpload);
        } elseif (($dataArray['use_merchant_logo'] ?? $result->use_merchant_logo) == true) {
            // if switching to use merchant logo, remove existing site logo if any
            $this->removeExistingLogo($result);
        }

        $result->update($dataArray);

        return $result;
    }

    protected function removeExistingLogo(Site $model): void
    {
        $existingLogo = $model->file()->where('type', $this->_logoType)->first();

        if ($existingLogo) {
            $this->deleteFile($existingLogo->path);
            $existingLogo->delete();
        }
    }
}
