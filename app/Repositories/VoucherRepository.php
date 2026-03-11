<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\VoucherDto;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VoucherRepository extends BaseRepository
{
    protected $_imgPath = '/vouchers/';
    protected $_imageType = Constants::IMAGETYPE;
    protected array $with = ['merchant', 'offers'];

    public function __construct(Voucher $model)
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

        $user = Auth::user();
        if ($user && ! $user->hasRole(Constants::SUPERADMIN)) {
            $merchants = $this->getAccessibleMerchants($user);
            $merchantIds = $merchants->pluck('id')->all();
            if (! empty($merchantIds)) {
                $query->whereIn('merchant_id', $merchantIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('merchant', function (Builder $qb) use ($search) {
                        $qb->where('name', 'like', "%{$search}%");
                    });
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

    public function show($id)
    {
        return $this->_model->with($this->with)->find($id);
    }

    public function store(VoucherDto $data)
    {
        $dataArray = $data->toArray();
        $offerIds = $data->offer_ids ?? [];
        if (!empty($offerIds)) {
            $dataArray['points_required'] = (int) Offer::whereIn('id', $offerIds)->sum('points_required');
        }
        $file = $data->file;
        $result = $this->add($this->_model, $dataArray);
        if (!empty($offerIds)) {
            $result->offers()->sync($offerIds);
        }
        if ($file) {
            $uploaded = $this->uploadFile($file, $this->_imgPath);
            $uploaded['type'] = $this->_imageType;
            $result->files()->create($uploaded);
        }
        return $result;
    }

    public function update($id, VoucherDto $data)
    {
        $result = $this->checkRecord($id);
        if (!$result) {
            return false;
        }
        $updateData = $data->toArray();
        $offerIds = $data->offer_ids ?? [];
        if (!empty($offerIds)) {
            $updateData['points_required'] = (int) Offer::whereIn('id', $offerIds)->sum('points_required');
        }
        $result->update($updateData);
        $result->offers()->sync($offerIds);
        if ($data->file) {
            $result->files()->where('type', $this->_imageType)->delete();
            $uploaded = $this->uploadFile($data->file, $this->_imgPath);
            $uploaded['type'] = $this->_imageType;
            $result->files()->create($uploaded);
        }
        return true;
    }

    public function formOptions(?User $user = null, string|int|null $merchantId = null): array
    {
        $isSuperAdmin = $user && $user->hasRole(Constants::SUPERADMIN);

        if ($isSuperAdmin || ! $user) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
        } else {
            $merchants = $this->getAccessibleMerchants($user);
        }

        if ($merchantId === null) {
            $offers = collect();
        } else {
            $offersRaw = Offer::with('merchant:id,name')
                ->where('merchant_id', $merchantId)
                ->select('id', 'merchant_id', 'title', 'description', 'points_required')
                ->orderBy('title')
                ->get();
            $offers = $offersRaw->map(fn ($o) => (object) [
                'id' => $o->id,
                'name' => ($o->merchant?->name ?? 'Merchant') . ' — ' . $o->title . ' (' . (int) $o->points_required . ' pts)',
                'merchant_id' => $o->merchant_id,
                'points_required' => (int) $o->points_required,
            ]);
        }

        return [
            'merchants' => $merchants,
            'offers' => $offers,
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

    /**
     * Get offers for a given merchant (for dropdown / AJAX).
     * Only offers for the selected merchant are returned.
     */
    public function offersByMerchant(string $merchantId): array
    {
        return Offer::where('merchant_id', $merchantId)
            ->select('id', 'merchant_id', 'title', 'points_required')
            ->orderBy('title')
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'text' => $o->title . ' (' . (int) $o->points_required . ' pts)',
                'points' => (int) $o->points_required,
            ])
            ->values()
            ->all();
    }
}
