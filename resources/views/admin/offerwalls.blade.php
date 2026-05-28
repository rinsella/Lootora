@extends('layouts.admin-modern')

@section('title', "Offerwalls")

@push('css')
    @livewireStyles
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @livewire('offerwalls')
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    @livewireScripts
@endpush
