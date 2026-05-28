<?php

namespace App\Services\Postback;

use App\Models\Offerwall;
use App\Models\PostbackLog;
use App\Models\ProviderTransaction;
use App\Models\User;
use App\Services\Fraud\FraudService;
use App\Services\Rewards\RewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PostbackService — single entry point for every offerwall postback.
 *
 * Flow:
 *   1. Always log the raw payload to postback_logs.
 *   2. Validate user_id, transaction_id, payout.
 *   3. Validate provider signature + optional IP whitelist.
 *   4. Reject duplicates (provider + transaction_id).
 *   5. Within a DB transaction:
 *        - lock user row
 *        - create provider_transactions row
 *        - credit user (or reverse on negative payout)
 *        - award referral upline
 *   6. Update log status; return "OK" / "DUPLICATE" / "REJECTED" / "ERROR".
 */
class PostbackService
{
    public function __construct(
        private SignatureValidator $signatures,
        private RewardService $rewards,
        private FraudService $fraud,
    ) {}

    /**
     * Normalised postback DTO expected from each provider adapter.
     *
     * @param  array{
     *   user_id: int|string|null,
     *   transaction_id: string|null,
     *   payout: float|string|null,    // provider payout in USD
     *   offer_id?: string|null,
     *   offer_name?: string|null,
     *   raw?: array,                  // raw request data for signature/log
     * } $data
     */
    public function process(string $providerSlug, array $data, Request $request): string
    {
        $providerSlug = strtolower($providerSlug);
        $raw          = $data['raw'] ?? $request->all();
        $userId       = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $txId         = $data['transaction_id'] ?? null;
        $payout       = isset($data['payout']) ? (float) $data['payout'] : 0.0;

        $log = PostbackLog::create([
            'provider'        => $providerSlug,
            'user_id'         => $userId,
            'transaction_id'  => $txId,
            'offer_id'        => $data['offer_id']   ?? null,
            'offer_name'      => $data['offer_name'] ?? null,
            'amount'          => $payout,
            'payout'          => $payout,
            'ip_address'      => $request->ip(),
            'country'         => $request->header('CF-IPCountry'),
            'raw_payload'     => json_encode($raw),
            'signature_valid' => false,
            'status'          => 'received',
        ]);

        try {
            if (!$userId || !$txId) {
                return $this->reject($log, 'Missing user_id or transaction_id');
            }

            /** @var User|null $user */
            $user = User::find($userId);
            if (!$user) {
                return $this->reject($log, 'Unknown user');
            }
            if ($user->isBanned()) {
                return $this->reject($log, 'User banned');
            }

            $provider = Offerwall::where('slug', $providerSlug)
                ->orWhere('name', $providerSlug)
                ->first();

            // IP whitelist (optional).
            if ($provider && !empty($provider->ip_whitelist)) {
                $allowed = array_filter(array_map('trim', explode(',', $provider->ip_whitelist)));
                if ($allowed && !in_array($request->ip(), $allowed, true)) {
                    return $this->reject($log, 'IP not whitelisted: '.$request->ip());
                }
            }

            // Signature validation.
            $secret = (string) ($provider->postback_secret ?? '');
            $sigOk  = $this->signatures->validate($providerSlug, $raw, $secret);
            $log->signature_valid = $sigOk;
            $log->save();

            if ($secret !== '' && !$sigOk) {
                $this->fraud->log(null, 'invalid_signature', "Invalid postback signature for {$providerSlug}", [
                    'ip' => $request->ip(),
                    'tx' => $txId,
                ]);
                return $this->reject($log, 'Invalid signature');
            }

            // Duplicate guard.
            $existing = ProviderTransaction::where('provider', $providerSlug)
                ->where('transaction_id', $txId)
                ->first();

            if ($existing) {
                $log->status = 'duplicate';
                $log->save();
                return 'DUPLICATE';
            }

            // Reversal handling.
            if ($payout < 0) {
                return $this->handleReversal($providerSlug, $user, $txId, abs($payout), $log);
            }

            $share   = $provider?->revenue_share_percentage ?? null;
            $break   = $this->rewards->compute($payout, $share !== null ? (float) $share : null);

            DB::transaction(function () use ($providerSlug, $user, $txId, $data, $request, $payout, $break) {
                $tx = ProviderTransaction::create([
                    'provider'         => $providerSlug,
                    'user_id'          => $user->id,
                    'transaction_id'   => $txId,
                    'offer_id'         => $data['offer_id']   ?? null,
                    'offer_name'       => $data['offer_name'] ?? null,
                    'reward_points'    => $break['user_points'],
                    'payout_usd'       => $break['payout_usd'],
                    'platform_profit'  => $break['platform_profit'],
                    'ip_address'       => $request->ip(),
                    'country'          => $request->header('CF-IPCountry'),
                    'status'           => 'completed',
                ]);

                $this->rewards->creditUser($user, $break['user_points']);
                $this->rewards->awardReferral($user, $break['user_points'], $tx->id);
            });

            $log->status = 'accepted';
            $log->save();

            return 'OK';
        } catch (\Throwable $e) {
            Log::error('Postback error: '.$e->getMessage(), [
                'provider' => $providerSlug,
                'trace'    => $e->getTraceAsString(),
            ]);
            $log->status        = 'error';
            $log->error_message = substr($e->getMessage(), 0, 250);
            $log->save();
            return 'ERROR';
        }
    }

    private function reject(PostbackLog $log, string $reason): string
    {
        $log->status        = 'rejected';
        $log->error_message = $reason;
        $log->save();
        return 'REJECTED';
    }

    private function handleReversal(string $provider, User $user, string $txId, float $points, PostbackLog $log): string
    {
        $tx = ProviderTransaction::where('provider', $provider)
            ->where('transaction_id', $txId)
            ->first();

        DB::transaction(function () use ($tx, $user, $points) {
            if ($tx) {
                $tx->status = 'reversed';
                $tx->save();
            }
            $this->rewards->reverseUser($user, $points);
        });

        $log->status = 'accepted';
        $log->save();
        return 'OK';
    }
}
