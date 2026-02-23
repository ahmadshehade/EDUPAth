<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authentication;

    /**
     * Summary of __construct
     * @param AuthService $authentication
     */
    public function __construct(AuthService $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Summary of register
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $userData = $this->authentication->register($request->validated());
        return $this->successMessage(
            'Successfully registered new user',
            $userData,
            201
        );
    }

    /**
     * Summary of login
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $loginData = $this->authentication->login($request->validated());
        return $this->successMessage(
            'Successfully logged in',
            $loginData,
            200
        );
    }

    /**
     * Summary of logout
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
           
            $success = $this->authentication->logout( $request->user());
            
            return $this->successMessage(
                'Successfully logged out from this session.',
                $success,
                200
            );
        } catch (\Exception $e) {
            return $this->failMessage(
                'Failed to logout. Please try again.',
                $e->getMessage(),
                422
            );
        }
    }


    /**
     * Summary of logoutFromAllToken
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutFromAllToken(Request $request){
        try {
           
            $success = $this->authentication->logoutFromAllTokens( $request->user());
            
            return $this->successMessage(
                'Successfully logged out from all session.',
                $success,
                200
            );
        } catch (\Exception $e) {
            return $this->failMessage(
                'Failed to logout. Please try again.',
                $e->getMessage(),
                422
            );
        }
    }
}