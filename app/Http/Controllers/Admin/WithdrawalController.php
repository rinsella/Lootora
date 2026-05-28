<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = trim((string) $request->get('q', ''));

        $query = Withdrawal::with('user')
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($status !== 'all' && $status !== '') {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $userIds = User::where('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->pluck('id');
                $q->whereIn('user_id', $userIds)
                  ->orWhere('account', 'like', "%$search%")
                  ->orWhere('method', 'like', "%$search%");
            });
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        $counts = [
            'all'      => Withdrawal::count(),
            'pending'  => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'paid'     => Withdrawal::where('status', 'paid')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
        ];

        return view('admin.withdrawals-modern', compact('withdrawals', 'status', 'search', 'counts'));
    }

    public function approve(Request $request, $id)
    {
        $note = (string) $request->input('note', '');
        DB::transaction(function () use ($id, $note) {
            $w = Withdrawal::lockForUpdate()->findOrFail($id);
            if ($w->status !== 'pending') {
                abort(422, 'Only pending withdrawals can be approved.');
            }
            $w->status = 'approved';
            if (\Schema::hasColumn('withdrawals', 'processed_at')) $w->processed_at = now();
            if (\Schema::hasColumn('withdrawals', 'admin_note') && $note !== '') $w->admin_note = $note;
            $w->save();

            $this->notify($w->user_id, 'Withdrawal approved', "Your withdrawal of {$w->amount} \$LOOT via {$w->method} has been approved.");
            $this->log('approve_withdrawal', $w->user_id, ['id' => $w->id, 'amount' => (float) $w->amount]);
        });

        return back()->with('success', 'Withdrawal approved.');
    }

    public function markPaid(Request $request, $id)
    {
        $note = (string) $request->input('note', '');
        DB::transaction(function () use ($id, $note) {
            $w = Withdrawal::lockForUpdate()->findOrFail($id);
            if (!in_array($w->status, ['approved','pending'], true)) {
                abort(422, 'Only pending/approved withdrawals can be marked paid.');
            }
            $w->status = 'paid';
            if (\Schema::hasColumn('withdrawals', 'paid_at')) $w->paid_at = now();
            if (\Schema::hasColumn('withdrawals', 'processed_at') && !$w->processed_at) $w->processed_at = now();
            if (\Schema::hasColumn('withdrawals', 'admin_note') && $note !== '') $w->admin_note = $note;
            $w->save();

            $this->notify($w->user_id, 'Withdrawal paid', "Your withdrawal of {$w->amount} \$LOOT has been paid out via {$w->method}.");
            $this->log('mark_paid_withdrawal', $w->user_id, ['id' => $w->id, 'amount' => (float) $w->amount]);
        });

        return back()->with('success', 'Withdrawal marked as paid.');
    }

    public function reject(Request $request, $id)
    {
        $note = trim((string) $request->input('note', ''));
        DB::transaction(function () use ($id, $note) {
            $w = Withdrawal::lockForUpdate()->findOrFail($id);
            if (!in_array($w->status, ['pending','approved'], true)) {
                abort(422, 'Only pending or approved withdrawals can be rejected.');
            }
            // refund: amount is in LOOT points
            $user = User::lockForUpdate()->find($w->user_id);
            if ($user) {
                $user->current_points = (float) $user->current_points + (float) $w->amount;
                $user->save();
            }

            $w->status = 'rejected';
            if (\Schema::hasColumn('withdrawals', 'processed_at')) $w->processed_at = now();
            if (\Schema::hasColumn('withdrawals', 'admin_note')) $w->admin_note = $note ?: 'Rejected by admin';
            $w->save();

            $this->notify($w->user_id, 'Withdrawal rejected', "Your withdrawal of {$w->amount} \$LOOT via {$w->method} was rejected and points refunded.".($note ? " Reason: $note" : ''));
            $this->log('reject_withdrawal', $w->user_id, ['id' => $w->id, 'amount' => (float) $w->amount, 'reason' => $note]);
        });

        return back()->with('success', 'Withdrawal rejected and points refunded.');
    }

    private function notify(int $userId, string $title, string $message): void
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'title'   => $title,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {}
    }

    private function log(string $action, ?int $targetUserId, array $meta = []): void
    {
        try {
            AdminActionLog::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $targetUserId,
                'action' => $action,
                'reason' => $meta['reason'] ?? null,
                'metadata' => $meta,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {}
    }
}
