<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::resource('profiles', ProfileController::class);
    Route::post('/profiles/{profile}/refresh', [ProfileController::class, 'refresh'])->name('profiles.refresh');
    Route::get('/health/rate-limit', function () {
        return [
            'remaining_tokens' => app(\App\Services\RateLimit\TokenBucketService::class)->remaining(),
        ];
    });
});

    Route::get('/healthz', function () {
        try {
            DB::select('select 1');
            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'time' => now(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'database' => 'down',
            ], 500);
        }
    });
    Route::post('/webhooks/apify', [WebhookController::class, 'handle']);
    Route::post('/webhooks/{provider}', [WebhookController::class, 'handle']);

require __DIR__.'/settings.php';

