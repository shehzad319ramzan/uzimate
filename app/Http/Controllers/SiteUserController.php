<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Dto\SiteUserDto;
use App\Http\Requests\SiteUserRequest;
use App\Models\Merchant;
use App\Models\Site;
use App\Models\SiteUser;
use App\Repositories\SiteUserRepository;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteUserController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(SiteUserRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/sites-user', 'siteusers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->_directory . '.create', $this->formOptions());
    }

    public function createSuper()
    {
        return view($this->_directory . '.create', $this->formOptions([
            'isSuperMode' => true,
            'superRoleId' => $this->getSuperRoleId(),
        ]));
    }

    public function store(SiteUserRequest $request)
    {
        try {
            $this->_repo->store(SiteUserDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function storeSuper(SiteUserRequest $request)
    {
        try {
            $request->merge([
                'merchant_id' => null,
                'site_ids' => [],
                'role_id' => $this->getSuperRoleId(),
            ]);
            $this->_repo->store(SiteUserDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Super admin added.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        $assignedSiteIds = SiteUser::where('user_id', $data->user_id)->pluck('site_id')->toArray();

        return view($this->_directory . '.edit', array_merge([
            'data' => $data,
        ], $this->formOptions([
            'isSuperMode' => $data->user?->hasRole(Constants::SUPERADMIN),
            'superRoleId' => $this->getSuperRoleId(),
            'assignedSiteIds' => $assignedSiteIds,
        ])));
    }

    public function update(SiteUserRequest $request, $id)
    {
        try {
            $siteUser = $this->_repo->show($id);
            if ($siteUser && $siteUser->user?->hasRole(Constants::SUPERADMIN)) {
                $request->merge([
                    'merchant_id' => null,
                'site_ids' => [],
                    'role_id' => $this->getSuperRoleId(),
                ]);
            }
            $this->_repo->update($id, SiteUserDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Updated succesfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage(); // Get the exception message
                return redirect()->route($this->_route . '.index')->with('error', $message);
            } else {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
        }
    }

    protected function formOptions(array $overrides = []): array
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole(Constants::SUPERADMIN);

        if ($isSuperAdmin) {
            // Super admin can see all merchants and sites
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
            $sites = Site::select('id', 'name', 'merchant_id')->orderBy('name')->get();
            $selectedMerchantId = null;
        } else {
            // For non-superadmin users, only show merchants/sites they have access to
            $merchants = $this->getAccessibleMerchants($user);
            $merchantIds = $merchants->pluck('id')->all();
            $sites = $this->getAccessibleSites($user, $merchantIds);
            $selectedMerchantId = $merchantIds[0] ?? null;
        }

        $assignedSiteIds = $overrides['assignedSiteIds'] ?? [];

        $base = [
            'merchants' => $merchants,
            'sites' => $sites,
            'selectedMerchantId' => $selectedMerchantId,
            'roles' => Role::where('name', '!=', Constants::SUPERADMIN)->select('id', 'name', 'title')->orderBy('title')->get(),
            'isSuperMode' => false,
            'superRoleId' => $this->getSuperRoleId(),
            'isSuperAdmin' => $isSuperAdmin,
        ];

        $result = array_merge($base, $overrides);
        $result['assignedSiteIds'] = old('site_ids', $assignedSiteIds);

        return $result;
    }

    protected function getSuperRoleId(): ?string
    {
        return Role::where('name', Constants::SUPERADMIN)->value('id');
    }

    protected function getAccessibleMerchants($user)
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

    protected function getAccessibleSites($user, array $merchantIds = [])
    {
        // First, check if user has specific site assignments via SiteUser
        $siteUserSiteIds = SiteUser::where('user_id', $user->id)
            ->whereNotNull('site_id')
            ->pluck('site_id')
            ->all();

        // If user has specific site assignments, ONLY return those sites
        // (Site users should only see their assigned sites, not all merchant sites)
        if (! empty($siteUserSiteIds)) {
            return Site::whereIn('id', $siteUserSiteIds)
                ->select('id', 'name', 'merchant_id')
                ->orderBy('name')
                ->get();
        }

        // Otherwise, return sites from accessible merchants
        $siteIds = [];

        if (! empty($merchantIds)) {
            $siteIds = Site::whereIn('merchant_id', $merchantIds)->pluck('id')->all();
        }

        if (empty($siteIds)) {
            return collect();
        }

        return Site::whereIn('id', $siteIds)->select('id', 'name', 'merchant_id')->orderBy('name')->get();
    }
}
