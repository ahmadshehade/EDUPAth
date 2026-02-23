<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordService
{
    /**
     * Send reset link to the given email.
     *
     * @param array $data
     * @return string
     * @throws ValidationException
     */
    public function sendResetLink(array $data): string
    {
        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return __($status);
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)]
            ]);
        }
    }
}