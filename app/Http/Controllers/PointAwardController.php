<?php

namespace App\Http\Controllers;

use App\Dto\PointAwardDto;
use App\Http\Requests\PointAwardRequest;
use App\Repositories\PointAwardRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PointAwardController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(PointAwardRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/points-awards', 'pointawards');
    }

    public function index()
    {
        $filters = request()->only(['search', 'merchant_id', 'site_id', 'date_from', 'date_to']);
        $data['all'] = $this->_repo->listWithFilters($filters);

        $options = $this->_repo->formOptions(Auth::user());

        return view($this->_directory . '.all', array_merge([
            'data' => $data,
            'filters' => $filters,
        ], $options));
    }

    public function create()
    {
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.create', $options);
    }

    public function show($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        return view($this->_directory . '.show', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        $options = $this->_repo->formOptions(
            Auth::user(),
            $data->site_id,
            $data->merchant_id,
            $data->user_id
        );

        return view($this->_directory . '.edit', array_merge(['data' => $data], $options));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PointAwardRequest $request)
    {
        try {
            $this->_repo->store(PointAwardDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */
    public function update(PointAwardRequest $request, $id)
    {
        try {
            $this->_repo->update($id, PointAwardDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Updated succesfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage(); // Get the exception message
                return redirect()->route($this->_route . '.index')->with('error', $message);
            } else {
                return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
            }
        }
    }
}
