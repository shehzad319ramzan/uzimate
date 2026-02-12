<?php

namespace App\Http\Controllers;

use App\Dto\MerchantCategoryDto;
use App\Http\Requests\MerchantCategoryRequest;
use App\Repositories\MerchantCategoryRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MerchantCategoryController extends BaseController
{
    public function __construct(MerchantCategoryRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/merchant-categories', 'merchantcategories');
    }

    public function index()
    {
        $filters = request()->only(['search', 'status']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        return view($this->_directory . '.all', ['data' => $data, 'filters' => $filters]);
    }

    public function store(MerchantCategoryRequest $request)
    {
        try {
            $this->_repo->store(MerchantCategoryDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
        }
    }

    public function update(MerchantCategoryRequest $request, $id)
    {
        try {
            $this->_repo->update($id, MerchantCategoryDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Updated successfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
        }
    }
}
