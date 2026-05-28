<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravolt\Avatar\Facade as Avatar;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_checkin_at'   => 'datetime',
        'current_points'    => 'decimal:4',
        'today_points'      => 'decimal:4',
        'total_points'      => 'decimal:4',
        'is_admin'          => 'boolean',
        'is_banned'         => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateReferralCode();
            }
        });
    }

    public static function generateReferralCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function name(): string
    {
        return $this->username;
    }

    public function avatar()
    {
        if ($this->profile_photo_path && file_exists(storage_path('app/public/'.$this->profile_photo_path))) {
            return \Storage::url($this->profile_photo_path);
        }

        // GD-free fallback: external initials avatar service.
        $name = urlencode($this->name() ?: 'L');
        return "https://ui-avatars.com/api/?name={$name}&background=16A34A&color=fff&bold=true&format=svg";
    }

    public function isBanned(): bool
    {
        return $this->is_banned || $this->status === 'banned';
    }

    public function isSuspicious(): bool
    {
        return $this->status === 'suspicious';
    }

    public function referralLink(): string
    {
        return rtrim(config('app.url'), '/').'/register?ref='.$this->referral_code;
    }

    /* ----------------------------- Relations ----------------------------- */

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class)->orderBy('created_at', 'desc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function bonusHistory(): HasMany
    {
        return $this->hasMany(BonusHistory::class);
    }

    public function providerTransactions(): HasMany
    {
        return $this->hasMany(ProviderTransaction::class)->orderBy('created_at', 'desc');
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(UserPaymentAccount::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    public function referralEarnings(): HasMany
    {
        return $this->hasMany(ReferralEarning::class, 'user_id');
    }

    /* ----------------------------- Balance ------------------------------- */

    /**
     * Safely credit LOOT points to the user. Use inside a DB transaction
     * when caller needs atomicity across multiple updates.
     */
    public function addPoints(float|int|string $points): void
    {
        $points = (float) $points;
        $this->current_points = (float) $this->current_points + $points;
        $this->today_points   = (float) $this->today_points   + $points;
        $this->total_points   = (float) $this->total_points   + $points;
        $this->save();
    }

    public function deductPoints(float|int|string $points): bool
    {
        $points = (float) $points;
        if ((float) $this->current_points < $points) {
            return false;
        }
        $this->current_points = (float) $this->current_points - $points;
        $this->save();
        return true;
    }
}

