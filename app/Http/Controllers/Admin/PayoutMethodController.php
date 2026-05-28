<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayoutMethodController extends Controller
{
    public function index()
    {
        $methods = Payment::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.payout-methods.index', compact('methods'));
    }

    public function create()
    {
        $method = new Payment([
            'currency' => 'USD',
            'is_active' => true,
            'fee_percentage' => 0,
            'fixed_fee' => 0,
            'sort_order' => 0,
        ]);
        return view('admin.payout-methods.create', compact('method'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['photo_path'] = $this->handleLogo($request, null);
        $method = Payment::create($data);
        $this->log('create_payout_method', $method->id, ['name' => $method->name]);
        return redirect()->route('admin.payout-methods')->with('success', "Payout method '{$method->name}' created.");
    }

    public function edit($id)
    {
        $method = Payment::findOrFail($id);
        return view('admin.payout-methods.edit', compact('method'));
    }

    public function update(Request $request, $id)
    {
        $method = Payment::findOrFail($id);
        $data = $this->validateData($request);

        $newPath = $this->handleLogo($request, $method->photo_path);
        if ($newPath !== null) $data['photo_path'] = $newPath;

        $method->update($data);
        $this->log('update_payout_method', $method->id, ['name' => $method->name]);
        return redirect()->route('admin.payout-methods')->with('success', "Payout method '{$method->name}' updated.");
    }

    public function destroy($id)
    {
        $method = Payment::findOrFail($id);
        $name = $method->name;
        if ($method->photo_path && Storage::disk('public')->exists($method->photo_path)) {
            Storage::disk('public')->delete($method->photo_path);
        }
        $method->delete();
        $this->log('delete_payout_method', $id, ['name' => $name]);
        return redirect()->route('admin.payout-methods')->with('success', "Payout method '{$name}' deleted.");
    }

    public function toggle($id)
    {
        $method = Payment::findOrFail($id);
        $method->is_active = !$method->is_active;
        $method->save();
        $this->log('toggle_payout_method', $method->id, ['active' => $method->is_active]);
        return back()->with('success', "Payout method '{$method->name}' is now ".($method->is_active ? 'active' : 'inactive').'.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:8',
            'description' => 'nullable|string|max:1000',
            'min_withdrawal' => 'nullable|numeric|min:0',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_fee' => 'nullable|numeric|min:0',
            'account_label' => 'nullable|string|max:128',
            'instructions' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);
    }

    private function handleLogo(Request $request, ?string $existingPath): ?string
    {
        if (!$request->hasFile('logo')) return null;
        $file = $request->file('logo');
        if (!$file || !$file->isValid()) return null;
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }
        return $file->store('payments', 'public');
    }

    private function log(string $action, ?int $targetId, array $meta = []): void
    {
        try {
            AdminActionLog::create([
                'admin_id' => auth()->id(),
                'target_user_id' => null,
                'action' => $action,
                'reason' => null,
                'metadata' => array_merge($meta, ['payout_method_id' => $targetId]),
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {}
    }
}
