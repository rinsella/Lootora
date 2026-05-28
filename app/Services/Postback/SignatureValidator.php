<?php

namespace App\Services\Postback;

/**
 * SignatureValidator — provider-specific signature checks. Each provider
 * publishes its own signature scheme; common patterns are supported here.
 *
 * Add a new provider scheme by extending the match in validate().
 */
class SignatureValidator
{
    /**
     * @param  string  $provider  Provider slug (e.g. "cpalead").
     * @param  array   $payload   Full request payload.
     * @param  string  $secret    Postback secret stored on the provider record.
     */
    public function validate(string $provider, array $payload, string $secret): bool
    {
        $provider = strtolower($provider);

        // Always allow if no secret configured (operator decision).
        if ($secret === '') {
            return true;
        }

        return match ($provider) {
            // OfferToro: md5(oid . user_id . payout . secret)
            'offertoro' => $this->compareMd5(
                ($payload['oid'] ?? '').($payload['user_id'] ?? '').($payload['payout'] ?? '').$secret,
                $payload['hash'] ?? '',
            ),

            // AdGem: hash_hmac sha256 of transaction_id with secret
            'adgem' => $this->compareHmacSha256(
                (string) ($payload['transaction_id'] ?? ''),
                $secret,
                (string) ($payload['signature'] ?? $payload['hash'] ?? ''),
            ),

            // CPALead: md5(payout . secret . subid)
            'cpalead' => $this->compareMd5(
                ($payload['payout'] ?? '').$secret.($payload['subid'] ?? ''),
                $payload['hash'] ?? $payload['security_token'] ?? '',
            ),

            // Generic provider — expect hash_hmac sha256 over sorted payload, key=signature.
            default => $this->validateGeneric($payload, $secret),
        };
    }

    private function validateGeneric(array $payload, string $secret): bool
    {
        $provided = (string) ($payload['signature'] ?? $payload['hash'] ?? '');
        if ($provided === '') {
            return false;
        }
        $payload = collect($payload)
            ->except(['signature', 'hash'])
            ->sortKeys()
            ->map(fn($v) => is_scalar($v) ? (string) $v : json_encode($v))
            ->implode('|');

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $provided);
    }

    private function compareMd5(string $input, string $provided): bool
    {
        if ($provided === '') return false;
        return hash_equals(md5($input), strtolower($provided));
    }

    private function compareHmacSha256(string $input, string $secret, string $provided): bool
    {
        if ($provided === '') return false;
        return hash_equals(hash_hmac('sha256', $input, $secret), strtolower($provided));
    }
}
