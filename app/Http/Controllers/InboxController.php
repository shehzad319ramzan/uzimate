<?php

namespace App\Http\Controllers;

use App\Dto\InboxDto;
use App\Http\Requests\InboxRequest;
use App\Repositories\InboxRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InboxController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(InboxRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/inbox', 'inbox');
    }

    public function index()
    {
        $filters = request()->only(['search', 'status']);
        $data['all'] = $this->_repo->listWithFilters($filters);

        return view($this->_directory . '.all', [
            'data' => $data,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view($this->_directory . '.create');
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

        return view($this->_directory . '.edit', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InboxRequest $request)
    {
        try {
            $this->_repo->store(InboxDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InboxRequest $request, $id)
    {
        try {
            $this->_repo->update($id, InboxDto::fromRequest($request->validated()));
            return redirect()->route($this->_route . '.index')->with('success', 'Updated succesfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage();
                return redirect()->route($this->_route . '.index')->with('error', $message);
            } else {
                return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong..');
            }
        }
    }
}
