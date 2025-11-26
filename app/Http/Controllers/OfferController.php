<?php

namespace App\Http\Controllers;

use App\Dto\OfferDto;
use App\Repositories\OfferRepository;
use App\Http\Requests\OfferRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OfferController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(OfferRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/offers', 'offers');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.create', $options);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfferRequest $request)
    {
        try {
            $this->_repo->store(OfferDto::fromRequest($request));
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

        // Load relationships to ensure site and merchant are available
        $data->load(['merchant', 'site']);

        $options = $this->_repo->formOptions(
            Auth::user(),
            $data->site_id,
            $data->merchant_id ?? ($data->site->merchant_id ?? null)
        );

        return view($this->_directory . '.edit', array_merge(['data' => $data], $options));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */
    public function update(OfferRequest $request, $id)
    {
        try {
            $this->_repo->update($id, OfferDto::fromRequest($request));
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

}
