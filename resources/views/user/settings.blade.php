@extends('layouts.member')

@section('title', 'Settings')

@section('content')
<div class="rounded-2xl gradient-loot p-6 sm:p-7 text-white shadow-cardLg mb-6">
    <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Account</p>
    <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Profile settings</h1>
    <p class="text-sm text-emerald-100 mt-1">Update your avatar, email, or password.</p>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">
        ✓ {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('user.settings.update') }}" enctype="multipart/form-data"
      class="rounded-2xl bg-white border border-loot-border p-5 sm:p-6 shadow-soft">
    @csrf

    <div class="flex items-center gap-5 pb-5 border-b border-loot-border">
        <img id="avatar-preview" src="{{ auth()->user()->avatar() }}" alt="" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-loot-border">
        <div>
            <label for="account-upload" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl gradient-loot text-white text-sm font-semibold cursor-pointer hover:opacity-90">
                Upload new photo
            </label>
            <input id="account-upload" type="file" name="image" accept="image/*" hidden>
            <button type="button" id="avatar-reset" class="ml-2 inline-flex items-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-sm font-semibold text-loot-ink">Reset</button>
            <p class="text-xs text-loot-muted mt-2">PNG, JPG or JPEG. Max ~2 MB.</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mt-5">
        <div>
            <label for="email" class="block text-xs font-semibold text-loot-ink mb-1.5">Email</label>
            <input id="email" type="email" disabled value="{{ auth()->user()->email }}"
                   class="w-full rounded-xl border border-loot-border bg-gray-50 px-3.5 py-2.5 text-sm text-loot-muted">
        </div>
        <div>
            <label for="password" class="block text-xs font-semibold text-loot-ink mb-1.5">New password</label>
            <input id="password" type="password" name="password" placeholder="Leave blank to keep current"
                   class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl gradient-loot text-white text-sm font-bold shadow-cardLg hover:opacity-90">Save changes</button>
        <button type="reset" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-sm font-semibold text-loot-ink">Discard</button>
    </div>
</form>

@push('scripts')
<script>
    const input = document.getElementById('account-upload');
    const preview = document.getElementById('avatar-preview');
    const reset = document.getElementById('avatar-reset');
    const original = preview.src;
    input?.addEventListener('change', e => {
        const f = e.target.files[0]; if(!f) return;
        const r = new FileReader();
        r.onload = ev => preview.src = ev.target.result;
        r.readAsDataURL(f);
    });
    reset?.addEventListener('click', () => { input.value=''; preview.src = original; });
</script>
@endpush
@endsection
