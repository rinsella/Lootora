<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostbackLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PostbackLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('postback_logs')) {
            return view('admin.postback-logs.index', [
                'logs' => null,
                'tableExists' => false,
                'filters' => [],
                'providers' => [],
            ]);
        }

        $filters = [
            'provider'        => trim((string) $request->get('provider', '')),
            'status'          => trim((string) $request->get('status', '')),
            'date'            => trim((string) $request->get('date', '')),
            'transaction_id'  => trim((string) $request->get('transaction_id', '')),
        ];

        $q = PostbackLog::query()->orderByDesc('created_at');
        if ($filters['provider'] !== '')       $q->where('provider', $filters['provider']);
        if ($filters['status'] !== '')         $q->where('status', $filters['status']);
        if ($filters['date'] !== '')           $q->whereDate('created_at', $filters['date']);
        if ($filters['transaction_id'] !== '') $q->where('transaction_id', 'like', '%'.$filters['transaction_id'].'%');

        $logs = $q->paginate(25)->withQueryString();
        $providers = PostbackLog::select('provider')->distinct()->pluck('provider')->filter()->values();

        return view('admin.postback-logs.index', [
            'logs' => $logs,
            'tableExists' => true,
            'filters' => $filters,
            'providers' => $providers,
        ]);
    }
}
