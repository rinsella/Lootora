@php
    use App\Support\Lootora;
    $u = auth()->user();
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Dashboard') · Lootora</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('app-assets/images/ico/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          loot: {
            primary: '#16A34A', primaryDark: '#15803D',
            accent: '#F59E0B', accentDark: '#D97706',
            bg: '#F8FAFC', card: '#FFFFFF',
            border: '#E5E7EB', ink: '#111827', muted: '#6B7280',
          }
        },
        fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
        boxShadow: {
          soft:   '0 1px 2px rgba(17,24,39,.04), 0 1px 3px rgba(17,24,39,.06)',
          cardLg: '0 10px 25px -5px rgba(22,163,74,.18), 0 8px 10px -6px rgba(22,163,74,.10)',
        }
      }
    }
  }
</script>
<style>
  body{font-family:'Inter',ui-sans-serif,system-ui,sans-serif;background:#F8FAFC;color:#111827;-webkit-font-smoothing:antialiased}
  .gradient-loot{background:linear-gradient(135deg,#16A34A 0%,#15803D 100%)}
  .gradient-gold{background:linear-gradient(135deg,#FBBF24 0%,#F59E0B 100%)}
  .gradient-hero{background:linear-gradient(135deg,#0F172A 0%,#15803D 65%, #16A34A 100%)}
  .scroll-hide::-webkit-scrollbar{display:none}
  .scroll-hide{scrollbar-width:none}
  [x-cloak]{display:none!important}
</style>
@stack('head')
</head>
<body class="min-h-screen pb-24 lg:pb-0">

<div class="lg:flex">
    @include('partials.sidebar')

    <div class="flex-1 lg:ml-64">
        @include('partials.app-header')

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
                    <span class="mt-0.5">✓</span><span class="flex-1">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-start gap-3">
                    <span class="mt-0.5">⚠</span><span class="flex-1">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="hidden lg:block border-t border-loot-border bg-white">
            <div class="max-w-6xl mx-auto px-8 py-6 flex items-center justify-between text-xs text-loot-muted">
                <p>© {{ date('Y') }} Lootora · Complete missions. Earn rewards. Unlock your loot.</p>
                <div class="flex gap-4">
                    <a href="{{ route('faq') }}" class="hover:text-loot-ink">FAQ</a>
                    <a href="{{ route('privacy-policy') }}" class="hover:text-loot-ink">Privacy</a>
                    <a href="{{ route('term-condition') }}" class="hover:text-loot-ink">Terms</a>
                </div>
            </div>
        </footer>
    </div>
</div>

@include('partials.bottom-nav')

@stack('scripts')
</body>
</html>
