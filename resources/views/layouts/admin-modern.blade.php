@php
    use App\Support\Lootora;
    $u = auth()->user();
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Admin') · Lootora Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('app-assets/images/ico/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
{{-- Bootstrap 5 (so existing Livewire/admin views still render correctly) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
{{-- Tailwind chrome --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: { preflight: false },
    theme: { extend: {
      colors: { loot: {
        primary:'#16A34A', primaryDark:'#15803D',
        accent:'#F59E0B', accentDark:'#D97706',
        bg:'#F8FAFC', border:'#E5E7EB', ink:'#111827', muted:'#6B7280'
      }},
      fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
      boxShadow: {
        soft:'0 1px 2px rgba(17,24,39,.04), 0 1px 3px rgba(17,24,39,.06)',
        cardLg:'0 10px 25px -5px rgba(22,163,74,.18), 0 8px 10px -6px rgba(22,163,74,.10)'
      }
    } }
  }
</script>
<style>
  body{font-family:'Inter',ui-sans-serif,system-ui,sans-serif !important;background:#F8FAFC !important;color:#111827 !important;-webkit-font-smoothing:antialiased}
  .gradient-loot{background:linear-gradient(135deg,#16A34A 0%,#15803D 100%)}
  .scroll-hide::-webkit-scrollbar{display:none}
  .scroll-hide{scrollbar-width:none}
  /* Re-style Bootstrap basics to match Lootora */
  .card{border:1px solid #E5E7EB;border-radius:1rem;box-shadow:0 1px 2px rgba(17,24,39,.04);background:#fff}
  .card-header{background:transparent;border-bottom:1px solid #E5E7EB;padding:1rem 1.25rem;border-top-left-radius:1rem;border-top-right-radius:1rem}
  .card-title{font-weight:700;color:#111827;margin:0}
  .btn-success{background:#16A34A;border-color:#16A34A}
  .btn-success:hover{background:#15803D;border-color:#15803D}
  .btn-primary{background:#16A34A;border-color:#16A34A}
  .btn-primary:hover{background:#15803D;border-color:#15803D}
  .badge-light-success{background:#dcfce7;color:#15803D;padding:.35rem .6rem;border-radius:999px;font-weight:600;font-size:.7rem}
  .badge-light-warning{background:#fef3c7;color:#b45309;padding:.35rem .6rem;border-radius:999px;font-weight:600;font-size:.7rem}
  .badge-light-danger{background:#fee2e2;color:#b91c1c;padding:.35rem .6rem;border-radius:999px;font-weight:600;font-size:.7rem}
  .badge-light-primary{background:#dcfce7;color:#15803D;padding:.35rem .6rem;border-radius:999px;font-weight:600;font-size:.7rem}
  .badge-light-info{background:#dbeafe;color:#1d4ed8;padding:.35rem .6rem;border-radius:999px;font-weight:600;font-size:.7rem}
  .table thead th{font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;font-weight:600;border-bottom:1px solid #E5E7EB}
  .table tbody td{vertical-align:middle;border-bottom:1px solid #F3F4F6}
  .form-control,.form-select{border-radius:.75rem;border:1px solid #E5E7EB}
  .form-control:focus,.form-select:focus{border-color:#16A34A;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
  .alert{border-radius:.875rem;border:1px solid transparent}
  .alert-success{background:#dcfce7;color:#14532d;border-color:#bbf7d0}
  .alert-danger{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
  .avatar img{border-radius:.5rem;object-fit:cover}
  .pagination .page-link{color:#16A34A;border-radius:.5rem;margin:0 .15rem;border:1px solid #E5E7EB}
  .pagination .page-item.active .page-link{background:#16A34A;border-color:#16A34A;color:#fff}
</style>
@stack('css')
@stack('head')
</head>
<body class="min-h-screen pb-10">

<div class="lg:flex">
    @include('partials.admin-sidebar')

    <div class="flex-1 lg:ml-64">
        @include('partials.admin-header')

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="mb-5">
                <h1 class="text-xl sm:text-2xl font-extrabold text-loot-ink">@yield('title', 'Admin')</h1>
                <p class="text-sm text-loot-muted">Lootora administrative console</p>
            </div>

            <div id="app">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
