<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $provider, public array $payload) {
    }

    public function handle(): void
    {
        logger()->info(
            'WEBHOOK RECEIVED',
            [
                'provider' => $this->provider,
                'payload' => $this->payload,
            ]
        );
    }
}
