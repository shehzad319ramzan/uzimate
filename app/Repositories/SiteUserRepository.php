<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\SiteUserDto;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SiteUserRepository extends BaseRepository
{
    protected $_imgPath = 'users/profile';

    protected array $with = ['merchant', 'site', 'user.roles'];

    public function __construct(SiteUser $model)
    {
        $this->setModel($model);
    }

    public function index()
    {
        return $this->get_all($this->_model, $this->with);
    }

    public function show($id)
    {
        return $this->get_by_id($this->_model, $id, $this->with);
    }

    public function all($relation = null)
    {
        return $this->getAll($this->_model, $this->with);
    }

    public function store(SiteUserDto $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createOrUpdateUser(new User(), $data);

            $siteUserData = $data->toArray();
            $siteUserData['user_id'] = $user->id;

            return $this->_model->create($siteUserData)->load($this->with);
        });
    }

    public function update($id, SiteUserDto $data)
    {
        $siteUser = $this->checkRecord($id);

        if ($siteUser === null) {
            return null;
        }

        return DB::transaction(function () use ($siteUser, $data) {
            $this->createOrUpdateUser($siteUser->user, $data);
            $siteUser->update($data->toArray());

            return $siteUser->load($this->with);
        });
    }

    public function destroy($id)
    {
        $siteUser = $this->checkRecord($id);

        if ($siteUser === null) {
            return false;
        }

        return DB::transaction(function () use ($siteUser) {
            $user = $siteUser->user;
            $siteUser->delete();

            if ($user) {
                $existingImage = $user->file()->where('type', Constants::PROFILETYPE)->first();
                if ($existingImage) {
                    $this->deleteFile($existingImage->path);
                    $existingImage->delete();
                }

                $user->delete();
            }

            return true;
        });
    }

    protected function createOrUpdateUser(User $user, SiteUserDto $data): User
    {
        $payload = $data->userPayload();

        if (isset($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            unset($payload['password']);
        }

        if (! $user->exists) {
            $payload['about'] = $payload['about'] ?? 'Site user account';
            $payload['email_verified_at'] = now();
        }

        $image = $payload['image'] ?? null;
        $roleId = $payload['role_id'] ?? null;
        unset($payload['image'], $payload['role_id']);

        $user->fill($payload);
        $user->save();

        if ($image) {
            $existingImage = $user->file()->where('type', Constants::PROFILETYPE)->first();
            if ($existingImage) {
                $this->deleteFile($existingImage->path);
                $existingImage->delete();
            }

            $profileUpload = $this->uploadFile($image, $this->_imgPath);
            $profileUpload['type'] = Constants::PROFILETYPE;
            $user->file()->create($profileUpload);
        }

        if ($roleId) {
            $role = Role::findOrFail($roleId);
            $user->syncRoles([$role->name]);
        }

        return $user;
    }
}

