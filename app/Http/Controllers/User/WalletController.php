<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\Lootora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $methods = Payment::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();

        $withdrawals = $user->withdrawals()->orderByDesc('created_at')->take(20)->get();

        return view('user.wallet', [
            'user'          => $user,
            'methods'       => $methods,
            'withdrawals'   => $withdrawals,
            'minWithdrawal' => Lootora::minWithdrawal(),
            'usdEquivalent' => Lootora::toUsd($user->current_points ?? 0),
        ]);
    }

    public function withdraw(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'method_id'      => ['required', Rule::exists('payments', 'id')->where('is_active', true)],
            'amount'         => ['required', 'numeric', 'min:1'],
            'account_name'   => ['nullable', 'string', 'max:128'],
            'account_number' => ['required', 'string', 'max:255'],
        ]);

        $method = Payment::findOrFail($data['method_id']);

        $amount = (float) $data['amount'];
        $minMethod = (float) ($method->min_withdrawal ?? 0);
        $minGlobal = (float) Lootora::minWithdrawal();
        $minRequired = max($minMethod, $minGlobal);

        if ($amount < $minRequired) {
            return back()
                ->withInput()
                ->withErrors(['amount' => "Minimum withdrawal for {$method->name} is ".number_format($minRequired, 2).' $LOOT.']);
        }

        if ((float) $user->current_points < $amount) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'You do not have enough $LOOT for this withdrawal.']);
        }

        $accountField = trim($data['account_number']);
        $accountDisplay = !empty($data['account_name'])
            ? trim($data['account_name']).' · '.$accountField
            : $accountField;

        DB::transaction(function () use ($user, $amount, $method, $accountDisplay, $accountField, $data, $request) {
            $u = User::lockForUpdate()->find($user->id);
            if ((float) $u->current_points < $amount) {
                abort(422, 'Insufficient balance.');
            }
            $u->current_points = (float) $u->current_points - $amount;
            $u->save();

            $w = new Withdrawal();
            $w->user_id = $u->id;
            $w->method  = $method->name;
            $w->amount  = $amount;
            $w->account = $accountDisplay;
            $w->status  = 'pending';
            if (Schema::hasColumn('withdrawals', 'payout_method_id'))   $w->payout_method_id = $method->id;
            if (Schema::hasColumn('withdrawals', 'account_name') && !empty($data['account_name'])) {
                $w->account_name = trim($data['account_name']);
            }
            if (Schema::hasColumn('withdrawals', 'account_identifier')) {
                $w->account_identifier = $accountField;
            }
            if (Schema::hasColumn('withdrawals', 'ip')) {
                $w->ip = $request->ip();
            }
            $w->save();

            Notification::create([
                'user_id' => $u->id,
                'title'   => 'Withdrawal requested',
                'message' => "Your withdrawal of {$amount} \$LOOT via {$method->name} was submitted and is awaiting review.",
            ]);
        });

        return redirect()->route('user.wallet')->with('success', 'Withdrawal of '.number_format($amount, 2).' $LOOT submitted for review.');
    }
}
