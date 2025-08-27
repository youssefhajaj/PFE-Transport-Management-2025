<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Hanyjay</title>

<meta property="og:title" content="Hanyjay">
<meta property="og:description" content="Welcome to Hanyjay Application">
<meta property="og:image" content="{{ asset('icons/KH_logo_512x512.png') }}">
<meta property="og:url" content="{{ url('/') }}">
<meta name="twitter:card" content="summary_large_image">


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" href="{{ asset('KH_logo.png') }}" type="image/png">
	<link rel="icon" href="{{ asset('icons/KH_logo_512x512.png') }}" sizes="512x512">

<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0d6efd">
<link rel="apple-touch-icon" href="{{ asset('icons/KH_logo_192x192.png') }}" sizes="192x192">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
