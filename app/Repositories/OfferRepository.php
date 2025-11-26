<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\OfferDto;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Site;
use App\Models\SiteUser;
use Illuminate\Support\Collection;

class OfferRepository extends BaseRepository
{
    protected $_imgPath = '/offers/';
    protected $_imageType = Constants::IMAGETYPE;

    /**
     * Create a new service instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Offer $model)
    {
        $this->setModel($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfferDto $data)
    {
        $dataArray = $data->toArray();
        $image = $dataArray['image'] ?? null;

        unset($dataArray['image']);

        $dataResult = $this->add($this->_model, $dataArray);

        if ($image != null) {
            $imageUploaded = $this->uploadFile($image, $this->_imgPath);
            $imageUploaded['type'] = $this->_imageType;
            $dataResult->files()->create($imageUploaded);
        }

        return $dataResult;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, OfferDto $data)
    {
        $result = $this->checkRecord($id);

        if ($result == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Offer not found');
        }

        $dataArray = $data->toArray();
        $image = $dataArray['image'] ?? null;

        unset($dataArray['image']);

        $result->update($dataArray);

        if ($image != null) {
            // Delete old image
            $oldFile = $result->files()->where('type', $this->_imageType)->first();
            if ($oldFile) {
                $this->deleteFile($oldFile->path);
                $oldFile->delete();
            }

            // Upload new image
            $imageUploaded = $this->uploadFile($image, $this->_imgPath);
            $imageUploaded['type'] = $this->_imageType;
            $result->files()->create($imageUploaded);
        }

        return $result;
    }

    /**
     * Prepare merchants/sites/options for create/edit forms.
     */
    public function formOptions($user, ?string $currentSiteId = null, ?string $currentMerchantId = null): array
    {
        $isSuperAdmin = $user->hasRole(Constants::SUPERADMIN);

        if ($isSuperAdmin) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
            $sites = Site::select('id', 'name', 'merchant_id')->orderBy('name')->get();
        } else {
            $merchants = $this->getAccessibleMerchants($user);
            $sites = $this->getAccessibleSites($user);
        }

        if ($currentSiteId) {
            $siteExists = $sites->first(function ($site) use ($currentSiteId) {
                return (string) $site->id === (string) $currentSiteId;
            });

            if (! $siteExists) {
                $site = Site::find($currentSiteId);
                if ($site) {
                    $sites->push((object) [
                        'id' => $site->id,
                        'name' => $site->name,
                        'merchant_id' => $site->merchant_id,
                    ]);
                }
            }
        }

        if ($currentMerchantId) {
            $merchantExists = $merchants->first(function ($merchant) use ($currentMerchantId) {
                return (string) $merchant->id === (string) $currentMerchantId;
            });

            if (! $merchantExists) {
                $merchant = Merchant::find($currentMerchantId);
                if ($merchant) {
                    $merchants->push((object) [
                        'id' => $merchant->id,
                        'name' => $merchant->name,
                    ]);
                }
            }
        }

        return [
            'merchants' => $merchants,
            'sites' => $sites,
            'isSuperAdmin' => $isSuperAdmin,
        ];
    }

    protected function getAccessibleMerchants($user): Collection
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

    protected function getAccessibleSites($user): Collection
    {
        $merchantIds = Merchant::where('user_id', $user->id)->pluck('id')->all();

        $siteUserSiteIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('site_id')
            ->pluck('site_id')
            ->all();

        $siteIds = Site::where('user_id', $user->id)->pluck('id')->all();

        if (! empty($merchantIds)) {
            $merchantSiteIds = Site::whereIn('merchant_id', $merchantIds)->pluck('id')->all();
            $siteIds = array_merge($siteIds, $merchantSiteIds);
        }

        $siteIds = array_unique(array_filter(array_merge($siteIds, $siteUserSiteIds)));

        if (empty($siteIds)) {
            return collect();
        }

        return Site::whereIn('id', $siteIds)->select('id', 'name', 'merchant_id')->orderBy('name')->get();
    }
}
