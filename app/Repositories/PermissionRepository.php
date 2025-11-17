<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository
{
    /**
     * Create a new service instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Permission $model)
    {
        $this->setModel($model);
    }

    public function index($perPage = 20)
    {
        return $this->_model->orderBy('category')->orderBy('title')->paginate($perPage);
    }

    public function store(array $data)
    {
        return $this->add($this->_model, $data);
    }

    public function update($id, array $data)
    {
        $permission = $this->checkRecord($id);

        if ($permission == null) {
            return null;
        }

        $permission->update($data);

        return $permission;
    }
}
