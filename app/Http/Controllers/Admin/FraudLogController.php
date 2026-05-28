<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FraudLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('fraud_logs')) {
            return view('admin.fraud-logs.index', ['logs' => null, 'tableExists' => false, 'filters' => [], 'types' => []]);
        }

        $filters = [
            'type'   => trim((string) $request->get('type', '')),
            'user'   => trim((string) $request->get('user', '')),
        ];

        $q = FraudLog::query()->orderByDesc('created_at');
        if ($filters['type'] !== '') $q->where('type', $filters['type']);
        if ($filters['user'] !== '') {
            $ids = User::where('username','like',"%{$filters['user']}%")
                ->orWhere('email','like',"%{$filters['user']}%")
                ->pluck('id');
            $q->whereIn('user_id', $ids);
        }

        $logs = $q->paginate(25)->withQueryString();
        $users = User::whereIn('id', $logs->pluck('user_id')->filter())->get()->keyBy('id');
        $types = FraudLog::select('type')->distinct()->pluck('type')->filter()->values();

        return view('admin.fraud-logs.index', [
            'logs' => $logs,
            'tableExists' => true,
            'filters' => $filters,
            'users' => $users,
            'types' => $types,
        ]);
    }
}
