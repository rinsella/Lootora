@extends('layouts.admin-modern')
@section('title', 'Edit · '.$provider->name)

@section('content')
<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Edit provider</h1>
        <p class="text-sm text-loot-muted">{{ $provider->name }}</p>
    </div>
    <a href="{{ route('admin.offerwalls') }}" class="text-xs font-bold text-loot-muted hover:text-loot-ink">← Back to list</a>
</div>
@include('admin.offerwalls._form')
@endsection
