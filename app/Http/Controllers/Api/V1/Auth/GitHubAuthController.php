<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller {
    /**
     * Redirect the user to GitHub's OAuth page.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToGitHub() {
        try {

            $url = Socialite::driver('github')
                ->scopes(['user:email'])
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'success' => true,
                'url' => $url
            ], 200);
        } catch (\Exception $e) {
            Log::error('GitHub redirect error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate GitHub login URL'
            ], 500);
        }
    }

    /**
     * Handle the callback from GitHub.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGitHubCallback() {
        try {
            Log::info('GitHub callback request', request()->all());
            if (!request()->has('code')) {
                throw new \Exception('No code provided in the request');
            }
            $githubUser = Socialite::driver('github')
                ->stateless()
                ->redirectUrl(env('GITHUB_REDIRECT_URI')) // يجب تطابق ما في .env
                ->user();
            if (!$githubUser->getEmail()) {
                throw new \Exception('Email not provided by GitHub. Make sure to request user:email scope.');
            }
            $user = User::where('email', $githubUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'email' => $githubUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'github_id' => $githubUser->getId(),
                ]);
            } else {
                if (empty($user->github_id)) {
                    $user->update(['github_id' => $githubUser->getId()]);
                }
            }
            $token = $user->createToken('auth_user')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('GitHub login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'GitHub authentication failed',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
