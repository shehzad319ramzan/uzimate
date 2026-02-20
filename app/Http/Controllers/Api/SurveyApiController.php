<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SurveyFormResource;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveySubmitResource;
use App\Models\CustomerLog;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Services\RewardRuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyApiController extends ApiBaseController
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }


    public function index(Request $request): JsonResponse
    {
        $query = Survey::with('merchant')
            ->where('status', '1')
            ->orderBy('created_at', 'desc');

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $perPage = min((int) $request->get('per_page', 20), 50);
        $surveys = $query->paginate($perPage);

        SurveyResource::$truncateDescription = true;
        $surveysData = SurveyResource::collection($surveys->getCollection())->resolve();
        SurveyResource::$truncateDescription = false;

        return response()->json([
            'success' => true,
            'data' => [
                'surveys' => [
                    'data' => $surveysData,
                    'current_page' => $surveys->currentPage(),
                    'last_page' => $surveys->lastPage(),
                    'per_page' => $surveys->perPage(),
                    'total' => $surveys->total(),
                ],
            ],
        ], 200);
    }


    public function show(string $id): JsonResponse
    {
        $survey = Survey::with('merchant')->where('status', '1')->find($id);
        if (!$survey) {
            return response()->json(['success' => false, 'message' => 'Survey not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => (new SurveyResource($survey))->resolve(),
        ], 200);
    }


    public function form(string $id): JsonResponse
    {
        $survey = Survey::where('status', '1')->with(['questions.options'])->find($id);
        if (!$survey) {
            return response()->json(['success' => false, 'message' => 'Survey not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => (new SurveyFormResource($survey))->resolve(),
        ], 200);
    }


    public function submit(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'uuid', 'exists:survey_questions,id'],
            'answers.*.option_id' => ['required', 'uuid', 'exists:survey_question_options,id'],
        ]);

        $survey = Survey::where('status', '1')->find($id);
        if (!$survey) {
            return response()->json(['success' => false, 'message' => 'Survey not found.'], 404);
        }

        $user = Auth::user();
        $existing = SurveyResponse::where('survey_id', $id)->where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'You have already completed this survey.'], 400);
        }

        $response = SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        foreach ($request->answers as $a) {
            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $a['question_id'],
                'survey_question_option_id' => $a['option_id'],
            ]);
        }

        $points = 0;
        if ($this->rewardRuleService->shouldAwardPointsForAction('survey_completed', $survey->merchant_id)) {
            $points = (int) $survey->points;
        }

        CustomerLog::create([
            'merchant_id' => $survey->merchant_id,
            'site_id' => null,
            'user_id' => $user->id,
            'action_type' => 'survey_completed',
            'action_category' => 'surveys',
            'description' => "Completed survey: {$survey->title} (Survey #{$survey->id})",
            'points_affected' => $points > 0 ? $points : null,
            'related_model_type' => Survey::class,
            'related_model_id' => $survey->id,
            'performed_by_id' => $user->id,
            'metadata' => ['survey_response_id' => $response->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $message = $points > 0 ? "You earned {$points} points!" : 'Thank you for completing the survey!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => (new SurveySubmitResource([
                'points_earned' => $points,
                'survey_id' => $survey->id,
            ]))->resolve(),
        ], 200);
    }
}
