<x-exam-layout title="Hasil Ujian Sedang Diproses">
    <div class="min-h-screen flex flex-col bg-slate-50">
        <!-- Topbar Sesuai Mockup [Screen Proses Hasil Ujian] -->
        <header class="h-16 bg-[#1e385b] text-white px-6 flex items-center justify-between flex-shrink-0 z-40 shadow-xs">
            <!-- Brand Sisi Kiri -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white border border-white/15 shadow-2xs">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="2" y1="8" x2="22" y2="8" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
                <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">
                    Sistem Manajemen Ujian
                </h1>
            </div>

            <!-- Profile & Tombol Keluar Sisi Kanan -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5">
                    @php
                        $userInitials = collect(explode(' ', Auth::user()->name))
                            ->filter()
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->join('');
                    @endphp
                    <div class="w-8 h-8 rounded-full bg-white text-[#1e385b] font-bold text-xs flex items-center justify-center shadow-2xs">
                        {{ $userInitials ?: 'BS' }}
                    </div>
                    <span class="hidden sm:inline text-xs sm:text-sm font-medium text-white">
                        {{ Auth::user()->name }} (Peserta Ujian)
                    </span>
                </div>

                <span class="text-white/40 hidden sm:inline">|</span>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="text-xs sm:text-sm font-medium text-white hover:text-white/80 transition cursor-pointer underline-offset-4 hover:underline">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content Area: Centered Card -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
            <div class="max-w-xl w-full bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6 sm:p-9">
                <!-- Judul Utama -->
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 text-center mb-6 tracking-tight">
                    Hasil Ujian Sedang Diproses
                </h2>

                <!-- Tabel Detail Ujian -->
                <div class="bg-slate-50/80 rounded-xl border border-slate-200/90 overflow-hidden divide-y divide-slate-200/80">
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Nama Ujian</span>
                        <span class="font-bold text-slate-900 text-right">{{ $jadwal->nama_ujian }}</span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Tanggal</span>
                        <span class="font-bold text-slate-900">
                            {{ $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Waktu</span>
                        <span class="font-bold text-slate-900">
                            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA
                        </span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Durasi</span>
                        <span class="font-bold text-slate-900">{{ $jadwal->durasi }} Menit</span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Jumlah Soal</span>
                        <span class="font-bold text-slate-900">{{ $totalSoal }} Soal</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-slate-200 my-6"></div>

                <!-- Banner Peringatan / Tunggu Sesuai Mockup -->
                <div class="bg-[#fef9c3] border border-amber-300 rounded-lg py-3 px-4 flex items-center justify-center gap-2 text-amber-900 font-bold text-xs sm:text-sm shadow-2xs">
                    <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Silahkan Tunggu Beberapa Saat</span>
                </div>
            </div>
        </main>
    </div>

    <!-- Auto-redirect ke halaman detail hasil setelah beberapa saat -->
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('peserta.hasil.show', $jadwal->id) }}";
        }, 5000);
    </script>
</x-exam-layout>
