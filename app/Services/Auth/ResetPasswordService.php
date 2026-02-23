<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordService
{
    /**
     * Reset the password for the given email with token.
     *
     * @param array $data
     * @return string
     * @throws ValidationException
     */
    public function reset(array $data): string
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make(($password));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return __($status);
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)]
            ]);
        }
    }
}