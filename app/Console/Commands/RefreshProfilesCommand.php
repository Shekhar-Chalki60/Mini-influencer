<?php

namespace App\Console\Commands;

use App\Jobs\FetchProfileJob;
use App\Models\Profile;
use Illuminate\Console\Command;

class RefreshProfilesCommand extends Command
{
    protected $signature = 'profiles:refresh';

    protected $description = 'Refresh stale influencer profiles';

    public function handle(): int
    {
        $profiles = Profile::query()
            ->where(function ($query) {
                $query
                    ->whereNull('last_refreshed_at')
                    ->orWhere(
                        'last_refreshed_at',
                        '<',
                        now()->subHours(6)
                    );
            })
            ->get();

        foreach ($profiles as $profile) {

            FetchProfileJob::dispatch($profile);

            $this->info(
                "Dispatched profile {$profile->username}"
            );
        }

        $this->info(
            "Total profiles dispatched: {$profiles->count()}"
        );

        return self::SUCCESS;
    }
}
