<?php

namespace App\Http\Controllers;

use App\Dto\RewardRuleDto;
use App\Http\Requests\RewardRuleRequest;
use App\Repositories\RewardRuleRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RewardRuleController extends BaseController
{
    public function __construct(RewardRuleRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/reward-rules', 'reward-rules');
    }

    public function index()
    {
        $filters = request()->only(['search', 'merchant_id', 'is_active']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        $options = $this->_repo->formOptions(Auth::user());

        return view($this->_directory . '.all', array_merge([
            'data' => $data,
            'filters' => $filters,
        ], $options));
    }

    public function create()
    {
        $user = Auth::user();
        $options = $this->_repo->formOptions($user);

        if ($options['isSuperAdmin']) {
            $selectedMerchantId = old('merchant_id');
        } else {
            $selectedMerchantId = $options['merchants']->isNotEmpty()
                ? (old('merchant_id', $options['merchants']->first()->id))
                : old('merchant_id');
        }
        $options['selectedMerchantId'] = $selectedMerchantId;

        return view($this->_directory . '.create', $options);
    }

    public function store(RewardRuleRequest $request)
    {
        try {
            $this->_repo->store(RewardRuleDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Reward rule created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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

    public function edit($id)
    {
        $data = $this->_repo->show($id);
        if ($data === null) {
            abort(404);
        }
        $user = Auth::user();
        $options = $this->_repo->formOptions($user);

        if ($options['isSuperAdmin']) {
            $selectedMerchantId = old('merchant_id', $data->merchant_id);
        } else {
            $selectedMerchantId = $options['merchants']->isNotEmpty()
                ? ($options['merchants']->firstWhere('id', $data->merchant_id)?->id ?? $options['merchants']->first()->id)
                : $data->merchant_id;
        }
        $options['selectedMerchantId'] = $selectedMerchantId;

        return view($this->_directory . '.edit', array_merge(compact('data'), $options));
    }

    public function update(RewardRuleRequest $request, $id)
    {
        try {
            $this->_repo->update($id, RewardRuleDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Reward rule updated successfully.');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->_repo->destroy($id);
            return redirect()->route($this->_route . '.index')->with('success', 'Reward rule deleted successfully.');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
            return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
        }
    }
}
