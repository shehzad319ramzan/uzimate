<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerLogRepository;
use Illuminate\Support\Facades\Auth;

class CustomerScansController extends BaseController
{
    public function __construct(CustomerLogRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/customer-scans', 'customer-scans');
    }

    /**
     * Display a listing of customer scan events (QR code scans only).
     */
    public function index()
    {
        $filters = request()->only(['search', 'merchant_id', 'site_id', 'date_from', 'date_to']);
        $data['all'] = $this->_repo->listScanLogs($filters);

        $options = $this->_repo->formOptions(Auth::user());

        return view($this->_directory . '.all', array_merge([
            'data' => $data,
            'filters' => $filters,
        ], $options));
    }
}
