<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ruang Ujian' }} — SIMURT</title>

    <!-- Favicon / Tab Bar Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">

    <!-- Scripts & Styles (Vite Lokal Offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-800 antialiased font-sans select-none min-h-screen flex flex-col">
    {{ $slot }}
</body>
</html>
