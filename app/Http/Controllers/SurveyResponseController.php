<?php

namespace App\Http\Controllers;

use App\Repositories\SurveyResponseRepository;

class SurveyResponseController extends BaseController
{
    public function __construct(SurveyResponseRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/survey-responses', 'surveyresponses');
    }

    /**
     * List all survey responses (users who filled surveys).
     */
    public function index()
    {
        $filters = request()->only(['survey_id', 'search', 'date_from', 'date_to']);
        $data['all'] = $this->_repo->listWithFilters($filters);
        $options = $this->_repo->formOptions();
        return view($this->_directory . '.all', array_merge(['data' => $data, 'filters' => $filters], $options));
    }

    /**
     * Show a single response with all answers (read-only).
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
