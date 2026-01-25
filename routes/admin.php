<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\FleetController;
use App\Http\Controllers\Admin\FleetUpdateController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionGroupController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RouteAccessController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'permission.auto'])->prefix('admin')->name('admin.')->group(function () {
    // Areas Management
    Route::post('areas/{area}/toggle-status', [AreaController::class, 'toggleStatus'])->name('areas.toggle-status');
    Route::resource('areas', AreaController::class);

    // Regions Management (nested under areas)
    Route::get('areas/{area}/regions', [RegionController::class, 'index'])->name('areas.regions.index');
    Route::post('areas/{area}/regions', [RegionController::class, 'store'])->name('areas.regions.store');
    Route::put('regions/{region}', [RegionController::class, 'update'])->name('regions.update');
    Route::delete('regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');

    // Agencies Management
    Route::get('agencies/get-regions', [AgencyController::class, 'getRegions'])->name('agencies.get-regions');
    Route::post('agencies/{agency}/toggle-status', [AgencyController::class, 'toggleStatus'])->name('agencies.toggle-status');
    Route::resource('agencies', AgencyController::class);

    // Fleets Management
    Route::get('fleets/export', [FleetController::class, 'export'])->name('fleets.export');
    Route::post('fleets/{fleet}/toggle-status', [FleetController::class, 'toggleStatus'])->name('fleets.toggle-status');
    Route::post('fleets/{fleet}/assign-driver', [FleetController::class, 'assignDriver'])->name('fleets.assign-driver');
    Route::resource('fleets', FleetController::class);

    // Drivers Management
    Route::post('drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggle-status');
    Route::resource('drivers', DriverController::class);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Fleet Update
    Route::get('fleet-update/create', [FleetUpdateController::class, 'create'])->name('fleet-update.create');
    Route::post('fleet-update', [FleetUpdateController::class, 'store'])->name('fleet-update.store');
    Route::get('fleet-update/{fleet}/edit', [FleetUpdateController::class, 'edit'])->name('fleet-update.edit');
    Route::put('fleet-update/{fleet}', [FleetUpdateController::class, 'update'])->name('fleet-update.update');
    Route::get('fleet-update/get-agencies', [FleetUpdateController::class, 'getAgencies'])->name('fleet-update.get-agencies');

    // Users Management
    Route::resource('users', UserController::class);

    // Roles Management
    Route::resource('roles', RoleController::class);

    // Permissions Management
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // Permission Groups Management
    Route::resource('permission-groups', PermissionGroupController::class)->except(['show']);

    // Menu Management
    Route::post('menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');
    Route::resource('menus', MenuController::class)->except(['show']);

    // Route Access Management
    Route::post('route-accesses/scan', [RouteAccessController::class, 'scan'])->name('route-accesses.scan');
    Route::post('route-accesses/bulk-update', [RouteAccessController::class, 'bulkUpdate'])->name('route-accesses.bulk-update');
    Route::post('route-accesses/bulk-destroy', [RouteAccessController::class, 'bulkDestroy'])->name('route-accesses.bulk-destroy');
    Route::post('route-accesses/sync-permissions', [RouteAccessController::class, 'syncPermissions'])->name('route-accesses.sync-permissions');
    Route::resource('route-accesses', RouteAccessController::class)->except(['show']);

    // Activity Logs
    Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::post('activity-logs/bulk-destroy', [ActivityLogController::class, 'bulkDestroy'])->name('activity-logs.bulk-destroy');
    Route::post('activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show', 'destroy']);
});
