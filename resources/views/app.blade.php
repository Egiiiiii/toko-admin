<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Toko Admin') }}</title>

        
        <!-- Script Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Helper Inertia -->
        @inertiaHead
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        @inertia
    </body>
</html>