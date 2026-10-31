<x-exam-layout title="Akses Ruang Ujian Terkunci">
    <div class="min-h-screen flex flex-col bg-slate-50" x-data="izinApp()">
        <!-- Topbar Sesuai SIMURT Design System -->
        <header class="h-16 bg-[#1e385b] text-white px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-40 shadow-xs">
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
                <h1 class="text-sm sm:text-base md:text-lg font-bold text-white tracking-tight leading-tight">
                    Sistem Manajemen Ujian
                </h1>
            </div>

            <!-- Profile & Navigasi Dashboard Kanan -->
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
                        {{ $userInitials ?: 'PS' }}
                    </div>
                    <span class="hidden sm:inline text-xs sm:text-sm font-medium text-white">
                        {{ Auth::user()->name }} (Peserta)
                    </span>
                </div>

                <span class="text-white/40 hidden sm:inline">|</span>

                <a href="{{ route('peserta.dashboard') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-white hover:text-white/80 transition underline-offset-4 hover:underline">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </header>

        <!-- Main Content: Centered Card -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
            <div class="max-w-xl w-full bg-white rounded-2xl shadow-xs border border-slate-200/90 p-6 sm:p-8 space-y-6">
                <!-- Icon & Judul Status -->
                <div class="text-center space-y-3">
                    @if($sesi && $sesi->status === 'diskualifikasi')
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto shadow-2xs">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                Akses Ujian Diblokir (Didiskualifikasi)
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed max-w-md mx-auto">
                                {{ $pesan ?? 'Anda telah didiskualifikasi dari ujian ini karena pelanggaran integritas. Akses ujian telah ditutup permanen.' }}
                            </p>
                        </div>
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mx-auto shadow-2xs">
                            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                Akses Ruang Ujian Terkunci
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed max-w-md mx-auto">
                                {{ $pesan ?? 'Sesi ujian Anda telah terputus atau telah mencapai batas toleransi pelanggaran integritas.' }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Box Detail Ujian & Status Sesi -->
                <div class="bg-slate-50/80 rounded-xl border border-slate-200/90 overflow-hidden divide-y divide-slate-200/80 text-xs sm:text-sm">
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-normal">Nama Ujian</span>
                        <span class="font-bold text-slate-900 text-right">{{ $jadwal->nama_ujian }}</span>
                    </div>

                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-normal">Tanggal & Waktu</span>
                        <span class="font-medium text-slate-800 text-right">
                            {{ $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '-' }} &bull; {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA
                        </span>
                    </div>

                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-normal">Status Sesi Ujian</span>
                        @if($sesi && $sesi->status === 'menunggu_izin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Izin Pengawas
                            </span>
                        @elseif($sesi && $sesi->status === 'diskualifikasi')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Diskualifikasi (Diblokir)
                            </span>
                        @elseif($sesi && in_array($sesi->status, ['terputus']))
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Sesi Terputus
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                Terkunci
                            </span>
                        @endif
                    </div>

                    @if($sesi && $sesi->jumlah_peringatan > 0)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-slate-500 font-normal">Riwayat Peringatan</span>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $sesi->jumlah_peringatan }} Pelanggaran Tercatat
                            </span>
                        </div>
                    @endif
                </div>

                @if(session('info'))
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($sesi && $sesi->status === 'diskualifikasi')
                    <!-- Tampilan Status Didiskualifikasi / Diblokir Permanen -->
                    <div class="p-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 space-y-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0 text-rose-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-rose-950">Akses Ditutup Permanen</h4>
                                <p class="text-[11px] sm:text-xs text-rose-800 mt-0.5">Sesi Anda telah didiskualifikasi oleh pengawas/sistem.</p>
                            </div>
                        </div>

                        <p class="text-[11px] sm:text-xs text-rose-800 leading-relaxed bg-white/70 p-3 rounded-lg border border-rose-200/60">
                            Anda <strong>tidak dapat lagi masuk ke ruang ujian</strong> dan <strong>tidak dapat mengajukan izin masuk kembali</strong> untuk jadwal ujian ini. Nilai Anda dicatat sebagai diskualifikasi.
                        </p>

                        <div class="pt-1">
                            <a href="{{ route('peserta.dashboard') }}" 
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn w-full text-center">
                                <span>Kembali ke Dashboard Utama</span>
                            </a>
                        </div>
                    </div>
                @elseif($sesi && $sesi->status === 'menunggu_izin')
                    <!-- Tampilan Status Menunggu Izin Pengawas -->
                    <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 space-y-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-amber-950">Permintaan Izin Sedang Ditinjau</h4>
                                <p class="text-[11px] sm:text-xs text-amber-800 mt-0.5">Pengawas ujian sedang memproses permohonan akses Anda.</p>
                            </div>
                        </div>

                        <p class="text-[11px] sm:text-xs text-amber-800 leading-relaxed bg-white/60 p-3 rounded-lg border border-amber-200/60">
                            Silakan tunggu di halaman ini atau klik tombol <strong>Cek Status Masuk Kembali</strong> secara berkala. Halaman ini juga akan memeriksa izin secara otomatis tiap 10 detik.
                        </p>

                        <div class="pt-1 flex flex-col sm:flex-row items-center gap-2.5">
                            <a href="{{ route('peserta.ujian.masuk', $jadwal->id) }}" 
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn w-full sm:flex-1 text-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Cek Status / Masuk Kembali</span>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Form Pengajuan Izin Masuk Kembali -->
                    <form method="POST" action="{{ route('peserta.ujian.minta-izin', $sesi?->id ?? 0) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="alasanInput" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alasan Terputus / Pengajuan Izin Masuk Kembali <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="alasanInput" 
                                      name="alasan" 
                                      required 
                                      rows="3" 
                                      placeholder="Jelaskan kendala Anda, contoh: Perangkat restart otomatis / gangguan jaringan internet / tidak sengaja menutup tab."
                                      class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] transition shadow-2xs leading-relaxed"></textarea>
                            <p class="text-[11px] text-slate-500 mt-1">Alasan yang Anda tulis akan diverifikasi langsung oleh pengawas ujian sebelum diberikan izin akses.</p>
                        </div>

                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            <span>Ajukan Izin Masuk Kembali ke Pengawas</span>
                        </button>
                    </form>
                @endif

                <!-- Footer Navigasi Kembali -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-center">
                    <a href="{{ route('peserta.dashboard') }}" 
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#1e385b] transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Dashboard Utama</span>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Script Polling Auto-Refresh jika sedang menunggu izin -->
    <script>
        function izinApp() {
            return {
                init() {
                    @if($sesi && $sesi->status === 'menunggu_izin')
                        // Periksa secara otomatis setiap 8 detik jika pengawas telah menyetujui izin
                        setInterval(() => {
                            fetch('{{ route("peserta.ujian.masuk", $jadwal->id) }}', {
                                headers: { 'Accept': 'text/html' }
                            }).then(res => {
                                // Jika tidak dialihkan ke halaman izin lagi, refresh halaman
                                if (res.redirected && !res.url.includes('izin')) {
                                    window.location.href = res.url;
                                }
                            }).catch(() => {});
                        }, 8000);
                    @endif
                }
            };
        }
    </script>
</x-exam-layout>
