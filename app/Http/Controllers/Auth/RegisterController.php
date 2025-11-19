<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Permission;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = Constants::DASHBOARD_URL;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('frontend.signup');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'merchant_name' => ['required', 'string', 'max:255'],
            'max_sites' => ['required', 'integer', 'min:1'],
            'spin_after_days' => ['required', 'integer', 'min:1'],
            'scan_after_hours' => ['required', 'integer', 'min:1'],
            'use_other_merchant_points' => ['nullable', 'boolean']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $userArr = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        $user = User::create($userArr);
        
        // Assign admin role
        $user->assignRole(Constants::Admin);

        // Assign merchant permissions
        $merchantModules = [
            'site',
            'site_user',
            'offer',
            'customer_scan',
            'offer_scan',
            'point_award',
            'spin_history',
            'customer_log',
            'inbox',
            'feedback',
        ];

        $actions = ['view', 'add', 'edit', 'delete'];
        $permissions = Permission::where(function ($query) use ($merchantModules, $actions) {
            foreach ($merchantModules as $module) {
                foreach ($actions as $action) {
                    $query->orWhere('name', "{$action}_{$module}");
                }
            }
        })->get();

        $user->syncPermissions($permissions);

        // Create merchant record
        Merchant::create([
            'user_id' => $user->id,
            'name' => $data['merchant_name'],
            'max_sites' => $data['max_sites'],
            'spin_after_days' => $data['spin_after_days'],
            'scan_after_hours' => $data['scan_after_hours'],
            'use_other_merchant_points' => isset($data['use_other_merchant_points']) && $data['use_other_merchant_points'] == '1',
            'status' => '1',
        ]);

        return $user;
    }
}
