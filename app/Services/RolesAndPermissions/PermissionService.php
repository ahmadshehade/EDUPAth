<?php

namespace App\Services\RolesAndPermissions;

use App\Models\User;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NewPermission  as Permission;
use Illuminate\Support\Facades\Log;
use App\Models\NewRole as Role;


class PermissionService {

    use FilterableServiceTrait;
    public function getAllPermissions(array  $data = [], array $options = []) {
        $permissions = Permission::query();
        return $this->applyFilters($permissions, $data);
    }

    /**
     * Get a permission by ID.
     *
     * @param int $id
     * @return Permission
     */
    public function getPermissionById(int $id): Permission {
        return Permission::findOrFail($id);
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return Permission
     * @throws Exception
     */
    public function createPermission(array $data): Permission {
        try {
            $permission = Permission::create([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);
            return $permission;
        } catch (Exception $e) {
            Log::error('Failed to create permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing permission.
     *
     * @param int $id
     * @param array $data
     * @return Permission
     * @throws Exception
     */
    public function updatePermission(int $id, array $data): Permission {
        try {
            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $data['name']
            ]);
            return $permission->fresh();
        } catch (Exception $e) {
            Log::error('Failed to update permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a permission by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deletePermission(int $id): bool {
        $permission = Permission::findOrFail($id);
        return (bool) $permission->delete();
    }

    /**
     * Assign a permission to a user.
     *
     * @param int $permissionId
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function assignPermissionToUser(int $permissionId, User $user): User {
        try {
            $permission = Permission::findOrFail($permissionId);
            $user->givePermissionTo($permission);
            return $user->load(['roles', 'permissions']);
        } catch (Exception $e) {
            Log::error('Failed to assign permission to user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove a permission from a user.
     *
     * @param int $permissionId
     * @param User $user
     * @return User
     */
    public function removePermissionFromUser(int $permissionId, User $user): User {
        $permission = Permission::findOrFail($permissionId);
        $user->revokePermissionTo($permission);
        return $user->load(['roles', 'permissions']);
    }

    /**
     * Sync multiple permissions to a user (replace existing ones).
     *
     * @param array $permissionIds
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function syncPermissionsToUser(array $permissionIds, User $user): User {
        try {
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $user->syncPermissions($permissions);
            return $user->load(['roles', 'permissions']);
        } catch (Exception $e) {
            Log::error('Failed to sync permissions to user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of assignPermissionsToRole
     * @param array $permissionIds
     * @param Role $role
     * @return Role
     */
    public function assignPermissionsToRole(array $permissionIds, Role $role): Role {

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->givePermissionTo($permissions);
        return $role->load('permissions');
    }

    /**
     * Summary of syncPermissionsToRole
     * @param array $permissionIds
     * @param Role $role
     * @return Role
     */
    public function syncPermissionsToRole(array $permissionIds, Role $role): Role {

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }


    /**
     * Summary of removePermissionFromRole
     * @param int $permissionId
     * @param Role $role
     * @return Role
     */
    public  function removePermissionFromRole(int $permissionId,Role $role){
        $permission=Permission::findOrFail($permissionId);
        $role->revokePermissionTo($permission);
        return $role->load('permissions');
    }
}
