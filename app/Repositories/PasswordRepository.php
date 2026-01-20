<?php

namespace App\Repositories;

use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VerificationCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class PasswordRepository
{
    public function change_password($request)
    {
        if (!Hash::check($request['old_password'], auth()->user()->password)) {
            throw new NotFoundHttpException('Old Password wroung');
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request['new_password'])
        ]);

        return true;
    }

    public function get_email($email)
    {
        $emailAddress = $email['email'];

        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format.');
        }

        $userRepo = new UserRepository(new User());
        $userExists = $userRepo->validateEmail($emailAddress);
        if (!$userExists) {
            throw new NotFoundHttpException('There is no account related to this email.');
        }

        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        VerificationCode::updateOrCreate(
            ['email' => $emailAddress],
            [
                'code' => $code,
                'ip' => request()->ip(),
                'updated_at' => now(),
            ]
        );

        Mail::to($emailAddress)->send(new VerificationMail($code));

        return true;
    }

    public function verifyCodeOnly($data)
    {
        $verificationCode = VerificationCode::where([
            'code' => $data['code'],
            'ip' => request()->ip(),
        ])->first();
        if (!$verificationCode) {
            throw new NotFoundHttpException('Your code expired or is invalid.');
        }

        return true;
    }

    public function reset_password($data)
    {
        $verificationCode = VerificationCode::where([
            'code' => $data['code'],
            'ip' => request()->ip(),
        ])->first();
        if ($verificationCode == null) {
            throw new NotFoundHttpException('Your code expired or is invalid.');
        }

        $user = User::where('email', $verificationCode->email)->first();
        if (!$user) {
            throw new NotFoundHttpException('User not found for the provided email.');
        }

        $user->password = Hash::make($data['new_password']);
        $user->update();

        $verificationCode->delete();

        return true;
    }
}
