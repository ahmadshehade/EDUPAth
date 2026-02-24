<?php

namespace App\Services\Auth;

use App\Enums\NameOfCache;
use App\Enums\NameOfCahce;
use App\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthInterface {
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array [$user, $token]
     * @throws Exception
     */
    public function register(array $data): array {
        try {
            $data['password']=Hash::make($data['password']);
            $user = User::create($data);
            $token = $user->createToken('auth_user')->plainTextToken;
            Cache::forget(NameOfCache::class::Users->value);
            return [$user, $token];
        } catch (Exception $e) {

            throw new Exception('Failed to register user: ' . $e->getMessage());
        }
    }

    /**
     * Login user.
     *
     * @param array $data
     * @return array [$user, $token]
     * @throws ValidationException
     */
    public function login(array $data): array {
        try {
            $user = User::where('email', $data['email'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }
        if (!Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials are incorrect.']
            ]);
        }
        $token = $user->createToken('auth_user')->plainTextToken;
        return [$user, $token];
    }

    /**
     * Summary of logout
     * @param mixed $user
     * @return bool
     */
    public function logout( $user): bool {
    
        $user->currentAccessToken()->delete();
        return true;
    }

     public  function logoutFromAllTokens($user)
     {
        $user->tokens()->delete();
        return true;
     }


}
