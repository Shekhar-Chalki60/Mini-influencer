<?php

namespace App\Services\RateLimit;

use Illuminate\Support\Facades\Cache;

class TokenBucketService
{
    private string $key = 'apify-token-bucket';

    private int $capacity = 100;

    private int $refillRatePerMinute = 10;

    public function consume(int $tokens = 1): bool
    {
        $bucket = Cache::get($this->key, [
            'tokens' => $this->capacity,
            'last_refill' => now()->timestamp,
        ]);
        $now = now()->timestamp;
        $elapsedMinutes = ($now - $bucket['last_refill']) / 60;
        $refill = floor($elapsedMinutes * $this->refillRatePerMinute);
        if ($refill > 0) {
            $bucket['tokens'] = min($this->capacity, $bucket['tokens'] + $refill);
            $bucket['last_refill'] = $now;
        }
        if ($bucket['tokens'] < $tokens) {
            Cache::put($this->key, $bucket, now()->addDay());
            return false;
        }
        $bucket['tokens'] -= $tokens;
        Cache::put($this->key, $bucket, now()->addDay());
        return true;
    }

    public function remaining(): int
    {
        return Cache::get($this->key, ['tokens' => $this->capacity])['tokens'];
    }
}
