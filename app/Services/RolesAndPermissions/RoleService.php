<?php

namespace App\Services\RolesAndPermissions;

use App\Models\User;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NewRole as Role;
class RoleService {

    use FilterableServiceTrait;
    /**
     * Get all roles with their permissions.
     *
     * @return Collection<int, Role>
     */
    public function getAllRoles(array $data = [], array $options = []) {
        
        $query = Role::query();
        

        return $this->applyFilters($query, $data, );
    }

    /**
     * Get a role by ID with its permissions.
     *
     * @param int $id
     * @return Role
     */
    public function getRoleById(int $id): Role {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create a new role with optional permissions.
     *
     * @param array $data ['name' => string, 'permissions' => array]
     * @return Role
     * @throws Exception
     */
    public function createRole(array $data): Role {
        try {
            DB::beginTransaction();
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            DB::commit();
            return $role->load('permissions');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing role and its permissions.
     *
     * @param int $id
     * @param array $data ['name' => string, 'permissions' => array]
     * @return Role
     * @throws Exception
     */
    public function updateRole(int $id, array $data): Role {
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $data['name']
            ]);
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            DB::commit();
            return $role->load('permissions');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a role by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteRole(int $id): bool {
        $role = Role::findOrFail($id);
        return (bool) $role->delete();
    }

    /**
     * Assign a role to a user by role ID.
     *
     * @param int $roleId
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function assignRoleToUser(int $roleId, User $user): User {
        try {
            $role = Role::findOrFail($roleId);
            $user->assignRole($role);
            return $user->load('roles');
        } catch (Exception $e) {
            Log::error('Failed to assign role to user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove a role from a user by role ID.
     *
     * @param int $roleId
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function removeRoleFromUser(int $roleId, User $user): User {
        try {
            $role = Role::findOrFail($roleId);
            $user->removeRole($role);
            return $user->load('roles');
        } catch (Exception $e) {
            Log::error('Failed to remove role from user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync roles for a user (replace existing roles with given ones).
     *
     * @param User $user
     * @param array $roleNames
     * @return User
     * @throws Exception
     */
    public function syncUserRoles(User $user, array $roleIds): User {
        try {
            $roles = Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
            return $user->load('roles');
        } catch (Exception $e) {
            Log::error('Failed to sync user roles: ' . $e->getMessage());
            throw $e;
        }
    }
}
