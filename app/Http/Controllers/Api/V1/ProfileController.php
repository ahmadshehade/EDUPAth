<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Profile;
use App\Services\User\ProfileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProfileController extends Controller {
    protected ProfileService $profileService;
    
     use  AuthorizesRequests;
    /**
     * Summary of __construct
     * @param ProfileService $profileService
     */
    public function __construct(ProfileService $profileService) {
        $this->profileService = $profileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $this->authorize('viewAny',Profile::class);
        $filters = $request->only(['user_id', 'bio', 'address', 'phone', 'date_of_birth', 'website']);
        $profiles = $this->profileService->getAll($filters);
        return $this->successMessage('Successfully Get All Profiles .', $profiles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request) {
        $this->authorize('create',Profile::class);
        $profile = $this->profileService->store($request->validated());
        return  $this->successMessage('Successfully Make New Profile .', $profile, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile) {
        $this->authorize('view',$profile);
        $data = $this->profileService->get($profile);
        return $this->successMessage('Successfully Get Profile .', $profile, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile) {
        $this->authorize('update',$profile);
        $data = $this->profileService->update($profile, $request->validated());
        return $this->successMessage('Successfully Update Profile .', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile  $profile) {
        $this->authorize('delete',$profile);
        $success = $this->profileService->destroy($profile);
        return $this->successMessage('Successfully Delete Profile .', $success, 200);
    }
}
