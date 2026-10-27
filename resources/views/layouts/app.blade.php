<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem Manajemen Ujian Real-Time' }} — SIMURT</title>

    <!-- Favicon / Tab Bar Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">

    <!-- Scripts & Styles (Vite Lokal Offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $shouldHideSidebar = ($hideSidebar ?? false) || request()->routeIs('peserta.jadwal.konfirmasi');
@endphp
<body class="bg-white text-slate-800 antialiased font-sans h-screen overflow-hidden">
    <div x-data="{ sidebarOpen: false }" class="h-screen flex flex-col overflow-hidden bg-white">
        <!-- Top Navbar (Full Width Dark Navy #1e385b) - Fixed at the very top, NEVER scrolls -->
        <header class="h-14 sm:h-16 bg-[#1e385b] text-white px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-30 shadow-sm">
            <!-- Left: Logo & Title -->
            <div class="flex items-center gap-3">
                @if(!$shouldHideSidebar)
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="lg:hidden p-1.5 rounded text-white/80 hover:text-white hover:bg-white/10 transition"
                            aria-label="Toggle Navigation">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif

                <!-- Icon Box & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 shadow-sm border border-white/20">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SIMURT" class="w-full h-full object-cover" />
                    </div>
                    <span class="text-white font-semibold text-base sm:text-lg tracking-tight">
                        Sistem Manajemen Ujian
                    </span>
                </div>
            </div>

            <!-- Right: User Avatar & Logout Link -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5">
                    <!-- Solid White Avatar Circle -->
                    <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center text-[#1e385b] font-bold text-xs flex-shrink-0 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-white text-xs sm:text-sm font-medium hidden xs:inline whitespace-nowrap">
                        {{ Auth::user()->name }} ({{ Auth::user()->isPanitia() ? 'Panitia Ujian' : (Auth::user()->isPengawas() ? 'Pengawas Ujian' : (Auth::user()->isAdmin() ? 'Administrator' : 'Peserta')) }})
                    </span>
                </div>

                <!-- Link Keluar -->
                <span class="text-white/40 hidden sm:inline">|</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="text-white/90 hover:text-white text-xs sm:text-sm font-normal underline-offset-4 hover:underline transition">
                        Keluar
                    </button>
                </form>
            </div>
        </header>        <!-- Body Layout (Sidebar + Scrollable Main Content) -->
        <div class="flex-1 flex flex-row overflow-hidden min-h-0">
            @if(!$shouldHideSidebar)
                <!-- Sidebar Mobile Backdrop -->
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition-opacity ease-linear duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="transition-opacity ease-linear duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="sidebarOpen = false" 
                     class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden" 
                     style="display: none;"></div>

                <!-- Sidebar Navigation (White Background, Pinned to Left, NEVER scrolls into header) -->
                <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                       class="fixed inset-y-0 left-0 z-50 w-60 sm:w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:static flex flex-col shadow-xl lg:shadow-none flex-shrink-0 h-full overflow-y-auto">
                    <!-- Mobile Header in Sidebar -->
                    <div class="h-14 flex items-center justify-between px-4 border-b border-slate-100 lg:hidden">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Menu Navigasi</span>
                        <button @click="sidebarOpen = false" class="p-1 rounded text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links with SVG Icons (NO EMOJIS) -->
                    <nav class="flex-1 px-3 sm:px-4 py-5 space-y-1.5 overflow-y-auto">
                        @if(Auth::user()->isAdmin())
                            <!-- 1. Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ease-out group {{ request()->routeIs('admin.dashboard') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:translate-x-1 active:translate-x-0.5' }}">
                                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- 2. Kelola Pengguna -->
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ease-out group {{ request()->routeIs('admin.users.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:translate-x-1 active:translate-x-0.5' }}">
                                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Kelola Pengguna</span>
                            </a>

                            <!-- 3. Hak Akses -->
                            <a href="{{ route('admin.hak-akses.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ease-out group {{ request()->routeIs('admin.hak-akses.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:translate-x-1 active:translate-x-0.5' }}">
                                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ request()->routeIs('admin.hak-akses.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <span>Hak Akses</span>
                            </a>

                            <!-- 4. Log Aktivitas -->
                            <a href="{{ route('admin.aktivitas.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ease-out group {{ request()->routeIs('admin.aktivitas.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:translate-x-1 active:translate-x-0.5' }}">
                                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ request()->routeIs('admin.aktivitas.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4l3-7 4 14 3-7h4" />
                                </svg>
                                <span>Log Aktivitas</span>
                            </a>

                            <!-- 5. Laporan Sistem -->
                            <a href="{{ route('admin.laporan.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ease-out group {{ request()->routeIs('admin.laporan.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:translate-x-1 active:translate-x-0.5' }}">
                                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ request()->routeIs('admin.laporan.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3m-16 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7m-16 5c0 1.657 3.582 3 8 3s8-1.343 8-3" />
                                </svg>
                                <span>Laporan Sistem</span>
                            </a>

                        @elseif(Auth::user()->isPanitia())
                            <a href="{{ route('panitia.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.dashboard') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.dashboard') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('panitia.peserta.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.peserta.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.peserta.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                <span>Data Peserta</span>
                            </a>

                            <a href="{{ route('panitia.pengawas.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.pengawas.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.pengawas.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Data Pengawas</span>
                            </a>

                            <a href="{{ route('panitia.paket.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.paket.*') || request()->routeIs('panitia.soal.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.paket.*') || request()->routeIs('panitia.soal.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Bank Soal</span>
                            </a>

                            <a href="{{ route('panitia.jadwal.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.jadwal.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.jadwal.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal Ujian</span>
                            </a>

                            <a href="{{ route('panitia.nilai.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.nilai.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.nilai.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>Hasil Ujian</span>
                            </a>

                            <a href="{{ route('panitia.laporan.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('panitia.laporan.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('panitia.laporan.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>Laporan</span>
                            </a>

                        @elseif(Auth::user()->isPengawas())
                            <a href="{{ route('pengawas.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pengawas.dashboard') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengawas.dashboard') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('pengawas.monitoring.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pengawas.monitoring.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengawas.monitoring.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round" />
                                    <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round" />
                                </svg>
                                <span>Monitoring Ujian</span>
                            </a>

                            <a href="{{ route('pengawas.log.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pengawas.log.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengawas.log.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Log Aktivitas</span>
                            </a>

                        @elseif(Auth::user()->isPeserta())
                            <a href="{{ route('peserta.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('peserta.dashboard') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('peserta.dashboard') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('peserta.jadwal.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('peserta.jadwal.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('peserta.jadwal.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal Ujian</span>
                            </a>

                            <a href="{{ route('peserta.hasil.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('peserta.hasil.*') ? 'bg-[#1e385b] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('peserta.hasil.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Hasil Ujian</span>
                            </a>
                        @endif
                    </nav>
                </aside>
            @endif

            <!-- Main Content Area: ONLY THIS SCROLLS! -->
            <div class="flex-1 flex flex-col min-w-0 bg-white overflow-y-auto min-h-0">
                <!-- Alerts / Flash Messages with SVG Icons (NO EMOJIS) -->
                @if(session('success'))
                    <div class="mx-6 sm:mx-8 lg:mx-10 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-sm flex-shrink-0 animate-page-enter">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-6 sm:mx-8 lg:mx-10 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-sm flex-shrink-0 animate-page-enter">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mx-6 sm:mx-8 lg:mx-10 mt-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-sm flex-shrink-0 animate-page-enter">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Page Main Content Slot -->
                <main class="flex-1 p-6 sm:p-8 lg:p-10 animate-page-enter">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <!-- Global Delete Confirmation Modal (SIMURT Design System) -->
    <div x-data="globalDeleteModal()" 
         x-cloak
         @open-delete-modal.window="openModal($event.detail)"
         @keydown.escape.window="if (isOpen && !isDeleting) close()"
         class="relative z-50">
        <!-- Backdrop Overlay -->
        <div x-show="isOpen"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs"
             @click="if (!isDeleting) close()"
             style="display: none;"
             aria-hidden="true"></div>

        <!-- Modal Box Container -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none"
             style="display: none;"
             role="dialog"
             aria-modal="true"
             :aria-label="title">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 pointer-events-auto relative transform transition-all"
                 @click.stop>
                <!-- Close Button (X) -->
                <button type="button" 
                        @click="close()" 
                        :disabled="isDeleting"
                        class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer disabled:opacity-50"
                        aria-label="Tutup dialog konfirmasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Warning Icon -->
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shadow-xs mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <!-- Content -->
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" x-text="title">
                        Konfirmasi Hapus Data
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed" x-text="message">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>

                    <!-- Warning Alert Callout -->
                    <div class="mt-4 p-3 rounded-lg bg-rose-50/80 border border-rose-200 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-xs text-rose-700 font-medium leading-relaxed">
                            Tindakan ini tidak dapat dibatalkan. Seluruh data terkait akan dihapus secara permanen dari sistem.
                        </span>
                    </div>
                </div>

                <!-- Actions Buttons -->
                <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" 
                            @click="close()" 
                            :disabled="isDeleting"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-medium transition tactile-btn cursor-pointer disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" 
                            @click="confirm()" 
                            :disabled="isDeleting"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn cursor-pointer disabled:opacity-50">
                        <svg x-show="!isDeleting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <svg x-show="isDeleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isDeleting ? 'Menghapus...' : confirmButtonText">Ya, Hapus Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function globalDeleteModal() {
            return {
                isOpen: false,
                isDeleting: false,
                title: 'Konfirmasi Hapus Data',
                message: 'Apakah Anda yakin ingin menghapus data ini?',
                confirmButtonText: 'Ya, Hapus Data',
                targetForm: null,
                onConfirmCallback: null,

                openModal(detail) {
                    this.title = detail.title || 'Konfirmasi Hapus Data';
                    this.message = detail.message || 'Apakah Anda yakin ingin menghapus data ini?';
                    this.confirmButtonText = detail.confirmButtonText || 'Ya, Hapus Data';
                    this.targetForm = detail.form || null;
                    this.onConfirmCallback = detail.onConfirm || null;
                    this.isDeleting = false;
                    this.isOpen = true;
                },

                close() {
                    if (this.isDeleting) return;
                    this.isOpen = false;
                    this.targetForm = null;
                    this.onConfirmCallback = null;
                },

                confirm() {
                    if (this.isDeleting) return;
                    this.isDeleting = true;

                    if (this.onConfirmCallback && typeof this.onConfirmCallback === 'function') {
                        const cb = this.onConfirmCallback;
                        this.close();
                        cb();
                        return;
                    }

                    if (this.targetForm) {
                        const form = this.targetForm;
                        form.dataset.confirmed = 'true';
                        HTMLFormElement.prototype.submit.call(form);
                    }
                }
            };
        }

        window.simurtConfirm = function(options) {
            window.dispatchEvent(new CustomEvent('open-delete-modal', {
                detail: {
                    title: options.title || 'Konfirmasi Tindakan',
                    message: options.message || 'Apakah Anda yakin ingin melanjutkan?',
                    confirmButtonText: options.confirmButtonText || 'Ya, Lanjutkan',
                    onConfirm: options.onConfirm
                }
            }));
        };

        // Capture delete form submissions across the application
        (function () {
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.dataset.confirmed === 'true') return;

                const methodInput = form.querySelector('input[name="_method"]');
                const isDeleteMethod = methodInput && methodInput.value.toUpperCase() === 'DELETE';
                const hasConfirmAttr = form.hasAttribute('data-confirm-delete');
                const onsubmitAttr = form.getAttribute('onsubmit') || '';
                const hasConfirmInOnsubmit = onsubmitAttr.includes('confirm(');

                if (isDeleteMethod || hasConfirmAttr || hasConfirmInOnsubmit) {
                    // Ignore forms with their own password modals (e.g., Breeze profile delete)
                    if (form.closest('[name="confirm-user-deletion"]') || 
                        form.closest('#confirm-user-deletion') || 
                        form.hasAttribute('data-no-global-confirm') ||
                        form.querySelector('input[name="password"]')) {
                        return;
                    }

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    let title = form.getAttribute('data-confirm-title') || 'Konfirmasi Hapus Data';
                    let message = form.getAttribute('data-confirm-message');

                    if (!message && hasConfirmInOnsubmit) {
                        const match = onsubmitAttr.match(/confirm\s*\(\s*(['"`])(.*?)\1\s*\)/s);
                        if (match && match[2]) {
                            message = match[2];
                        }
                    }

                    if (!message) {
                        message = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan data yang dihapus tidak dapat dipulihkan.';
                    }

                    window.dispatchEvent(new CustomEvent('open-delete-modal', {
                        detail: {
                            form: form,
                            title: title,
                            message: message
                        }
                    }));
                }
            }, true);
        })();
    </script>
</body>
</html>
