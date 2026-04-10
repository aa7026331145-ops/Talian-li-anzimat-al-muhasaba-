<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('tenant')->group(function () {
        // Chart of Accounts
        Route::apiResource('accounts', AccountController::class);

        // Journal Entries
        Route::get('/journal-entries', [JournalEntryController::class, 'index']);
        Route::post('/journal-entries', [JournalEntryController::class, 'store']);
        Route::get('/journal-entries/{journalEntry}', [JournalEntryController::class, 'show']);
        Route::post('/journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post']);
    });

    // Super-admin only
    Route::middleware('role:super-admin')->group(function () {
        Route::apiResource('tenants', TenantController::class);
    });
});
