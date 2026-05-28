<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Auth') · Lootora</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('app-assets/images/ico/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
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
  body{font-family:'Inter',ui-sans-serif,system-ui,sans-serif;background:#F8FAFC;color:#111827;-webkit-font-smoothing:antialiased}
  .gradient-loot{background:linear-gradient(135deg,#16A34A 0%,#15803D 100%)}
  .gradient-hero{background:linear-gradient(135deg,#0F172A 0%,#15803D 65%,#16A34A 100%)}
</style>
</head>
<body class="min-h-screen">
<div class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Brand panel --}}
    <div class="hidden lg:flex gradient-hero text-white p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-white/10"></div>
        <div class="absolute -left-10 bottom-10 w-72 h-72 rounded-full bg-white/5"></div>

        <a href="{{ route('home') }}" class="flex items-center gap-3 relative z-10">
            <div class="w-11 h-11 rounded-xl bg-white/15 grid place-items-center font-extrabold text-xl">L</div>
            <div>
                <p class="font-extrabold text-lg leading-none">Lootora</p>
                <p class="text-xs text-emerald-100 mt-1">Earn · Unlock · Loot</p>
            </div>
        </a>

        <div class="relative z-10 max-w-md">
            <p class="text-xs uppercase tracking-[0.2em] text-emerald-200 font-bold">Welcome to Lootora</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight">Complete missions. <br>Earn rewards. <br>Unlock your loot.</h1>
            <p class="mt-4 text-emerald-100">Surveys, games, app trials and offers from top providers — all in one rewarding place.</p>

            <ul class="mt-8 space-y-3 text-sm">
                <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-white/15 grid place-items-center mt-0.5">✓</span><span>Get paid in $LOOT — convert to real money or gift cards.</span></li>
                <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-white/15 grid place-items-center mt-0.5">✓</span><span>Daily check-in streaks for bonus rewards.</span></li>
                <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-white/15 grid place-items-center mt-0.5">✓</span><span>Refer friends, earn a share of everything they do.</span></li>
            </ul>
        </div>

        <p class="relative z-10 text-xs text-emerald-200">© {{ date('Y') }} Lootora · lootora.net</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-10 h-10 rounded-xl gradient-loot grid place-items-center text-white font-extrabold">L</div>
                <span class="font-extrabold text-loot-ink text-lg">Lootora</span>
            </a>

            @yield('form')
        </div>
    </div>
</div>
</body>
</html>
