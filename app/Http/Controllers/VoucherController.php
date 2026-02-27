<?php

namespace App\Http\Controllers;

use App\Dto\VoucherDto;
use App\Http\Requests\VoucherRequest;
use App\Repositories\VoucherRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VoucherController extends BaseController
{
    public function __construct(VoucherRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/vouchers', 'vouchers');
    }

    public function index()
    {
        $filters = request()->only(['search', 'merchant_id', 'status']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.all', array_merge(['data' => $data, 'filters' => $filters], $options));
    }

    public function create()
    {
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.create', $options);
    }

    public function store(VoucherRequest $request)
    {
        try {
            $payload = array_merge($request->validated(), ['file' => $request->file('file')]);
            $this->_repo->store(VoucherDto::fromRequest($payload));
            return redirect()->route($this->_route . '.index')->with('success', 'Voucher created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong.');
        }
    }

    public function show($id)
    {
        $data = $this->_repo->show($id);
        if ($data === null) {
            abort(404);
        }
        return view($this->_directory . '.show', compact('data'));
    }


    public function preview($id)
    {
        $data = $this->_repo->show($id);
        if ($data === null) {
            abort(404);
        }
        return view($this->_directory . '.partials.voucher-app-preview', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->_repo->show($id);
        if ($data === null) {
            abort(404);
        }
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.edit', array_merge(['data' => $data], $options));
    }

    public function update(VoucherRequest $request, $id)
    {
        try {
            $payload = array_merge($request->validated(), ['file' => $request->file('file')]);
            $this->_repo->update($id, VoucherDto::fromRequest($payload));
            return redirect()->route($this->_route . '.index')->with('success', 'Voucher updated successfully.');
        } catch (NotFoundHttpException $e) {
            return redirect()->route($this->_route . '.index')->with('error', $e->getMessage());
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->_repo->destroy($id);
            return redirect()->route($this->_route . '.index')->with('success', 'Voucher deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route($this->_route . '.index')->with('error', $th->getMessage() ?? 'Something went wrong.');
        }
    }
}
