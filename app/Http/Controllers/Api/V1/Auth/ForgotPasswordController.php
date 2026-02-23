<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\ForgotPasswordService;
use App\Services\Auth\ResetPasswordService;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    protected ForgotPasswordService $forgotPasswordService;
    protected ResetPasswordService $resetPasswordService;

    public function __construct(
        ForgotPasswordService $forgotPasswordService,
        ResetPasswordService $resetPasswordService
    ) {
        $this->forgotPasswordService = $forgotPasswordService;
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * Send reset link to user email.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $message = $this->forgotPasswordService->sendResetLink($request->validated());

        return $this->successMessage($message, null, 200);
    }

    /**
     * Reset password using token.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $message = $this->resetPasswordService->reset($request->validated());

        return $this->successMessage($message, null, 200);
    }
}