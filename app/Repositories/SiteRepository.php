<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\SiteDto;
use App\Models\Site;

class SiteRepository extends BaseRepository
{
    protected $_imgPath = 'sites/logos';

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
     * Store a newly created resource in storage.
     */
    public function store(SiteDto $data)
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
    public function update($id, SiteDto $data)
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
