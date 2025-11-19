<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\MerchantDto;
use App\Models\Merchant;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Support\Facades\Auth;

class MerchantRepository extends BaseRepository
{
    use HasMerchantScope;

    protected $_imgPath = 'merchants/logos';

    /**
     * Create a new service instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Merchant $model)
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
            $merchantIds = $this->accessibleMerchantIds();

            if (empty($merchantIds)) {
                return $query->whereRaw('1 = 0')->paginate(20);
            }

            $query->whereIn('id', $merchantIds);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MerchantDto $data)
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
            $logoUpload['type'] = Constants::LOGOTYPE;
            $dataResult->file()->create($logoUpload);
        }

        return $dataResult;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, MerchantDto $data)
    {
        $result = $this->checkRecord($id);

        $dataArray = $data->toArray();
        $dataArray['user_id'] = $dataArray['user_id'] ?? $result->user_id;

        if (isset($dataArray['image'])) {
            $fileData = $dataArray['image'];
            unset($dataArray['image']);

            $existingLogo = $result->file()->where('type', Constants::LOGOTYPE)->first();

            if ($existingLogo) {
                $this->deleteFile($existingLogo->path);
                $existingLogo->delete();
            }

            $logoUpload = $this->uploadFile($fileData, $this->_imgPath);
            $logoUpload['type'] = Constants::LOGOTYPE;
            $result->file()->create($logoUpload);
        }

        $result->update($dataArray);

        return $result;
    }
}
