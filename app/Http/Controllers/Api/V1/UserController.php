<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller {
    use AuthorizesRequests;
    protected UserService $userService;

    /**
     * Summary of __construct
     * @param UserService $userService
     */
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $this->authorize('viewAny', User::class);
        $filters = $request->only(['name', 'email', 'id']);
        $users = $this->userService->getAll($filters);
        return $this->successMessage('Successfully Get All Users .', $users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) {
        $this->authorize('view', $user);
        $data = $this->userService->getUser($user);
        return  $this->successMessage('Successfully Get User Information .', $data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request,  User $user) {
        $this->authorize('update', $user);
        $data = $this->userService->update($user, $request->validated());
        return  $this->successMessage('Successfully Update User Information .', $data, 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) {
        $this->authorize('delete', $user);
        $success = $this->userService->destroy($user);
        return  $this->successMessage('Successfully Delete User Information .', $success, 200);
    }
}
