<?php

use Illuminate\Support\Facades\Route;
use NuxtIt\RP\Http\Controllers\SettingsController;
use NuxtIt\RP\Http\Controllers\UserController;
use NuxtIt\RP\Http\Controllers\RoleController;
use NuxtIt\RP\Http\Controllers\PermissionController;

Route::middleware(['role:admin|superadmin|super-admin'])->group(function () {
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.roles.assign');
    Route::delete('users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');

    // Roles
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions.assign');
    Route::delete('roles/{role}/permissions/{permission}', [RoleController::class, 'removePermission'])->name('roles.permissions.remove');

    // Permissions
    Route::resource('permissions', PermissionController::class);
});

