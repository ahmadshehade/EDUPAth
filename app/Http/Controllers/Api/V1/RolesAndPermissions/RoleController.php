<?php

namespace App\Http\Controllers\Api\V1\RolesAndPermissions;

use App\Http\Controllers\Controller;

use App\Http\Requests\RolesAndPermissions\StoreRoleRequest;
use App\Http\Requests\RolesAndPermissions\UpdateRoleRequest;
use App\Models\User;
use App\Services\RolesAndPermissions\RoleService;
use Illuminate\Http\Request;
use App\Models\NewRole as Role;

class RoleController extends Controller {
    protected RoleService $roleService;

    /**
     * Summary of __construct
     * @param RoleService $roleService
     */
    public  function __construct(RoleService  $roleService) {
        $this->roleService = $roleService;
    }

    /**
     * Summary of index
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $filters = $request->only(['name']);
        $roles = $this->roleService->getAllRoles($filters);
        return $this->successMessage('Successfully Get All Roles.', $roles, 200);
    }

    /**
     * Summary of show
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public  function show(Role $role) {
        $role = $this->roleService->getRoleById($role->id);
        return $this->successMessage('Successfully Get Role', $role, 200);
    }
    /**
     * Summary of store
     * @param StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request) {
        $role = $this->roleService->createRole($request->validated());
        return $this->successMessage('Successfully Make New Role . ', $role, 201);
    }
    /**
     * Summary of update
     * @param Role $role
     * @param UpdateRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Role $role, UpdateRoleRequest $request) {
        $data = $this->roleService->updateRole($role->id, $request->validated());
        return $this->successMessage('Successfully Update Role .', $data, 200);
    }
    /**
     * Summary of destroy
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public  function destroy(Role $role) {
        $success = $this->roleService->deleteRole($role->id);
        return $this->successMessage('Successfully Delete Role .', $success, 200);
    }
    /**
     * Summary of assignRoleToUser
     * @param Role $role
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRoleToUser(Role $role, User $user) {
        $data = $this->roleService->assignRoleToUser($role->id, $user);
        return $this->successMessage('Successfully Assign Role To User', $data, 200);
    }

    /**
     * Summary of removeRoleFromUser
     * @param Role $role
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeRoleFromUser(Role $role, User $user) {
        $data = $this->roleService->removeRoleFromUser($role->id, $user);
        return $this->successMessage('Successfully Remove Role From User', $data, 200);
    }

    /**
     * Summary of syncUserRoles
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncUserRoles(Request $request, User $user) {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);
        $roleIds = $request->input('role_ids', []);
        $data = $this->roleService->syncUserRoles($user, $roleIds);
        return $this->successMessage('Successfully Sync Roles To User.', $data, 200);
    }
}
