<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Constants;
use App\Events\CustomerLoggedIn;
use App\Http\Controllers\Controller;
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
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'terms_condition' => 'required|string',
            'fcm_token' => ['nullable', 'string', 'max:500'],
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

            $user = auth()->user();
            if ($user && $request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->input('fcm_token')]);
            }
            if ($user && $user->hasRole(Constants::CUSTOMER)) {
                try {
                    CustomerLoggedIn::dispatch($user->id, $request, 'Customer logged in');
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
            'fcm_token' => ['nullable', 'string', 'max:500'],
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
            if ($request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->input('fcm_token')]);
            }

            // Create API token
            $token = $user->createToken('auth_token')->plainTextToken;

            if ($user && $user->hasRole(Constants::CUSTOMER)) {
                try {
                    CustomerLoggedIn::dispatch($user->id, $request, 'Customer logged in via API');
                } catch (\Exception $e) {
                    Log::error('Failed to create customer log for API login: ' . $e->getMessage());
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
                        'fcm_token' => $user->fcm_token,
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

}
