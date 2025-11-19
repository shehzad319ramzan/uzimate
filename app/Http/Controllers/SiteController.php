<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Dto\SiteDto;
use App\Repositories\SiteRepository;
use App\Http\Requests\SiteRequest;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(SiteRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/sites', 'sites');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole(Constants::SUPERADMIN);
        
        if ($isSuperAdmin) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
            $selectedMerchantId = old('merchant_id');
        } else {
            $merchants = $this->getAccessibleMerchants($user);
            $selectedMerchantId = old('merchant_id', $merchants->first()->id ?? null);
        }

        return view($this->_directory . '.create', compact('merchants', 'selectedMerchantId', 'isSuperAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        try {
            $this->_repo->store(SiteDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole(Constants::SUPERADMIN);
        
        if ($isSuperAdmin) {
            $merchants = Merchant::select('id', 'name')->orderBy('name')->get();
            $selectedMerchantId = old('merchant_id', $data->merchant_id ?? null);
        } else {
            $merchants = $this->getAccessibleMerchants($user);
            $selectedMerchantId = $merchants->isNotEmpty()
                ? $merchants->firstWhere('id', $data->merchant_id)?->id ?? $merchants->first()->id
                : null;
        }

        return view($this->_directory . '.edit', compact('data', 'merchants', 'selectedMerchantId', 'isSuperAdmin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $id)
    {
        try {
            $this->_repo->update($id, SiteDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Updated succesfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage(); // Get the exception message
                return redirect()->route($this->_route . '.index')->with('error', $message);
            } else {
                return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong..');
            }
        }
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
}
