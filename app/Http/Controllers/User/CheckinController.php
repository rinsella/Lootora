<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Support\Lootora;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckinController extends Controller
{
    /**
     * Daily check-in. One per calendar day per user. Awards LOOT and updates
     * `day_streak` / `best_streak`. Safe to call concurrently (row lock).
     */
    public function store()
    {
        $reward = Lootora::dailyCheckinPoints();

        DB::transaction(function () use ($reward) {
            $user = \App\Models\User::lockForUpdate()->find(auth()->id());
            if (!$user) {
                abort(404);
            }

            $today = now()->startOfDay();
            $last  = $user->last_checkin_at ? Carbon::parse($user->last_checkin_at)->startOfDay() : null;

            if ($last && $last->equalTo($today)) {
                session()->flash('error', 'You have already checked in today. Come back tomorrow!');
                return;
            }

            // Consecutive day? otherwise reset streak.
            if ($last && $last->equalTo($today->copy()->subDay())) {
                $user->day_streak = (int) $user->day_streak + 1;
            } else {
                $user->day_streak = 1;
            }

            if ($user->day_streak > (int) $user->best_streak) {
                $user->best_streak = $user->day_streak;
            }

            $user->last_checkin_at = now();
            $user->addPoints($reward); // also saves

            try {
                Notification::create([
                    'user_id' => $user->id,
                    'title'   => 'Daily check-in reward',
                    'message' => '+'.number_format($reward, 2).' $LOOT credited. Streak: '.$user->day_streak.' days.',
                ]);
            } catch (\Throwable $e) {
                // notifications table schema variations; ignore
            }

            session()->flash('success', '+'.number_format($reward, 2).' $LOOT credited. Streak: '.$user->day_streak.' days.');
        });

        return redirect()->route('user.home');
    }
}
