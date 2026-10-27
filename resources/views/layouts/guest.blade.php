<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem Manajemen Ujian' }} — SIMURT</title>

    <!-- Favicon / Tab Bar Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">

    <!-- Scripts & Styles (Vite Lokal Offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center py-10 px-4 sm:px-6">
        <div class="w-full {{ $maxWidth ?? 'sm:max-w-[420px]' }} bg-white shadow-sm rounded-2xl border border-slate-200 p-6 sm:p-8 card-lift animate-page-enter">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
