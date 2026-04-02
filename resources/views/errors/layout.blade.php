<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'QSMCore') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            navy: '#0B2B45',
                            blue: '#1F5E88',
                            sky: '#2F8BC9',
                            bg: '#F6F8FB',
                            border: '#E5EAF0',
                            text: '#0F172A',
                            muted: '#475569',
                        },
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-b from-brand-bg to-slate-100 text-brand-text antialiased font-sans">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">
            <div class="bg-white/90 backdrop-blur border border-brand-border rounded-2xl shadow-lg shadow-slate-200/60 px-8 py-10 text-center">
                <img src="/logos/lo.png" alt="{{ config('app.name') }}" class="h-11 w-auto mx-auto mb-6" width="120" height="44">
                @yield('content')
            </div>
            <p class="mt-6 text-center text-sm text-brand-muted">
                {{ config('app.name', 'QSMCore') }} — Quality &amp; Safety Management
            </p>
        </div>
    </div>
</body>
</html>
