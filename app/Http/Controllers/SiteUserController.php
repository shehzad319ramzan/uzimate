<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Dto\SiteUserDto;
use App\Http\Requests\SiteUserRequest;
use App\Models\Merchant;
use App\Models\Site;
use App\Repositories\SiteUserRepository;
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
                'site_id' => null,
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

        return view($this->_directory . '.edit', array_merge([
            'data' => $data,
        ], $this->formOptions([
            'isSuperMode' => $data->user?->hasRole(Constants::SUPERADMIN),
            'superRoleId' => $this->getSuperRoleId(),
        ])));
    }

    public function update(SiteUserRequest $request, $id)
    {
        try {
            $siteUser = $this->_repo->show($id);
            if ($siteUser && $siteUser->user?->hasRole(Constants::SUPERADMIN)) {
                $request->merge([
                    'merchant_id' => null,
                    'site_id' => null,
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
        return array_merge([
            'merchants' => Merchant::select('id', 'name')->orderBy('name')->get(),
            'sites' => Site::select('id', 'name', 'merchant_id')->orderBy('name')->get(),
            'roles' => Role::whereNotIn('name', [Constants::SUPERADMIN, Constants::CUSTOMER])->select('id', 'name', 'title')->orderBy('title')->get(),
            'isSuperMode' => false,
            'superRoleId' => $this->getSuperRoleId(),
        ], $overrides);
    }

    protected function getSuperRoleId(): ?string
    {
        return Role::where('name', Constants::SUPERADMIN)->value('id');
    }
}
