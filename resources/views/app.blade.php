<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" > 
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'QSMCore') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- FullCalendar CSS -->
        <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.20/main.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.20/main.min.css" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased dark:bg-gray-900">
        @inertia
    </body>
</html>
