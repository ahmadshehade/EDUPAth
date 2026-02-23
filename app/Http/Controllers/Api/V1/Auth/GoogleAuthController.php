<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller {

    /**
     * Summary of redirectToGoogle
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToGoogle() {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $url], 200);
    }


    /**
     * Summary of handleGoogleCallback
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback() {
        try {
            Log::info('Callback request', request()->all());

            if (!request()->has('code')) {
                throw new \Exception('No code provided');
            }

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl(env('GOOGLE_REDIRECT_URI')) // نفس الرابط المستخدم في الطلب الأول
                ->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            $token = $user->createToken('auth_user')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Google authentication failed',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
