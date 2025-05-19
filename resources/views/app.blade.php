<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600&display=swap" rel="stylesheet">
        <!-- Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://unpkg.com/vue-tel-input@6/dist/vue-tel-input.css">
        

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-poppins antialiased">
        @inertia
    </body>

    <style>
    #app {
        text-align: left;
        padding: 0;
        max-width: none;
        min-height: 100vh;
        width: 100%;
    }
    body {
        font-family: 'Poppins', sans-serif;
    }
    </style>
</html>