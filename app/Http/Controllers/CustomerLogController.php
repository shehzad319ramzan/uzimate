<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerLogRepository;
use Illuminate\Support\Facades\Auth;

class CustomerLogController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(CustomerLogRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/customer-logs', 'customerlogs');
    }

    /**
     * Display a listing of customer logs (read-only audit trail).
     * Customer logs are automatically generated - no manual creation.
     */
    public function index()
    {
        $filters = request()->only(['search', 'merchant_id', 'site_id', 'action_type', 'action_category', 'date_from', 'date_to']);
        $data['all'] = $this->_repo->listWithFilters($filters);

        $options = $this->_repo->formOptions(Auth::user());

        return view($this->_directory . '.all', array_merge([
            'data' => $data,
            'filters' => $filters,
        ], $options));
    }

    /**
     * Display the specified customer log (read-only).
     */
    public function show($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        return view($this->_directory . '.show', compact('data'));
    }
}
