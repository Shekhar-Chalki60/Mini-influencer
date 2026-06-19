<?php

namespace App\Services\Providers;

use App\Exceptions\ProfileFetchException;
use Illuminate\Support\Facades\Http;

class ApifyProfileProvider implements ProfileProviderInterface
{
    public function fetch(string $username): array
    {
        $response = Http::connectTimeout(3)
            ->timeout(20)
            ->withToken(config('services.apify.token'))
            ->post(
                'https://api.apify.com/v2/acts/apify~instagram-scraper/run-sync-get-dataset-items',
                [
                    'directUrls' => [
                        "https://www.instagram.com/{$username}/",
                    ],
                    'resultsType' => 'details',
                    'resultsLimit' => 1,
                ]
            );

        if ($response->status() === 404) {
            throw new ProfileFetchException(false, 'Instagram profile not found');
        }
        if ($response->status() === 401) {
            throw new ProfileFetchException(false, 'Invalid API key');
        }
        if ($response->status() === 429 || $response->serverError()) {
            throw new ProfileFetchException(true, 'Temporary provider failure');
        }

        $profile = $response->json()[0] ?? null;
        if (! $profile) {
            throw new ProfileFetchException(false, 'Invalid provider response');
        }

        return [
            'followers_count' => $profile['followersCount'] ?? 0,
            'following_count' => $profile['followsCount'] ?? 0,
            'posts_count' => $profile['postsCount'] ?? 0,
            'bio' => $profile['biography'] ?? '',
            'profile_picture_url' => $profile['profilePicUrlHD'] ?? $profile['profilePicUrl'] ?? null,
        ];
    }
}
