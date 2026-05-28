<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Account banned · Lootora</title>
<link rel="icon" href="{{ asset('app-assets/images/ico/favicon.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>body{font-family:'Inter',ui-sans-serif,system-ui,sans-serif;background:#F8FAFC;color:#111827}.gradient-loot{background:linear-gradient(135deg,#16A34A 0%,#15803D 100%)}</style>
</head>
<body class="min-h-screen grid place-items-center px-6">
<div class="max-w-lg w-full text-center">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-8">
        <div class="w-11 h-11 rounded-xl gradient-loot grid place-items-center text-white font-extrabold text-lg">L</div>
        <span class="font-extrabold text-lg">Lootora</span>
    </a>

    <div class="rounded-3xl bg-white border border-gray-200 p-8 sm:p-10 shadow-xl">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-rose-50 text-rose-600 grid place-items-center text-4xl mb-5">🚫</div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Your account is banned</h1>
        <p class="mt-3 text-gray-600 text-sm">We've suspended this account due to a violation of our terms of service or for suspicious activity. If you believe this is a mistake, please contact support.</p>

        <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
            <a href="mailto:support@lootora.net" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl gradient-loot text-white font-bold shadow-lg">Contact support</a>
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 font-semibold text-gray-900">Sign out</button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-xs text-gray-500">© {{ date('Y') }} Lootora · support@lootora.net</p>
</div>
</body>
</html>
