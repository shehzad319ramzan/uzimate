<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ForgotPasswordApiRequest;
use App\Http\Requests\Password\GetEmailApiRequest;
use App\Http\Requests\Password\ResetPasswordApiRequest;
use App\Http\Requests\Password\VerificationCodeApiRequest;
use App\Repositories\PasswordRepository;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordApiController extends Controller
{
    private $_repo = null;

    public function __construct(PasswordRepository $repo)
    {
        $this->_repo = $repo;
    }

    public function change_password(ResetPasswordApiRequest $request)
    {
        try {
            $this->_repo->change_password($request->validated());
            return response()->json(["success" => 'Password changed successfully'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage();
                return response()->json(["error" => $message], Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json(["error" => 'Something went wrong..'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function get_email(GetEmailApiRequest $request)
    {
        try {
            $this->_repo->get_email($request->validated());
            return response()->json(["success" => 'Validated! Check your email for verification code'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage();
                return response()->json(["error" => $message], Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json(["error" => 'Something went wrong..'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function verification_code(VerificationCodeApiRequest $request)
    {
        try {
            $this->_repo->verifyCodeOnly($request->validated());
            return response()->json(["success" => 'Code verified successfully! Now you can reset your password.'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                return response()->json(["error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            return response()->json(["error" => 'Something went wrong..'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reset_password(ForgotPasswordApiRequest $request)
    {
        try {
            $this->_repo->reset_password($request->validated());
            return response()->json(["success" => 'Password reset successfully! Now you can login'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage();
                return response()->json(["error" => $message], Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json(["error" => 'Something went wrong..'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
