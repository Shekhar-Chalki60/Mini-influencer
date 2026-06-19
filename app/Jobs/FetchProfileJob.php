<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\ProfileSnapshot;
use App\Services\Locking\AdvisoryLockService;
use App\Services\Providers\ProfileProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ProfileFetchException;
use App\Services\CircuitBreaker\CircuitBreakerService;
use App\Services\RateLimit\TokenBucketService;

class FetchProfileJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public Profile $profile) {
    }

    public function handle(): void
    {
        $circuitBreaker = app(CircuitBreakerService::class);
        if ($circuitBreaker->isOpen()) {
            logger()->warning('Circuit breaker open');
            self::dispatch($this->profile)->delay(now()->addMinutes(2));
            return;
        }
        $bucket = app(TokenBucketService::class);
        if (! $bucket->consume()) {
            logger()->warning('Rate limit exceeded');
            self::dispatch($this->profile)->delay(now()->addSeconds($this->nextDelay()));
            return;
        }
        logger()->info('JOB STARTED', [
            'profile_id' => $this->profile->id,
        ]);
        $lockService = app(AdvisoryLockService::class);
        if (! $lockService->acquire($this->profile->id)) {
            logger()->info('LOCK FAILED');
            return;
        }
        try {
            logger()->info('UPDATING STATUS');
            $this->profile->update([
                'status' => Profile::STATUS_FETCHING,
            ]);
            $provider = app(ProfileProviderInterface::class);
            logger()->info('CALLING APIFY');
            $data = $provider->fetch($this->profile->username);
            logger()->info('DATA RECEIVED', $data);
            DB::transaction(function () use ($data) {
                ProfileSnapshot::create([
                    'profile_id' => $this->profile->id,
                    'followers_count' => $data['followers_count'],
                    'following_count' => $data['following_count'],
                    'posts_count' => $data['posts_count'],
                    'captured_at' => now(),
                ]);

                $this->profile->update([
                    'status' => Profile::STATUS_FETCHED,
                    'followers_count' => $data['followers_count'],
                    'following_count' => $data['following_count'],
                    'posts_count' => $data['posts_count'],
                    'bio' => $data['bio'],
                    'profile_picture_url' => $data['profile_picture_url'],
                    'last_refreshed_at' => now(),
                ]);
            });
            logger()->info('JOB SUCCESS');
            $circuitBreaker->recordSuccess();
        }catch (ProfileFetchException $e) {
            logger()->error($e->getMessage());
            $circuitBreaker->recordFailure();
            if (! $e->retriable) {
                $this->profile->update([
                    'status' => Profile::STATUS_FAILED,
                    'last_error' => $e->getMessage(),
                ]);
                return;
            }
            throw $e;
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
            $circuitBreaker->recordFailure();
            throw $e;
        }finally {
            $lockService->release($this->profile->id);
        }
    }

    public function backoff(): array
    {
        return [
            60,
            120,
            240,
            480,
            960,
        ];
    }

    private function nextDelay(): int
    {
        return match ($this->attempts()) {
            1 => 60,
            2 => 120,
            3 => 240,
            4 => 480,
            default => 960,
        };
    }
}
