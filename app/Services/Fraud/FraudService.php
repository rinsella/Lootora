<?php

namespace App\Services\Fraud;

use App\Models\FraudLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * FraudService — lightweight fraud signals. Records risk events; the admin
 * decides whether to ban / mark suspicious.
 */
class FraudService
{
    public function log(?int $userId, string $type, string $message, array $metadata = [], ?int $riskScore = null): FraudLog
    {
        return FraudLog::create([
            'user_id'    => $userId,
            'type'       => $type,
            'risk_score' => $riskScore,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::header('User-Agent'), 0, 500),
            'message'    => substr($message, 0, 250),
            'metadata'   => $metadata,
        ]);
    }

    /** Flag a user as suspicious without banning them. */
    public function markSuspicious(User $user, string $reason): void
    {
        $user->status = 'suspicious';
        $user->save();
        $this->log($user->id, 'marked_suspicious', $reason);
    }

    /** Count accounts registered from the same IP within a window (hours). */
    public function accountsFromIp(string $ip, int $hours = 24): int
    {
        return User::where('registered_ip', $ip)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();
    }
}
