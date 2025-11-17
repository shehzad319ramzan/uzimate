<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionRequest;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionController extends BaseController
{
    public function __construct(PermissionRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/permissions', 'permissions');
    }

    public function index()
    {
        $data['all'] = $this->_repo->index();
        return view($this->_directory . '.all', compact('data'));
    }

    public function create()
    {
        return view($this->_directory . '.create');
    }

    public function store(PermissionRequest $request)
    {
        try {
            $this->_repo->store($request->validated());
            return redirect()->route($this->_route . '.index')->with('success', 'Permission created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        return view($this->_directory . '.edit', compact('data'));
    }

    public function update(PermissionRequest $request, $id)
    {
        try {
            $this->_repo->update($id, $request->validated());
            return redirect()->route($this->_route . '.index')->with('success', 'Permission updated successfully.');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }

            return redirect()->back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->_repo->destroy($id);
            return redirect()->route($this->_route . '.index')->with('success', 'Permission deleted successfully.');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
            return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
        }
    }
}

