<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Postback\PostbackService;
use Illuminate\Http\Request;

/**
 * Generic postback endpoint: /api/postback/{provider}
 *
 * Each provider passes a different parameter naming scheme; this controller
 * applies a best-effort mapping to a normalised DTO and delegates to the
 * PostbackService. Provider-specific controllers may still override.
 */
class PostbackController extends Controller
{
    public function handle(Request $request, string $provider, PostbackService $service)
    {
        $payload = $request->all();

        $userId = $payload['user_id']
            ?? $payload['uid']
            ?? $payload['subid']
            ?? $payload['sub1']
            ?? null;

        $txId = $payload['transaction_id']
            ?? $payload['tx_id']
            ?? $payload['txn_id']
            ?? $payload['trans_id']
            ?? $payload['id']
            ?? null;

        $payout = $payload['payout']
            ?? $payload['amount']
            ?? $payload['revenue']
            ?? 0;

        $response = $service->process($provider, [
            'user_id'        => $userId,
            'transaction_id' => $txId,
            'payout'         => (float) $payout,
            'offer_id'       => $payload['offer_id']   ?? $payload['oid'] ?? null,
            'offer_name'     => $payload['offer_name'] ?? $payload['name'] ?? null,
            'raw'            => $payload,
        ], $request);

        return response($response, 200)->header('Content-Type', 'text/plain');
    }
}
