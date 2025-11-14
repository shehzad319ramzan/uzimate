<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\MerchantDto;
use App\Models\Merchant;

class MerchantRepository extends BaseRepository
{
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
     * Store a newly created resource in storage.
     */
    public function store(MerchantDto $data)
    {
        $dataArray = $data->toArray();
        
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
