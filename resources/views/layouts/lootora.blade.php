<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16A34A">

    <title>@yield('title', 'Complete missions. Earn rewards.') · {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Lootora — complete missions, earn LOOT Points, and unlock your loot. Surveys, offers, games and daily missions with real rewards.')">

    <link rel="icon" href="{{ asset('app-assets/images/ico/favicon.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    colors: {
                        loot: {
                            primary: '#16A34A',
                            primaryDark: '#15803D',
                            accent: '#F59E0B',
                            accentDark: '#D97706',
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            border: '#E5E7EB',
                            ink: '#111827',
                            muted: '#6B7280',
                        }
                    },
                    boxShadow: {
                        soft: '0 4px 24px -8px rgba(17, 24, 39, 0.10)',
                        cardLg: '0 10px 30px -12px rgba(22, 163, 74, 0.25)',
                    },
                    borderRadius: { '2xl': '1rem' }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background-color: #F8FAFC; color: #111827; }
        .gradient-loot { background-image: linear-gradient(135deg, #16A34A 0%, #15803D 100%); }
        .gradient-gold { background-image: linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%); }
    </style>

    @stack('css')
</head>
<body class="antialiased text-loot-ink bg-loot-bg">

    @yield('body')

    @stack('js')
</body>
</html>
