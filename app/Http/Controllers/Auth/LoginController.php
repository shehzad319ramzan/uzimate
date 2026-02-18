<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Models\CustomerLog;
use App\Services\RewardRuleService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = Constants::DASHBOARD_URL;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('frontend.login');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'terms_condition' => 'required|string',
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            // Auto-create customer log for customer login (always create; points only when rule active)
            $user = auth()->user();
            if ($user && $user->hasRole(Constants::CUSTOMER)) {
                try {
                    $this->createLoginCustomerLog($user->id, $request, 'Customer logged in');
                } catch (\Exception $e) {
                    Log::error('Failed to create customer log for login: ' . $e->getMessage());
                }
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Login user via API (returns JSON with token)
     */
    public function loginWithApi(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user = auth()->user();

            // Create API token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Auto-create customer log for customer login (always create; points only when rule active)
            if ($user && $user->hasRole(Constants::CUSTOMER)) {
                try {
                    $this->createLoginCustomerLog($user->id, $request, 'Customer logged in via API');
                } catch (\Exception $e) {
                    \Log::error('Failed to create customer log for API login: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'full_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
                        'roles' => $user->roles->pluck('name'),
                    ],
                    'token' => $token,
                ]
            ], 200);
        }

        $this->incrementLoginAttempts($request);

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Create customer log for login. Always create log; points only when reward rule is active (and first_time_only respected).
     */
    protected function createLoginCustomerLog($userId, Request $request, string $description): void
    {
        $points = 0;
        if ($this->rewardRuleService->shouldAwardPointsForAction('login', null)) {
            $points = (int) ($this->rewardRuleService->getPointsForAction('login', null) ?? 0);
            if ($points > 0 && $this->rewardRuleService->isFirstTimeOnly('login', null)) {
                // Only skip if user already received points for a previous login (not just any login log)
                $alreadyReceivedLoginPoints = CustomerLog::where('user_id', $userId)
                    ->where('action_type', 'login')
                    ->whereNotNull('points_affected')
                    ->where('points_affected', '>', 0)
                    ->exists();
                if ($alreadyReceivedLoginPoints) {
                    $points = 0;
                }
            }
        }

        CustomerLog::create([
            'user_id' => $userId,
            'action_type' => 'login',
            'action_category' => 'system',
            'description' => $points > 0 ? "{$description} - earned {$points} points" : $description,
            'points_affected' => $points > 0 ? $points : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
