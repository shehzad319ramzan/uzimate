<?php

namespace App\Repositories;

use App\Constants\Constants;
use App\Dto\UserDto;
use App\Interface\UserInterface;
use App\Models\User;
use App\Models\SiteUser;
use App\Support\Concerns\HasMerchantScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Password;
use App\Notifications\UserCreatedResetNotification;

class UserRepository extends BaseRepository implements UserInterface
{
    use HasMerchantScope;

    /**
     * Create a new service instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(User $model)
    {
        $this->setModel($model);
    }

    public function get_all_users()
    {
        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole(Constants::SUPERADMIN);

        $query = $this->_model->with('roles')
            ->whereHas('roles', function ($q) {
                $q->whereNotIn('name', [Constants::SUPERADMIN]);
            });

        // If not superadmin, only show users that are linked to SiteUser records
        // that belong to the logged-in user's accessible merchants/sites
        if (!$isSuperAdmin && $user) {
            $accessibleMerchantIds = $this->accessibleMerchantIds();
            $accessibleSiteIds = $this->accessibleSiteIds();

            // Get user IDs from SiteUser records that belong to accessible merchants/sites
            $siteUserQuery = SiteUser::whereNotNull('user_id');

            if (!empty($accessibleMerchantIds) || !empty($accessibleSiteIds)) {
                $siteUserQuery->where(function ($q) use ($accessibleMerchantIds, $accessibleSiteIds) {
                    if (!empty($accessibleMerchantIds)) {
                        $q->whereIn('merchant_id', $accessibleMerchantIds);
                    }
                    if (!empty($accessibleSiteIds)) {
                        if (!empty($accessibleMerchantIds)) {
                            $q->orWhereIn('site_id', $accessibleSiteIds);
                        } else {
                            $q->whereIn('site_id', $accessibleSiteIds);
                        }
                    }
                });
            } else {
                // No accessible merchants or sites, return empty
                return $query->whereRaw('1 = 0')->get();
            }

            $siteUserUserIds = $siteUserQuery->pluck('user_id')->unique()->all();

            if (empty($siteUserUserIds)) {
                // If no accessible SiteUsers, return empty collection
                return $query->whereRaw('1 = 0')->get();
            }

            // Only show users that are linked to SiteUser records
            $query->whereIn('id', $siteUserUserIds);
        }

        return $query->get();
    }

    public function getStaff($user)
    {
        return $this->_model->whereHas('roles', function ($q) use ($user) {
            $q->where('name', $user);
        })->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserDto $data)
    {
        $dataArray = $data->toArray();
        $roleId = $dataArray['type'];
        $dataArray['email_verified_at'] = now();

        $roleName = Role::whereId($roleId)->pluck('name')->first();

        if (is_null($roleName)) {
            throw new NotFoundHttpException('The specified role does not exist.'); // Throw a custom exception
        }

        unset($dataArray['type']);

        $user = $this->add($this->_model, $dataArray);
        $user->assignRole($roleName);

        if (isset($dataArray['image'])) {
            $profileUpload = $this->uploadFile($dataArray['image'], $this->_imgPath);
            $profileUpload['type'] = Constants::PROFILETYPE;
            $user->file()->create($profileUpload);
        }

        $token = Password::broker()->createToken($user);

        $user->notify(new UserCreatedResetNotification($token));

        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UserDto $data)
    {
        $dataArray = $data->toArray();

        if (isset($dataArray['password'])) {
            $dataArray['password'] = Hash::make($dataArray['password']);
        }

        $dataResult = $this->get_by_id($this->_model, $id);

        // Handle role update
        $roleId = isset($dataArray['type']) && !empty($dataArray['type']) ? $dataArray['type'] : null;
        unset($dataArray['type']);

        if (isset($dataArray['image'])) {
            $existingImage = $dataResult->file()->first();

            if ($existingImage) {
                $this->deleteFile($existingImage->path);
                $existingImage->delete();
            }

            $profileUpload = $this->uploadFile($dataArray['image'], $this->_imgPath);
            $profileUpload['type'] = Constants::PROFILETYPE;
            $dataResult->file()->create($profileUpload);
        }

        $dataResult->update($dataArray);

        // Update role if provided
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role) {
                // Remove all existing roles and assign the new one
                $dataResult->syncRoles([$role->name]);
                
                // Clear permission cache to ensure changes are reflected
                Cache::forget('spatie.permission.cache');
            }
        }

        // Reload the user with roles to ensure fresh data
        $dataResult->load('roles');

        return $dataResult;
    }

    public function validateEmail($email)
    {
        $data = $this->get_by_column_single($this->_model, ['email' => $email]);

        if ($data == null) {
            return false;
        } else {
            return true;
        }
    }
}
