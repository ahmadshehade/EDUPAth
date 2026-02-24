<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\GitHubAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RolesAndPermissions\PermissionController;
use App\Http\Controllers\Api\V1\RolesAndPermissions\RoleController;
use App\Http\Controllers\Api\V1\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Authentication Routes (v1)
 * 
 * This group contains all authentication-related endpoints for the API v1,
 * including:
 * - Standard email/password registration and login.
 * - Password reset flow (forgot/reset).
 * - OAuth2 authentication with Google and GitHub.
 */
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgot']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);


    Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

    Route::get('auth/github', [GitHubAuthController::class, 'redirectToGitHub']);
    Route::get('auth/github/callback', [GitHubAuthController::class, 'handleGitHubCallback']);
});


/**
 * Protected Routes (Requires Authentication)
 * 
 * These routes are accessible only to authenticated users via Sanctum token.
 * They handle session management including logging out from the current device
 * and revoking all tokens (logout from all devices).
 */
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::post('logout-from-all', [AuthController::class, 'logoutFromAllToken'])
        ->name('logoutFrom.all');
});

/**
 * API Routes for Role Management (Admin only).
 * 
 * All routes in this group are protected by 'auth:sanctum' middleware (authenticated users)
 * and 'can:adminJob' middleware, meaning only users with the 'adminJob' ability
 * (typically administrators) can access these endpoints.
 * 
 * These routes provide CRUD operations for roles, as well as assignment,
 * removal, and synchronization of roles to users.
 * 
 * @see App\Http\Controllers\Api\V1\RolesAndPermissions\RoleController
 */
Route::prefix('v1/roles')->middleware(['auth:sanctum', 'can:adminJob'])
    ->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('roles.all');
        Route::post('/make-role', [RoleController::class, 'store'])
            ->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])
            ->name('roles.show');
        Route::post('/{role}', [RoleController::class, 'update'])
            ->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy');

        Route::post('/assign-role/{role}/to-user/{user}', [RoleController::class, 'assignRoleToUser'])
            ->name('roles.assignUser');
        Route::post('/remove-role/{role}/to-user/{user}', [RoleController::class, 'removeRoleFromUser'])
            ->name('roles.removeUser');
        Route::post('/sync-roles-to-user/{user}', [RoleController::class, 'syncUserRoles'])
            ->name('roles.syncUserRoles');
    });
/**
 * API Routes for Permission Management (Admin only).
 * 
 * These routes are protected by the 'can:adminJob' middleware, which restricts access
 * to users with the 'adminJob' ability (typically administrators). They provide
 * full CRUD operations for permissions, as well as endpoints to assign, remove,
 * and sync permissions to users and roles.
 * 
 * @see App\Http\Controllers\Api\V1\RolesAndPermissions\PermissionController
 */
Route::prefix('v1/permissions')->middleware(['can:adminJob'])
    ->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->name('permissions.all');
        Route::post('/', [PermissionController::class, 'store'])
            ->name('permissions.store');
        Route::get('/{permission}', [PermissionController::class, 'show'])
            ->name('permissions.show');
        Route::post('/{permission}', [PermissionController::class, 'update'])
            ->name('permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])
            ->name('permissions.delete');

        Route::post('/assign-permission/{permission}/to-user/{user}', [PermissionController::class, 'assignPermissionToUser'])
            ->name('permissions.assignUser');
        Route::post('/remove-permission/{permission}/from-user/{user}', [PermissionController::class, 'removePermissionFromUser'])
            ->name('permissions.removeFromUser');
        Route::post('/sync-permissions/to/user/{user}', [PermissionController::class, 'syncPermissionsToUser'])
            ->name('permissions.syncUser');
        Route::post('/assign-permissions/to/role/{role}', [PermissionController::class, 'assignPermissionsToRole'])
            ->name('permissions.assignRole');
        Route::post('/sync-permissions/to/role/{role}', [PermissionController::class, 'syncPermissionsToRole'])
            ->name('permmissions.syncRole');
        Route::post('/remove-permission/{permission}/from-role/{role}', [PermissionController::class, 'removePermissionFromRole'])
            ->name('permissions.removeFromRole');
    });


Route::prefix('v1/users')->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->name('users.all');
        Route::get('/{user}', [UserController::class, 'show'])
            ->name('users.show');
        Route::post('/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

Route::prefix('v1/profiles')->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', [ProfileController::class, 'index'])
            ->name('profiles.all');
        Route::post('/', [ProfileController::class, 'store'])
            ->name('profiles.store');
        Route::get('/{profile}', [ProfileController::class, 'show'])
            ->name('profiles.show');
        Route::post('/{profile}', [ProfileController::class, 'update'])
            ->name('profiles.update');
        Route::delete('/{profile}', [ProfileController::class, 'destroy'])
            ->name('profiles.delete');
    });
