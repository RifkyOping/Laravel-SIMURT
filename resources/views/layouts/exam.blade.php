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

    <style>
        /* Anti-seleksi teks, drag, dan long-press menu untuk semua browser (Desktop, Android, iOS) */
        *, *::before, *::after {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important; /* Nonaktifkan popup menu saat tekan lama di HP Android / iOS */
        }
        input, textarea {
            -webkit-user-select: text !important;
            user-select: text !important;
        }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased font-sans select-none min-h-screen flex flex-col"
      oncontextmenu="return false;"
      ondragstart="return false;">
    {{ $slot }}
</body>
</html>
