<?php

namespace App\Http\Controllers\Api\V1\RolesAndPermissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolesAndPermissions\StorePermissionRequest;
use App\Http\Requests\RolesAndPermissions\UpdatePermissionRequest;
use App\Services\RolesAndPermissions\PermissionService;
use Illuminate\Http\Request;
use App\Models\NewPermission  as Permission;
use App\Models\User;
use App\Models\NewRole as Role;

class PermissionController extends Controller {

    /**
     * Summary of permissionService
     * @var PermissionService
     */
    protected PermissionService $permissionService;
    /**
     * Summary of __construct
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService) {
        $this->permissionService = $permissionService;
    }

    /**
     * Summary of index
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request  $request) {
        $filters = $request->only(['name', 'id']);
        $permissions = $this->permissionService->getAllPermissions($filters);
        return $this->successMessage('Successfully Get All Permissions .', $permissions, 200);
    }
    /**
     * Summary of store
     * @param StorePermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request) {
        $permission = $this->permissionService->createPermission($request->validated());
        return $this->successMessage('Successfully Make New Permission .', $permission, 201);
    }
    /**
     * Summary of show
     * @param mixed $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission  $permission) {
        $data = $this->permissionService->getPermissionById($permission->id);
        return $this->successMessage('Successfully Get  Permission .', $data, 200);
    }
    /**
     * Summary of update
     * @param Permission $permission
     * @param UpdatePermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Permission $permission, UpdatePermissionRequest $request) {
        $data = $this->permissionService->updatePermission($permission->id, $request->validated());
        return $this->successMessage('Successfully Update Permission .', $data, 200);
    }
    /**
     * Summary of destroy
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission) {
        $success = $this->permissionService->deletePermission($permission->id);
        return $this->successMessage('Successfully Delete Permissions .', $success, 200);
    }
    /**
     * Summary of assignPermissionToUser
     * @param Permission $permission
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissionToUser(Permission $permission, User $user) {
        $data = $this->permissionService->assignPermissionToUser($permission->id, $user);
        return $this->successMessage('Successfully Assign Permissions To User .', $data, 200);
    }
    /**
     * Summary of removePermissionFromUser
     * @param Permission $permission
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public  function removePermissionFromUser(Permission $permission, User $user) {

        $data = $this->permissionService->removePermissionFromUser($permission->id, $user);
        return $this->successMessage('Successfully Remove Permissions From User .', $data, 200);
    }
    /**
     * Summary of syncPermissionsToUser
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermissionsToUser(Request $request, User $user) {
        $request->validate([
            'permissionIds' => 'required|array',
            'permissionIds.*' => 'exists:permissions,id'
        ]);
        $permissionIds = $request->permissionIds;
        $data = $this->permissionService->syncPermissionsToUser($permissionIds, $user);
        return $this->successMessage('Successfully Sync Permissions To User .', $data, 200);
    }
    /**
     * Summary of assignPermissionsToRole
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissionsToRole(Request $request, Role $role) {
        $request->validate([
            'permissionIds' => 'required|array',
            'permissionIds.*' => 'exists:permissions,id'
        ]);
        $permissionIds = $request->permissionIds;
        $data = $this->permissionService->assignPermissionsToRole($permissionIds, $role);
        return $this->successMessage('Successfully Assign Permissions To Role .', $data, 200);
    }
    /**
     * Summary of syncPermissionsToRole
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermissionsToRole(Request $request, Role $role) {
        $request->validate([
            'permissionIds' => 'required|array',
            'permissionIds.*' => 'exists:permissions,id'
        ]);
        $permissionIds = $request->permissionIds;
        $data = $this->permissionService->syncPermissionsToRole($permissionIds, $role);
        return $this->successMessage('Successfully Sync Permissions To Role .', $data, 200);
    }
    /**
     * Summary of removePermissionFromRole
     * @param Permission $permission
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public  function removePermissionFromRole(Permission $permission ,Role $role){
        $data=$this->permissionService->removePermissionFromRole($permission->id,$role);
        return $this->successMessage('Successfully Remove Permission From Role .',$data,200);
    }
}
