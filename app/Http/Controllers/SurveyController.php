<?php

namespace App\Http\Controllers;

use App\Dto\SurveyDto;
use App\Http\Requests\SurveyRequest;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Repositories\SurveyRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SurveyController extends BaseController
{
    public function __construct(SurveyRepository $repo)
    {
        $this->setRepo($repo, 'auth/pages/surveys', 'surveys');
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

    public function show($id)
    {
        $data = $this->_repo->show($id);
        if ($data == null) {
            abort(404);
        }
        return view($this->_directory . '.show', compact('data'));
    }

    /**
     * Return HTML partial of survey questions for modal (AJAX).
     */
    public function questionsPreview($id)
    {
        $survey = $this->_repo->show($id);
        if ($survey == null) {
            abort(404);
        }
        return view($this->_directory . '.partials.questions-modal-content', compact('survey'))->render();
    }

    public function edit($id)
    {
        $data = $this->_repo->show($id);
        if ($data == null) {
            abort(404);
        }
        $options = $this->_repo->formOptions(Auth::user());
        return view($this->_directory . '.edit', array_merge(['data' => $data], $options));
    }

    public function store(SurveyRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $questionsInput = $validated['questions'] ?? [];
            unset($validated['questions']);
            $payload = array_merge($validated, ['image' => $request->file('file') ?? $request->file('image')]);
            $survey = $this->_repo->store(SurveyDto::fromRequest($payload));

            foreach ($questionsInput as $sortOrder => $q) {
                $questionText = trim((string) ($q['question_text'] ?? ''));
                if ($questionText === '') {
                    continue;
                }
                $options = isset($q['options']) ? array_values(array_filter(array_map(function ($v) {
                    return trim((string) $v);
                }, $q['options']))) : [];
                if (empty($options)) {
                    continue;
                }
                $question = $survey->questions()->create([
                    'question_text' => $questionText,
                    'sort_order' => $sortOrder + 1,
                ]);
                foreach ($options as $i => $text) {
                    $question->options()->create(['option_text' => $text, 'sort_order' => $i + 1]);
                }
            }

            return redirect()->route($this->_route . '.index')->with('success', 'Survey created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong.');
        }
    }

    public function update(SurveyRequest $request, string $id): RedirectResponse
    {
        try {
            $payload = array_merge($request->validated(), ['image' => $request->file('file') ?? $request->file('image')]);
            $this->_repo->update($id, SurveyDto::fromRequest($payload));
            return redirect()->route($this->_route . '.index')->with('success', 'Survey updated successfully.');
        } catch (NotFoundHttpException $e) {
            return redirect()->route($this->_route . '.index')->with('error', $e->getMessage());
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage() ?? 'Something went wrong.');
        }
    }

    public function storeQuestion(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'question_text' => ['required', 'string', 'max:1000'],
            'options' => ['required', 'array'],
            'options.*' => ['nullable', 'string', 'max:500'],
        ]);
        $options = array_values(array_filter(array_map(function ($v) {
            return trim((string) $v);
        }, $request->options ?? [])));
        if (empty($options)) {
            return redirect()->route($this->_route . '.edit', $id)->with('error', 'At least one option is required.');
        }
        $survey = Survey::findOrFail($id);
        $sortOrder = ($survey->questions()->max('sort_order') ?? 0) + 1;
        $question = $survey->questions()->create([
            'question_text' => $request->question_text,
            'sort_order' => $sortOrder,
        ]);
        foreach ($options as $i => $text) {
            $question->options()->create(['option_text' => $text, 'sort_order' => $i + 1]);
        }
        return redirect()->route($this->_route . '.edit', $id)->with('success', 'Question added.');
    }

    public function destroyQuestion(string $questionId): RedirectResponse
    {
        $question = SurveyQuestion::findOrFail($questionId);
        $surveyId = $question->survey_id;
        $question->delete();
        return redirect()->route($this->_route . '.edit', $surveyId)->with('success', 'Question removed.');
    }
}
