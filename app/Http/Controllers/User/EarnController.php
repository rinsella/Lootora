<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Offerwall;
use Illuminate\Http\Request;

class EarnController extends Controller
{
    public function index(Request $request)
    {
        $query = Offerwall::query();

        $category = trim((string) $request->input('category', ''));
        $sort     = trim((string) $request->input('sort', 'highest')) ?: 'highest';
        $search   = trim((string) $request->input('q', ''));

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort safely (column may not exist on legacy schema → fallback).
        try {
            $query->orderByRaw('COALESCE(sort_order, 0) asc');
        } catch (\Throwable $e) {}

        $offerwalls = $query->orderBy('id', 'desc')->get();

        $categories = ['all', 'surveys', 'games', 'apps', 'signups', 'videos', 'crypto'];

        return view('user.earn', compact('offerwalls', 'categories', 'category', 'sort', 'search'));
    }

    /**
     * Resolve provider URL template placeholders with the authenticated
     * user's details. Falls back to legacy `iframe_url` column.
     */
    public static function resolveUrl(Offerwall $offerwall): string
    {
        $user = auth()->user();
        $tpl  = $offerwall->iframe_url_template ?: ($offerwall->iframe_url ?? '');

        if (!$tpl || !$user) {
            return $tpl ?: '#';
        }

        return strtr($tpl, [
            '{user_id}'  => (string) $user->id,
            '{username}' => urlencode((string) ($user->username ?? '')),
            '{email}'    => urlencode((string) ($user->email    ?? '')),
            '{country}'  => urlencode((string) ($user->country  ?? '')),
            '{ip}'       => (string) request()->ip(),
        ]);
    }
}

