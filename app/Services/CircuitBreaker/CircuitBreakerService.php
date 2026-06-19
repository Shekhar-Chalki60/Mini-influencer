<?php

namespace App\Services\CircuitBreaker;

use Illuminate\Support\Facades\Cache;

class CircuitBreakerService
{
    private const FAILURE_KEY = 'apify_failures';

    private const OPEN_KEY = 'apify_circuit_open';

    private const HALF_OPEN_KEY = 'apify_half_open';

    public function isOpen(): bool
    {
        return Cache::has(self::OPEN_KEY);
    }

    public function canTest(): bool
    {
        return Cache::add(self::HALF_OPEN_KEY, true, now()->addMinutes(1));
    }

    public function recordSuccess(): void
    {
        Cache::forget(self::FAILURE_KEY);
        Cache::forget(self::OPEN_KEY);
        Cache::forget(self::HALF_OPEN_KEY);
    }

    public function recordFailure(): void
    {
        $failures = Cache::increment(self::FAILURE_KEY);
        if ($failures >= 10) {
            Cache::put(self::OPEN_KEY, true, now()->addMinutes(2));
            Cache::forget(self::FAILURE_KEY);
        }
    }
}
