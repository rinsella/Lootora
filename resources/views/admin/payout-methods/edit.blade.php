@extends('layouts.admin-modern')
@section('title', 'Edit · '.$method->name)

@section('content')
<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Edit payout method</h1>
        <p class="text-sm text-loot-muted">{{ $method->name }}</p>
    </div>
    <a href="{{ route('admin.payout-methods') }}" class="text-xs font-bold text-loot-muted hover:text-loot-ink">← Back to list</a>
</div>
@include('admin.payout-methods._form')
@endsection
