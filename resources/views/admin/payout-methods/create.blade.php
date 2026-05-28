@extends('layouts.admin-modern')
@section('title', 'New Payout Method')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Add payout method</h1>
        <p class="text-sm text-loot-muted">Create a new way for users to cash out their $LOOT.</p>
    </div>
    <a href="{{ route('admin.payout-methods') }}" class="text-xs font-bold text-loot-muted hover:text-loot-ink">← Back to list</a>
</div>
@include('admin.payout-methods._form')
@endsection
