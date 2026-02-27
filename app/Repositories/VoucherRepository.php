<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\VoucherDto;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Builder;

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
        $result->update($data->toArray());
        $result->offers()->sync($data->offer_ids ?? []);
        if ($data->file) {
            $result->files()->where('type', $this->_imageType)->delete();
            $uploaded = $this->uploadFile($data->file, $this->_imgPath);
            $uploaded['type'] = $this->_imageType;
            $result->files()->create($uploaded);
        }
        return true;
    }

    public function formOptions(?User $user = null): array
    {
        $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
        $offersRaw = Offer::with('merchant:id,name')->select('id', 'merchant_id', 'title', 'description', 'points_required')->orderBy('title')->get();
        $offers = $offersRaw->map(fn ($o) => (object) [
            'id' => $o->id,
            'name' => ($o->merchant?->name ?? 'Merchant') . ' — ' . $o->title . ' (' . (int) $o->points_required . ' pts)',
            'merchant_id' => $o->merchant_id,
        ]);
        return compact('merchants', 'offers');
    }
}
