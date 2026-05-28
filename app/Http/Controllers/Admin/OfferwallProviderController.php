<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Offerwall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferwallProviderController extends Controller
{
    public function index()
    {
        $providers = Offerwall::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.offerwalls.index', compact('providers'));
    }

    public function create()
    {
        $provider = new Offerwall(['is_active' => true, 'revenue_share_percentage' => 70.00, 'sort_order' => 0]);
        return view('admin.offerwalls.create', compact('provider'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name']);

        $data['photo_path'] = $this->handleLogo($request, null);
        $data['postback_url'] = $this->postbackUrlFor($data['slug']);

        // Backward compat: mirror template into iframe_url if iframe_url empty
        if (empty($data['iframe_url']) && !empty($data['iframe_url_template'])) {
            $data['iframe_url'] = $data['iframe_url_template'];
        }

        $provider = Offerwall::create($data);
        $this->log('create_offerwall', $provider->id, ['name' => $provider->name]);

        return redirect()->route('admin.offerwalls')->with('success', "Provider '{$provider->name}' created.");
    }

    public function edit($id)
    {
        $provider = Offerwall::findOrFail($id);
        return view('admin.offerwalls.edit', compact('provider'));
    }

    public function update(Request $request, $id)
    {
        $provider = Offerwall::findOrFail($id);
        $data = $this->validateData($request, $provider->id);
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name'], $provider->id);

        $newPath = $this->handleLogo($request, $provider->photo_path);
        if ($newPath !== null) $data['photo_path'] = $newPath;

        $data['postback_url'] = $this->postbackUrlFor($data['slug']);

        if (empty($data['iframe_url']) && !empty($data['iframe_url_template'])) {
            $data['iframe_url'] = $data['iframe_url_template'];
        }

        $provider->update($data);
        $this->log('update_offerwall', $provider->id, ['name' => $provider->name]);

        return redirect()->route('admin.offerwalls')->with('success', "Provider '{$provider->name}' updated.");
    }

    public function destroy($id)
    {
        $provider = Offerwall::findOrFail($id);
        $name = $provider->name;
        if ($provider->photo_path && Storage::disk('public')->exists($provider->photo_path)) {
            Storage::disk('public')->delete($provider->photo_path);
        }
        $provider->delete();
        $this->log('delete_offerwall', $id, ['name' => $name]);

        return redirect()->route('admin.offerwalls')->with('success', "Provider '{$name}' deleted.");
    }

    public function toggle($id)
    {
        $provider = Offerwall::findOrFail($id);
        $provider->is_active = !$provider->is_active;
        $provider->save();
        $this->log('toggle_offerwall', $provider->id, ['active' => $provider->is_active]);

        return back()->with('success', "Provider '{$provider->name}' is now ".($provider->is_active ? 'active' : 'inactive').'.');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:64|alpha_dash|unique:offerwalls,slug'.($id ? ",$id" : ''),
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:32',
            'payout_type' => 'nullable|string|max:32',
            'iframe_url_template' => 'nullable|string|max:2000',
            'iframe_url' => 'nullable|string|max:2000',
            'api_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'postback_secret' => 'nullable|string|max:255',
            'ip_whitelist' => 'nullable|string|max:2000',
            'revenue_share_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);
    }

    private function resolveSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $slug = $slug ? Str::slug($slug) : Str::slug($name);
        $base = $slug ?: 'provider';
        $candidate = $base;
        $i = 2;
        while (Offerwall::where('slug', $candidate)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $base.'-'.$i++;
        }
        return $candidate;
    }

    private function handleLogo(Request $request, ?string $existingPath): ?string
    {
        if (!$request->hasFile('logo')) return null;
        $file = $request->file('logo');
        if (!$file || !$file->isValid()) return null;
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }
        return $file->store('offerwalls', 'public');
    }

    private function postbackUrlFor(string $slug): string
    {
        return url('/api/postback/'.$slug);
    }

    private function log(string $action, ?int $targetId, array $meta = []): void
    {
        try {
            AdminActionLog::create([
                'admin_id' => auth()->id(),
                'target_user_id' => null,
                'action' => $action,
                'reason' => null,
                'metadata' => array_merge($meta, ['provider_id' => $targetId]),
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {}
    }
}
