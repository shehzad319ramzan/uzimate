<?php

namespace App\Http\Controllers;

use App\Repositories\InviteFriendRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InviteFriendController extends BaseController
{
    public function __construct(InviteFriendRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/invite-friends', 'invitefriends');
    }

    public function index()
    {
        $filters = request()->only(['search', 'date_from', 'date_to']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        $options = $this->_repo->formOptions();
        return view($this->_directory . '.all', array_merge(['data' => $data, 'filters' => $filters], $options));
    }

    public function show($id)
    {
        $data = $this->_repo->show($id);
        if ($data === null) {
            abort(404);
        }
        return view($this->_directory . '.show', compact('data'));
    }

    public function destroy($id)
    {
        try {
            $this->_repo->destroy($id);
            return redirect()->route($this->_route . '.index')->with('success', 'Deleted successfully.');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return redirect()->route($this->_route . '.index')->with('error', $th->getMessage());
            }
            return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
        }
    }
}
