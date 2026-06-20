<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessWebhookJob;

class WebhookController extends Controller
{
    public function handle(Request $request, string $provider)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Webhook-Signature', '');
        $expected = hash_hmac('sha256', $payload, env('WEBHOOK_SECRET'));

        if (! hash_equals($expected, $signature)) {
            abort(401);
        }
        $nonce = $request->header('X-Webhook-Id');
        if (! $nonce) {
            abort(400, 'Missing webhook id');
        }
        if (Cache::has("webhook:{$nonce}")) {
            abort(409);
        }
        Cache::put("webhook:{$nonce}", true, now()->addDay());
        ProcessWebhookJob::dispatch($provider, $request->all());
        return response()->json([
            'received' => true,
        ]);
    }
}
