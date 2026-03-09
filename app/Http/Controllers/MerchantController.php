<?php

namespace App\Http\Controllers;

use App\Dto\MerchantDto;
use App\Helper\QrCodeHelper;
use App\Http\Requests\MerchantRequest;
use App\Models\Merchant;
use App\Models\MerchantCategory;
use App\Repositories\MerchantRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MerchantController extends BaseController
{
    public function __construct(MerchantRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/merchants', 'merchants');
    }

    public function index()
    {
        $filters = request()->only(['search', 'merchant_category_id']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        $categories = MerchantCategory::where('status', '1')->orderBy('name')->get();
        return view($this->_directory . '.all', ['data' => $data, 'filters' => $filters, 'categories' => $categories]);
    }

    public function create()
    {
        $categories = MerchantCategory::where('status', '1')->orderBy('name')->get();
        return view($this->_directory . '.create', ['categories' => $categories]);
    }

    public function edit($id)
    {
        $data = $this->_repo->show($id);
        if ($data == null) {
            abort(404);
        }
        $categories = MerchantCategory::where('status', '1')->orderBy('name')->get();
        return view($this->_directory . '.edit', ['data' => $data, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MerchantRequest $request)
    {
        try {
            $this->_repo->store(MerchantDto::fromRequest($request));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
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
    public function update(MerchantRequest $request, $id)
    {
        try {
            $this->_repo->update($id, MerchantDto::fromRequest($request));
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

    public function generateQr(Merchant $merchant)
    {
        if ($merchant->qr_code) {
            return redirect()->route('merchants.show', $merchant->id)->with('info', 'This merchant already has a QR code.');
        }
        $qrPath = QrCodeHelper::generateAndSave($merchant->id, 'merchants/qr/');
        $merchant->update(['qr_code' => $qrPath]);
        return redirect()->route('merchants.show', $merchant->id)->with('success', 'QR code generated. Customers can scan it to earn points.');
    }
}
