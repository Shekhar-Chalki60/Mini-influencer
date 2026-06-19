<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebhookTest extends TestCase
{
    public function test_valid_webhook()
    {
        $payload = json_encode(['username' => 'cristiano',]);
        $signature = hash_hmac('sha256', $payload, config('app.webhook_secret'));
        $response = $this->post('/webhooks/apify',
            json_decode($payload, true),
            [
                'X-Webhook-Id' => '1',
                'X-Webhook-Signature' => $signature,
            ]
        );
        $response->assertOk();
    }

    public function test_invalid_signature()
    {
        $response = $this->post('/webhooks/apify',
            [
                'username' => 'cristiano',
            ],
            [
                'X-Webhook-Id' => '2',
                'X-Webhook-Signature' => 'wrong',
            ]
        );
        $response->assertStatus(401);
    }

    public function test_replay_attack()
    {
        $payload = json_encode(['username' => 'cristiano',]);
        $signature = hash_hmac('sha256', $payload, config('app.webhook_secret'));
        $headers = [
            'X-Webhook-Id' => 'same-id',
            'X-Webhook-Signature' => $signature,
        ];
        $this->post('/webhooks/apify', json_decode($payload, true), $headers);
        $response = $this->post('/webhooks/apify', json_decode($payload, true), $headers);
        $response->assertStatus(409);
    }
}
