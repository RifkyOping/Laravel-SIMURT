<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="proctoringDashboard()" x-init="init()">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ruang Pengawas (Proctoring)</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau aktivitas peserta ujian secara real-time dan tangani indikasi kecurangan.</p>
        </div>

        <!-- 1. Daftar Ruang Ujian yang Diawasi (Pintu Masuk Ruangan Ujian untuk Pengawas) -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Ruang Ujian yang Diawasi</h2>
                    <p class="text-xs text-slate-500">Pilih dan masuk ke ruangan ujian untuk mengawasi peserta secara langsung.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($assignedJadwals as $jadwalItem)
                    @php
                        $isSelected = $jadwalAktif && $jadwalAktif->id === $jadwalItem->id;
                        $isBerlangsung = $jadwalItem->status === 'berlangsung';
                    @endphp
                    <div class="bg-white rounded-xl border p-5 shadow-xs flex flex-col justify-between transition {{ $isSelected ? 'border-[#1e385b] ring-2 ring-[#1e385b]/10' : 'border-slate-200/90 hover:border-slate-300' }}">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2">
                                    {{ $jadwalItem->nama_ujian }}
                                </h3>
                                @if($isBerlangsung)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Berlangsung
                                    </span>
                                @elseif($jadwalItem->status === 'akan_datang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200 flex-shrink-0">
                                        Akan Datang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200 flex-shrink-0">
                                        Selesai
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-500 space-y-1.5 pt-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <span>{{ $jadwalItem->tanggal ? $jadwalItem->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    <span>{{ substr($jadwalItem->jam_mulai, 0, 5) }} - {{ substr($jadwalItem->jam_selesai, 0, 5) }} WITA ({{ $jadwalItem->durasi }} Menit)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>{{ $jadwalItem->pesertas_count ?? $jadwalItem->pesertas->count() }} Peserta Terdaftar</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            @if($isSelected)
                                <span class="text-[11px] font-semibold text-[#1e385b] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#1e385b]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    Ruang Terpilih
                                </span>
                            @else
                                <a href="{{ route('pengawas.dashboard', ['jadwal_id' => $jadwalItem->id]) }}" 
                                   class="text-[11px] font-medium text-slate-600 hover:text-slate-900 transition">
                                    Lihat di Dashboard
                                </a>
                            @endif

                            <a href="{{ route('pengawas.monitoring.show', $jadwalItem->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs font-semibold shadow-xs transition">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" />
                                    <line x1="12" y1="17" x2="12" y2="21" />
                                </svg>
                                <span>Masuk Ruang Pengawas</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400">
                        Belum ada jadwal ujian yang ditugaskan kepada Anda.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Kartu Ujian Aktif Saat Ini -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs p-6" id="monitoring-aktif">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 gap-4 md:gap-0">
                <!-- Kolom 1: Ujian Aktif -->
                <div class="md:pr-8 space-y-1">
                    <span class="text-xs font-medium text-slate-500">Ujian Aktif Saat Ini</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        {{ $jadwalAktif ? ($jadwalAktif->nama_ujian === '(SKD) - Batch 1' ? 'Seleksi Kemampuan Dasar (SKD) - Batch 1' : $jadwalAktif->nama_ujian) : 'Seleksi Kemampuan Dasar (SKD) - Batch 1' }}
                    </div>
                </div>

                <!-- Kolom 2: Waktu Mulai -->
                <div class="md:px-8 pt-3 md:pt-0 space-y-1">
                    <span class="text-xs font-medium text-slate-500">Waktu Mulai</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        {{ $jadwalAktif ? substr($jadwalAktif->jam_mulai, 0, 5) . ' WITA' : '08:00 WITA' }}
                    </div>
                </div>

                <!-- Kolom 3: Sisa Waktu Countdown -->
                <div class="md:pl-8 pt-3 md:pt-0 space-y-1">
                    <span class="text-xs font-medium text-slate-500">Sisa Waktu</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight font-mono" x-text="countdownFormatted">
                        {{ $sisaWaktuText }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Peringatan Keamanan Sistem Banner -->
        <div class="rounded-xl border border-amber-200 bg-[#fef9ec] border-l-4 border-l-amber-500 p-4 sm:p-5 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="text-amber-800 text-lg flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Peringatan Keamanan Sistem</h3>
                    <p class="text-xs text-amber-900 mt-1">
                        Peserta <span class="font-bold text-slate-900">{{ $latestPelanggaran?->peserta?->user?->name ?? 'Andi Wijaya' }}</span> {{ $latestPelanggaran?->keterangan ?? 'terdeteksi keluar.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. Monitoring Peserta Terhubung Section -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Monitoring Peserta Terhubung</h2>
            </div>

            <!-- Tabel Data Peserta -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Peserta</th>
                                <th class="py-3 px-4">No. Ujian</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Proggres Soal</th>
                                <th class="py-3 px-4">Terakhir Aktif</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <!-- Template Alpine.js jika items polling sudah dimuat -->
                            <template x-if="items.length > 0">
                                <template x-for="(item, index) in items" :key="item.peserta_id || index">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center text-slate-500 font-medium" x-text="index + 1"></td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900" x-text="item.nama"></td>
                                        <td class="py-3.5 px-4 text-slate-500 font-mono" x-text="item.no_ujian"></td>
                                        <td class="py-3.5 px-4">
                                            <span :class="getStatusBadgeClass(item.status)"
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium"
                                                  x-text="item.status">
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-slate-700" x-text="item.progres"></td>
                                        <td class="py-3.5 px-4 text-slate-500" x-text="item.terakhir_aktif"></td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                <template x-if="item.sesi_id && item.status !== 'Belum Mulai' && item.status !== 'Selesai' && (!item.status || item.status.toLowerCase() !== 'diskualifikasi')">
                                                    <button type="button" 
                                                            @click="openVideoModal(item, 'camera')"
                                                            class="px-2.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Pantau Live Feed Kamera Peserta (WebRTC)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                                                        </svg>
                                                        <span>Kamera</span>
                                                    </button>
                                                </template>
                                                <template x-if="item.sesi_id && item.status !== 'Belum Mulai' && item.status !== 'Selesai' && (!item.status || item.status.toLowerCase() !== 'diskualifikasi')">
                                                    <button type="button" 
                                                            @click="openVideoModal(item, 'screen')"
                                                            class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Pantau Siaran Layar Peserta (WebRTC)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                                        </svg>
                                                        <span>Layar</span>
                                                    </button>
                                                </template>
                                                <template x-if="(item.has_pending_permission || item.status === 'Menunggu Izin') && (!item.status || item.status.toLowerCase() !== 'diskualifikasi')">
                                                    <button type="button" 
                                                            @click="openIzinModal(item)"
                                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Berikan izin masuk kembali">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>Izin</span>
                                                    </button>
                                                </template>
                                                <template x-if="!item.status || item.status.toLowerCase() !== 'diskualifikasi'">
                                                    <button type="button" 
                                                            @click="openTegurModal(item)"
                                                            class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer">
                                                        Tegur
                                                    </button>
                                                </template>
                                                <template x-if="!item.status || item.status.toLowerCase() !== 'diskualifikasi'">
                                                    <button type="button" 
                                                            @click="openBlockModal(item)"
                                                            class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer">
                                                        Blokir
                                                    </button>
                                                </template>
                                                <template x-if="item.status && item.status.toLowerCase() === 'diskualifikasi'">
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium text-slate-400 bg-slate-50 border border-slate-200">
                                                        Diblokir
                                                    </span>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>

                            <!-- Server-rendered fallback jika Alpine belum inisialisasi -->
                            <template x-if="items.length === 0">
                                @forelse($pesertaList as $ps)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center text-slate-500 font-medium">{{ $ps->no }}</td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $ps->nama }}</td>
                                        <td class="py-3.5 px-4 text-slate-500 font-mono">{{ $ps->no_ujian }}</td>
                                        <td class="py-3.5 px-4">
                                            @if($ps->status === 'Peringatan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Peringatan
                                                </span>
                                            @elseif($ps->status === 'Aktif')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Aktif
                                                </span>
                                            @elseif(strtolower($ps->status ?? '') === 'diskualifikasi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Diskualifikasi
                                                </span>
                                            @elseif($ps->status === 'Selesai')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                                    {{ $ps->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-slate-700">{{ $ps->progres }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $ps->terakhir_aktif }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                @if(!empty($ps->sesi_id) && $ps->status !== 'Belum Mulai' && $ps->status !== 'Selesai' && strtolower($ps->status ?? '') !== 'diskualifikasi')
                                                    <button type="button" 
                                                            @click="openVideoModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}', no_ujian: '{{ $ps->no_ujian }}' }, 'camera')"
                                                            class="px-2.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Pantau Live Feed Kamera Peserta (WebRTC)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                                                        </svg>
                                                        <span>Kamera</span>
                                                    </button>
                                                    <button type="button" 
                                                            @click="openVideoModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}', no_ujian: '{{ $ps->no_ujian }}' }, 'screen')"
                                                            class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Pantau Siaran Layar Peserta (WebRTC)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                                        </svg>
                                                        <span>Layar</span>
                                                    </button>
                                                @endif
                                                @if((!empty($ps->has_pending_permission) || $ps->status === 'Menunggu Izin') && strtolower($ps->status ?? '') !== 'diskualifikasi')
                                                    <button type="button" 
                                                            @click="openIzinModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}', no_ujian: '{{ $ps->no_ujian }}', status: '{{ $ps->status }}', alasan_izin: '{{ addslashes($ps->alasan_izin ?? '') }}' })"
                                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer inline-flex items-center gap-1"
                                                            title="Berikan izin masuk kembali">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>Izin</span>
                                                    </button>
                                                @endif
                                                @if(strtolower($ps->status ?? '') !== 'diskualifikasi')
                                                    <button type="button" 
                                                            @click="openTegurModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}' })"
                                                            class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer">
                                                        Tegur
                                                    </button>
                                                    <button type="button" 
                                                            @click="openBlockModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}' })"
                                                            class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer">
                                                        Blokir
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium text-slate-400 bg-slate-50 border border-slate-200">
                                                        Diblokir
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400">
                                            Belum ada peserta yang terhubung pada sesi ujian ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Beri Izin Masuk / Ujian (SIMURT Design System) -->
        <div x-show="showIzinModal" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div @click.away="if (!isSubmittingIzin) showIzinModal = false"
                 class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 relative">
                <!-- Close Button -->
                <button type="button" 
                        @click="showIzinModal = false" 
                        :disabled="isSubmittingIzin"
                        class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition tactile-btn cursor-pointer disabled:opacity-50"
                        title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base leading-tight">Beri Izin Masuk / Ujian</h3>
                        <p class="text-xs text-slate-500">Konfirmasi persetujuan akses ruang ujian</p>
                    </div>
                </div>

                <!-- Info Peserta Card -->
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Nama Peserta:</span>
                        <span class="font-bold text-slate-900" x-text="targetIzinItem?.nama"></span>
                    </div>
                    <div class="flex items-center justify-between" x-show="targetIzinItem?.no_ujian">
                        <span class="text-slate-500 font-medium">Nomor Ujian:</span>
                        <span class="font-mono text-slate-700 font-semibold" x-text="targetIzinItem?.no_ujian"></span>
                    </div>
                    <div class="flex items-center justify-between" x-show="targetIzinItem?.status">
                        <span class="text-slate-500 font-medium">Status Terkini:</span>
                        <span class="font-semibold text-amber-700" x-text="targetIzinItem?.status"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60" x-show="targetIzinItem?.alasan_izin">
                        <span class="text-slate-500 font-medium block mb-1">Alasan Permintaan Izin:</span>
                        <p class="font-medium text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200/80 italic text-xs leading-relaxed" x-text="targetIzinItem?.alasan_izin"></p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Memberikan izin akan membuka blokir akses ujian peserta dan mengizinkan mereka melanjutkan pengerjaan soal secara langsung.
                </p>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="showIzinModal = false" 
                            :disabled="isSubmittingIzin"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition tactile-btn cursor-pointer disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" 
                            @click="confirmIzin()" 
                            :disabled="isSubmittingIzin"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer disabled:opacity-50">
                        <svg x-show="!isSubmittingIzin" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg x-show="isSubmittingIzin" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isSubmittingIzin ? 'Memproses...' : 'Ya, Berikan Izin'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Kirim Teguran Peserta (SIMURT Design System) -->
        <div x-show="showTegurModal" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div @click.away="if (!isSubmittingTegur) showTegurModal = false"
                 class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 relative">
                <!-- Close Button -->
                <button type="button" 
                        @click="showTegurModal = false" 
                        :disabled="isSubmittingTegur"
                        class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition tactile-btn cursor-pointer disabled:opacity-50"
                        title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base leading-tight">Kirim Teguran Peserta</h3>
                        <p class="text-xs text-slate-500">Peringatan resmi pengawas ke layar peserta</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600">
                    Kirim teguran pelanggaran ke peserta <strong class="text-slate-800" x-text="targetTegurItem?.nama"></strong>. Counter pelanggaran peserta akan bertambah.
                </p>

                <!-- Template Pilihan Cepat -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilihan Pesan Cepat:</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" 
                                @click="setTegurTemplate('Peringatan: Tetap fokus pada layar ujian Anda.')"
                                class="px-2.5 py-1 rounded-md text-[11px] font-medium border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
                            Fokus Layar
                        </button>
                        <button type="button" 
                                @click="setTegurTemplate('Peringatan: Dilarang membuka tab atau aplikasi lain.')"
                                class="px-2.5 py-1 rounded-md text-[11px] font-medium border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
                            Dilarang Pindah Tab
                        </button>
                        <button type="button" 
                                @click="setTegurTemplate('Peringatan: Harap posisikan wajah selalu menghadap ke layar.')"
                                class="px-2.5 py-1 rounded-md text-[11px] font-medium border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
                            Hadap Layar
                        </button>
                        <button type="button" 
                                @click="setTegurTemplate('Peringatan: Dilarang menoleh atau berbicara selama ujian.')"
                                class="px-2.5 py-1 rounded-md text-[11px] font-medium border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
                            Dilarang Menoleh
                        </button>
                    </div>
                </div>

                <!-- Textarea Keterangan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pesan Keterangan Teguran</label>
                    <textarea x-model="tegurReason" 
                              rows="3" 
                              placeholder="Masukkan keterangan teguran untuk peserta..."
                              class="w-full text-xs p-3 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-2xs transition"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="showTegurModal = false" 
                            :disabled="isSubmittingTegur"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition tactile-btn cursor-pointer disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" 
                            @click="confirmTegur()" 
                            :disabled="isSubmittingTegur"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer disabled:opacity-50">
                        <svg x-show="!isSubmittingTegur" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <svg x-show="isSubmittingTegur" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isSubmittingTegur ? 'Mengirim...' : 'Kirim Teguran'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Blokir / Diskualifikasi -->
        <div x-show="showBlockModal" 
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="showBlockModal = false" 
                 class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto mb-3 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Diskualifikasi Peserta Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Anda akan mendiskualifikasi <strong class="text-slate-800" x-text="targetItem?.nama"></strong>. Ujian peserta akan langsung diakhiri dan nilainya diubah menjadi <strong>0 (NOL)</strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Diskualifikasi</label>
                    <textarea x-model="blockReason" rows="3" placeholder="Contoh: Terbukti berbuat curang / membuka materi eksternal"
                              class="w-full text-xs p-3 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-2xs transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showBlockModal = false" 
                            class="inline-flex items-center justify-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-medium transition tactile-btn">
                        Batal
                    </button>
                    <button type="button" @click="confirmBlock()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg text-xs shadow-xs transition tactile-btn">
                        Konfirmasi Diskualifikasi
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Live Feed Kamera Peserta (WebRTC) -->
        <div x-show="showVideoModal" 
             class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-start justify-center p-3 sm:p-4 overflow-y-auto" 
             style="display: none;"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95">
            <div @click.away="closeVideoModal()" 
                 :class="proctorMode === 'screen' ? 'max-w-4xl' : 'max-w-xl'"
                 class="bg-white rounded-xl w-full p-4 sm:p-5 shadow-2xl border border-slate-200 space-y-3 transition-all duration-200 mt-2 sm:mt-5 mb-5">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-xs transition-colors"
                             :class="proctorMode === 'screen' ? 'bg-indigo-50 border border-indigo-200 text-indigo-600' : 'bg-[#1e385b]/10 border border-[#1e385b]/20 text-[#1e385b]'">
                            <template x-if="proctorMode === 'screen'">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                </svg>
                            </template>
                            <template x-if="proctorMode === 'camera'">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-tight" x-text="proctorMode === 'screen' ? 'Live Pantau Layar Peserta' : 'Live Kamera Peserta'"></h3>
                                <!-- Status Badge -->
                                <span x-show="videoState === 'connected'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    Terhubung
                                </span>
                                <span x-show="videoState === 'connecting'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <svg class="w-3 h-3 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Menghubungkan
                                </span>
                                <span x-show="videoState === 'error'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Terputus / Butuh Izin
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                <span class="font-semibold text-slate-800" x-text="targetVideoItem?.nama"></span> &bull; No: <span class="font-mono text-slate-700" x-text="targetVideoItem?.no_ujian"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="closeVideoModal()" 
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition tactile-btn cursor-pointer"
                            title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mode Switcher Tabs (Kamera vs Layar) -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg">
                    <button type="button" 
                            @click="switchProctorMode('camera')"
                            :class="proctorMode === 'camera' ? 'bg-[#1e385b] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium hover:bg-slate-200/60'"
                            class="flex-1 py-1.5 px-3 rounded-md text-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" :class="proctorMode === 'camera' ? 'text-white' : 'text-slate-500'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <span>Kamera Depan (Wajah)</span>
                    </button>
                    <button type="button" 
                            @click="switchProctorMode('screen')"
                            :class="proctorMode === 'screen' ? 'bg-[#1e385b] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium hover:bg-slate-200/60'"
                            class="flex-1 py-1.5 px-3 rounded-md text-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" :class="proctorMode === 'screen' ? 'text-white' : 'text-slate-500'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                        </svg>
                        <span>Pantau Layar Monitor</span>
                    </button>
                </div>

                <!-- Video Viewport & Status Overlays -->
                <div :class="proctorMode === 'screen' ? 'h-64 sm:h-[360px]' : 'h-52 sm:h-64'"
                     class="relative w-full bg-slate-950 rounded-xl overflow-hidden border border-slate-800 flex items-center justify-center shadow-inner transition-all duration-200">
                    <video id="remoteProctorVideo" 
                           autoplay 
                           playsinline 
                           muted
                           x-show="videoState === 'connected'" 
                           class="w-full h-full object-contain"></video>

                    <!-- Overlay: Connecting State -->
                    <div x-show="videoState === 'connecting'" class="text-center p-4 text-slate-300 space-y-2.5">
                        <div class="relative w-10 h-10 mx-auto flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-2 border-indigo-400/30 animate-ping"></div>
                            <svg class="w-7 h-7 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-white" x-text="proctorMode === 'screen' ? 'Menghubungkan Siaran Layar Peserta...' : 'Menghubungkan WebRTC ke Perangkat Peserta...'"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5 max-w-sm mx-auto" x-text="proctorMode === 'screen' ? 'Menunggu peserta menyetujui izin berbagi layar di perangkatnya.' : 'Mengirim permintaan transmisi feed kamera real-time.'"></p>
                        </div>
                    </div>

                    <!-- Overlay: Error State -->
                    <div x-show="videoState === 'error'" class="w-full max-w-md text-center p-4 text-slate-300 space-y-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/30">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-rose-300" x-text="videoErrorMessage || (proctorMode === 'screen' ? 'Siaran layar peserta tidak aktif atau izin ditolak.' : 'Kamera peserta tidak aktif atau izin ditolak.')"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-show="proctorMode === 'screen'">
                                Catatan: Berbagi layar hanya didukung pada Laptop/PC peserta (tidak didukung di HP).
                            </p>
                        </div>

                        <!-- Action Buttons in Error State -->
                        <div class="flex items-center justify-center gap-2 pt-0.5">
                            <button type="button" 
                                    @click="retryProctorWebRtc()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-xs text-white font-medium shadow-xs transition tactile-btn cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>Coba Hubungkan Ulang</span>
                            </button>
                        </div>
                    </div>

                    <!-- Top Bar Badges on Active Feed -->
                    <div x-show="videoState === 'connected'" 
                         class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-600/90 text-white shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            LIVE P2P
                        </span>
                        <div class="flex items-center gap-2 pointer-events-auto">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono bg-black/60 text-slate-200 border border-white/10 backdrop-blur-xs"
                                  x-text="proctorMode === 'screen' ? 'Siaran Layar Peserta' : 'Kamera Depan Peserta'">
                            </span>
                            <button type="button" 
                                    @click="toggleFullscreenVideo()" 
                                    class="p-1 rounded bg-black/60 hover:bg-black/80 text-white transition backdrop-blur-xs cursor-pointer"
                                    title="Fullscreen Video">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Diagnostic Help Info Box for Local Testing -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-left">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <p class="text-[11px] text-slate-600 leading-normal">
                            <span class="font-semibold text-slate-800">Petunjuk Uji Lokal:</span> Akses kamera di HP/LAN via HTTP butuh setting <code class="font-mono text-slate-700 bg-slate-200/60 px-1 py-0.5 rounded text-[10px]">chrome://flags/#unsafely-treat-insecure-origin-as-secure</code>. Pantau layar hanya didukung di browser Desktop/Laptop.
                        </p>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="closeVideoModal(); openTegurModal(targetVideoItem);" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition tactile-btn cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <span>Tegur Peserta</span>
                        </button>
                        <button type="button" 
                                @click="closeVideoModal(); openBlockModal(targetVideoItem);" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-300 text-rose-600 bg-white hover:bg-rose-50 font-medium text-xs transition tactile-btn cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span>Blokir</span>
                        </button>
                    </div>
                    <button type="button" 
                            @click="closeVideoModal()" 
                            class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white font-medium text-xs shadow-xs transition tactile-btn cursor-pointer">
                        Tutup Live Feed
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Notification Toast (SIMURT Design System) -->
        <div x-show="toast.show" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3"
             class="fixed bottom-5 right-5 z-50 max-w-sm w-full pointer-events-auto"
             style="display: none;">
            <div class="p-4 rounded-xl shadow-xl border flex items-start gap-3 bg-white"
                 :class="{
                     'border-emerald-200 text-slate-800': toast.type === 'success',
                     'border-amber-200 text-slate-800': toast.type === 'warning',
                     'border-rose-200 text-slate-800': toast.type === 'error'
                 }">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     :class="{
                         'bg-emerald-50 text-emerald-600': toast.type === 'success',
                         'bg-amber-50 text-amber-600': toast.type === 'warning',
                         'bg-rose-50 text-rose-600': toast.type === 'error'
                     }">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                    </template>
                </div>
                <div class="flex-1 pt-0.5">
                    <h4 class="text-xs font-bold leading-tight" 
                        :class="{
                            'text-emerald-900': toast.type === 'success',
                            'text-amber-900': toast.type === 'warning',
                            'text-rose-900': toast.type === 'error'
                        }"
                        x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'warning' ? 'Teguran Dikirim' : 'Pemberitahuan')"></h4>
                    <p class="text-xs text-slate-600 mt-0.5 leading-snug" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition tactile-btn cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Script Alpine.js Real-time Dashboard & Countdown -->
    <script>
        function proctoringDashboard() {
            return {
                jadwalId: '{{ $jadwalAktif?->id }}',
                sisaDetik: {{ $sisaDetik }},
                countdownFormatted: '{{ $sisaWaktuText }}',
                summary: {
                    aktif: {{ $ringkasan['aktif'] }},
                    peringatan: {{ $ringkasan['peringatan'] }},
                    terputus: {{ $ringkasan['terputus'] }},
                    total: {{ $ringkasan['total'] }}
                },
                items: @json($pesertaList),
                showIzinModal: false,
                targetIzinItem: null,
                isSubmittingIzin: false,
                showTegurModal: false,
                targetTegurItem: null,
                tegurReason: 'Peringatan: Tetap fokus pada layar ujian Anda.',
                isSubmittingTegur: false,
                showBlockModal: false,
                targetItem: null,
                blockReason: 'Pelanggaran aturan integritas ruang ujian.',
                toast: { show: false, message: '', type: 'success' },
                toastTimer: null,

                // WebRTC Live Proctoring State (Kamera & Layar)
                showVideoModal: false,
                targetVideoItem: null,
                proctorMode: 'camera',
                videoState: 'idle',
                videoErrorMessage: '',
                proctorPeerConnection: null,
                proctorSignalingTimer: null,
                csrfToken: '{{ csrf_token() }}',
                countdownTimer: null,
                pollTimer: null,

                init() {
                    this.startCountdown();

                    if (this.jadwalId) {
                        // Polling live setiap 4 detik
                        this.pollTimer = setInterval(() => {
                            this.fetchPollData();
                        }, 4000);
                    }
                },

                startCountdown() {
                    this.updateFormatted();
                    this.countdownTimer = setInterval(() => {
                        if (this.sisaDetik > 0) {
                            this.sisaDetik--;
                            this.updateFormatted();
                        } else {
                            clearInterval(this.countdownTimer);
                        }
                    }, 1000);
                },

                updateFormatted() {
                    const h = String(Math.floor(this.sisaDetik / 3600)).padStart(2, '0');
                    const m = String(Math.floor((this.sisaDetik % 3600) / 60)).padStart(2, '0');
                    const s = String(this.sisaDetik % 60).padStart(2, '0');
                    this.countdownFormatted = `${h}:${m}:${s}`;
                },

                getStatusBadgeClass(status) {
                    if (status === 'Belum Mulai') {
                        return 'bg-slate-100 text-slate-600 border border-slate-200';
                    }
                    if (status === 'Peringatan' || status === 'Menunggu Izin') {
                        return 'bg-amber-50 text-amber-700 border border-amber-200';
                    }
                    if (status === 'Aktif') {
                        return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                    }
                    if (status === 'Selesai') {
                        return 'bg-blue-50 text-blue-700 border border-blue-200';
                    }
                    return 'bg-rose-50 text-rose-700 border border-rose-200';
                },

                async fetchPollData() {
                    if (!this.jadwalId) return;

                    try {
                        const response = await fetch(`{{ url('/pengawas/monitoring') }}/${this.jadwalId}/poll`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        });

                        if (!response.ok) return;

                        const data = await response.json();
                        if (data.success && data.items && data.items.length > 0) {
                            this.items = data.items;
                            this.summary = {
                                aktif: data.summary.aktif,
                                peringatan: data.summary.peringatan,
                                terputus: data.summary.terputus,
                                total: data.summary.total_peserta
                            };
                        }
                    } catch (e) {
                        // Silent fallback, retain current state
                    }
                },

                openIzinModal(item) {
                    this.targetIzinItem = item;
                    this.showIzinModal = true;
                },

                async confirmIzin() {
                    if (!this.targetIzinItem) return;
                    this.isSubmittingIzin = true;
                    const sesiId = this.targetIzinItem.sesi_id;
                    const nama = this.targetIzinItem.nama;

                    if (!sesiId) {
                        this.showIzinModal = false;
                        this.isSubmittingIzin = false;
                        this.showToast(`Izin berhasil diberikan kepada ${nama}.`, 'success');
                        return;
                    }

                    try {
                        const res = await fetch(`{{ url('/pengawas/monitoring/sesi') }}/${sesiId}/izin`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        });

                        if (res.ok) {
                            this.showIzinModal = false;
                            this.showToast(`Izin berhasil diberikan kepada ${nama}.`, 'success');
                            this.fetchPollData();
                        } else {
                            this.showToast('Gagal memberikan izin.', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal memberikan izin.', 'error');
                    } finally {
                        this.isSubmittingIzin = false;
                    }
                },

                openTegurModal(item) {
                    this.targetTegurItem = item;
                    this.tegurReason = 'Peringatan: Tetap fokus pada layar ujian Anda.';
                    this.showTegurModal = true;
                },

                setTegurTemplate(text) {
                    this.tegurReason = text;
                },

                async confirmTegur() {
                    if (!this.targetTegurItem) return;
                    this.isSubmittingTegur = true;
                    const sesiId = this.targetTegurItem.sesi_id;
                    const nama = this.targetTegurItem.nama;
                    const ket = this.tegurReason || 'Peringatan: Tetap fokus pada layar ujian Anda.';

                    if (!sesiId) {
                        this.showTegurModal = false;
                        this.isSubmittingTegur = false;
                        this.showToast(`Teguran berhasil dikirim kepada ${nama}.`, 'warning');
                        return;
                    }

                    try {
                        const res = await fetch(`{{ url('/pengawas/monitoring/sesi') }}/${sesiId}/tegur`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({ keterangan: ket })
                        });

                        if (res.ok) {
                            this.showTegurModal = false;
                            this.showToast(`Teguran berhasil dikirim kepada ${nama}.`, 'warning');
                            this.fetchPollData();
                        } else {
                            this.showToast('Gagal mengirim teguran.', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal mengirim teguran.', 'error');
                    } finally {
                        this.isSubmittingTegur = false;
                    }
                },

                openBlockModal(item) {
                    if (item && item.status && item.status.toLowerCase() === 'diskualifikasi') {
                        this.showToast(`Peserta ${item.nama} sudah didiskualifikasi.`, 'info');
                        return;
                    }
                    this.targetItem = item;
                    this.blockReason = 'Pelanggaran aturan integritas ruang ujian.';
                    this.showBlockModal = true;
                },

                async confirmBlock() {
                    if (!this.targetItem) return;

                    if (!this.targetItem.sesi_id) {
                        this.showBlockModal = false;
                        this.showToast(`Peserta ${this.targetItem.nama} telah didiskualifikasi.`, 'error');
                        return;
                    }

                    try {
                        const res = await fetch(`{{ url('/pengawas/monitoring/sesi') }}/${this.targetItem.sesi_id}/blokir`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({ alasan: this.blockReason })
                        });

                        this.showBlockModal = false;
                        if (res.ok) {
                            if (this.targetItem) {
                                this.targetItem.status = 'Diskualifikasi';
                            }
                            const found = this.items.find(i => (this.targetItem && i.sesi_id && i.sesi_id === this.targetItem.sesi_id) || (this.targetItem && i.peserta_id && i.peserta_id === this.targetItem.peserta_id));
                            if (found) {
                                found.status = 'Diskualifikasi';
                            }
                            this.showToast(`Peserta ${this.targetItem.nama} berhasil didiskualifikasi.`, 'error');
                            this.fetchPollData();
                        } else {
                            this.showToast('Gagal mendiskualifikasi peserta.', 'error');
                        }
                    } catch (e) {
                        this.showBlockModal = false;
                        this.showToast('Gagal mendiskualifikasi peserta.', 'error');
                    }
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    if (this.toastTimer) clearTimeout(this.toastTimer);
                    this.toastTimer = setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                },

                // ==========================================
                // WEBRTC PROCTORING VIEWER METHODS
                // ==========================================
                openVideoModal(item, mode = 'camera') {
                    if (!item.sesi_id) {
                        this.showToast('Sesi ujian peserta belum aktif.', 'warning');
                        return;
                    }
                    this.targetVideoItem = item;
                    this.proctorMode = mode;
                    this.showVideoModal = true;
                    this.startProctorWebRtc(item, mode);
                },

                switchProctorMode(mode) {
                    if (this.proctorMode === mode) return;
                    this.proctorMode = mode;
                    if (this.targetVideoItem) {
                        this.startProctorWebRtc(this.targetVideoItem, mode);
                    }
                },

                closeVideoModal() {
                    this.stopProctorWebRtc(true);
                    this.showVideoModal = false;
                    this.targetVideoItem = null;
                    this.proctorMode = 'camera';
                },

                retryProctorWebRtc() {
                    if (this.targetVideoItem) {
                        this.startProctorWebRtc(this.targetVideoItem, this.proctorMode);
                    }
                },

                toggleFullscreenVideo() {
                    const videoEl = document.getElementById('remoteProctorVideo');
                    if (!videoEl) return;
                    if (document.fullscreenElement) {
                        document.exitFullscreen().catch(() => {});
                    } else if (videoEl.requestFullscreen) {
                        videoEl.requestFullscreen().catch(() => {});
                    } else if (videoEl.webkitRequestFullscreen) {
                        videoEl.webkitRequestFullscreen().catch(() => {});
                    }
                },

                async startProctorWebRtc(item, mode = null) {
                    if (mode) {
                        this.proctorMode = mode;
                    }
                    const currentMode = this.proctorMode || 'camera';

                    // Cleanup peer connection lokal tanpa menutup sesi di server
                    this.stopProctorWebRtc(false);
                    this.videoState = 'connecting';
                    this.videoErrorMessage = '';

                    const config = {
                        iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
                    };

                    this.proctorPeerConnection = new RTCPeerConnection(config);

                    // Add transceiver untuk menerima video dari peserta
                    this.proctorPeerConnection.addTransceiver('video', { direction: 'recvonly' });

                    this.proctorPeerConnection.ontrack = (event) => {
                        this.videoState = 'connected';
                        this.$nextTick(() => {
                            const videoEl = document.getElementById('remoteProctorVideo');
                            if (videoEl && event.streams && event.streams[0]) {
                                videoEl.srcObject = event.streams[0];
                                videoEl.play().catch(e => console.log('Autoplay handled:', e));
                            }
                        });
                    };

                    this.proctorPeerConnection.onicecandidate = (event) => {
                        if (event.candidate && item.sesi_id) {
                            this.sendProctorSignal(item.sesi_id, 'peserta', 'candidate', event.candidate);
                        }
                    };

                    this.proctorPeerConnection.onconnectionstatechange = () => {
                        if (!this.proctorPeerConnection) return;
                        if (this.proctorPeerConnection.connectionState === 'connected') {
                            this.videoState = 'connected';
                        } else if (['disconnected', 'failed'].includes(this.proctorPeerConnection.connectionState)) {
                            this.videoState = 'error';
                            this.videoErrorMessage = 'Koneksi WebRTC terputus.';
                        }
                    };

                    // Kirim sinyal request_stream ke peserta dengan mode stream yang diminta
                    await this.sendProctorSignal(item.sesi_id, 'peserta', 'request_stream', { mode: currentMode });

                    // Polling sinyal WebRTC dari peserta tiap 1 detik
                    this.proctorSignalingTimer = setInterval(async () => {
                        if (!this.showVideoModal || !item.sesi_id) return;
                        try {
                            const res = await fetch(`{{ url('/webrtc/sesi') }}/${item.sesi_id}/signal`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken
                                }
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.signals && data.signals.length > 0) {
                                for (const sig of data.signals) {
                                    await this.handleProctorSignal(sig, item.sesi_id);
                                }
                            }
                        } catch (e) {}
                    }, 1000);
                },

                normalizeSdp(sdp) {
                    if (!sdp || typeof sdp !== 'string') return sdp;
                    const lines = sdp
                        .replace(/\r\n/g, '\n')
                        .replace(/\r/g, '\n')
                        .split('\n')
                        .map(line => line.trim())
                        .filter(line => line.length > 0);
                    return lines.join('\r\n') + '\r\n';
                },

                async handleProctorSignal(signal, sesiId) {
                    if (signal.type === 'offer') {
                        if (this.proctorPeerConnection && signal.data) {
                            try {
                                let sdpText = null;
                                let sdpType = 'offer';
                                let payload = signal.data;

                                if (typeof payload === 'string') {
                                    try { payload = JSON.parse(payload); } catch(e) {}
                                }

                                if (payload && payload.sdp && typeof payload.sdp === 'object') {
                                    sdpText = payload.sdp.sdp;
                                    sdpType = payload.sdp.type || 'offer';
                                } else if (payload && typeof payload.sdp === 'string') {
                                    sdpText = payload.sdp;
                                    sdpType = payload.type || 'offer';
                                } else if (payload && payload.type && payload.sdp) {
                                    sdpText = payload.sdp;
                                    sdpType = payload.type;
                                }

                                if (!sdpText) {
                                    console.warn('[WebRTC Dashboard] Format payload offer tidak memiliki sdp string:', payload);
                                    return;
                                }

                                sdpText = this.normalizeSdp(sdpText);

                                // Jika state connection tidak stable, buat ulang RTCPeerConnection agar setRemoteDescription tidak error
                                if (this.proctorPeerConnection.signalingState !== 'stable') {
                                    console.log('[WebRTC Dashboard] Signaling state bukan stable (' + this.proctorPeerConnection.signalingState + '), membuat ulang RTCPeerConnection');
                                    this.proctorPeerConnection.close();
                                    const config = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };
                                    this.proctorPeerConnection = new RTCPeerConnection(config);
                                    this.proctorPeerConnection.addTransceiver('video', { direction: 'recvonly' });
                                    this.proctorPeerConnection.ontrack = (event) => {
                                        this.videoState = 'connected';
                                        this.$nextTick(() => {
                                            const videoEl = document.getElementById('remoteProctorVideo');
                                            if (videoEl && event.streams && event.streams[0]) {
                                                videoEl.srcObject = event.streams[0];
                                                videoEl.play().catch(() => {});
                                            }
                                        });
                                    };
                                    this.proctorPeerConnection.onicecandidate = (event) => {
                                        if (event.candidate && sesiId) {
                                            this.sendProctorSignal(sesiId, 'peserta', 'candidate', event.candidate);
                                        }
                                    };
                                    this.proctorPeerConnection.onconnectionstatechange = () => {
                                        if (!this.proctorPeerConnection) return;
                                        if (this.proctorPeerConnection.connectionState === 'connected') {
                                            this.videoState = 'connected';
                                        } else if (['disconnected', 'failed'].includes(this.proctorPeerConnection.connectionState)) {
                                            this.videoState = 'error';
                                            this.videoErrorMessage = 'Koneksi WebRTC terputus.';
                                        }
                                    };
                                }

                                try {
                                    await this.proctorPeerConnection.setRemoteDescription(new RTCSessionDescription({ type: sdpType, sdp: sdpText }));
                                } catch (sdpErr) {
                                    // Fallback jika browser menolak baris legacy msid Plan B
                                    if (sdpErr.message && (sdpErr.message.includes('msid') || sdpErr.message.includes('Invalid SDP line'))) {
                                        console.warn('[WebRTC Dashboard] Retrying setRemoteDescription without legacy a=ssrc msid line:', sdpErr);
                                        const fallbackLines = sdpText.split('\r\n').filter(line => !(line.startsWith('a=ssrc:') && line.includes('msid:')));
                                        const fallbackSdp = fallbackLines.join('\r\n') + '\r\n';
                                        await this.proctorPeerConnection.setRemoteDescription(new RTCSessionDescription({ type: sdpType, sdp: fallbackSdp }));
                                    } else {
                                        throw sdpErr;
                                    }
                                }

                                const answer = await this.proctorPeerConnection.createAnswer();
                                await this.proctorPeerConnection.setLocalDescription(answer);
                                await this.sendProctorSignal(sesiId, 'peserta', 'answer', {
                                    type: answer.type,
                                    sdp: this.normalizeSdp(answer.sdp)
                                });
                            } catch (e) {
                                console.warn('[WebRTC Dashboard] Gagal memproses offer:', e);
                                this.videoState = 'error';
                                this.videoErrorMessage = 'Gagal menegosiasikan koneksi WebRTC: ' + (e.message || '');
                            }
                        }
                    } else if (signal.type === 'candidate') {
                        if (this.proctorPeerConnection && signal.data) {
                            try {
                                await this.proctorPeerConnection.addIceCandidate(new RTCIceCandidate(signal.data));
                            } catch (e) {}
                        }
                    } else if (signal.type === 'error') {
                        this.videoState = 'error';
                        this.videoErrorMessage = signal.data || (this.proctorMode === 'screen' ? 'Peserta tidak dapat berbagi layar.' : 'Peserta tidak dapat mengakses kamera.');
                    } else if (signal.type === 'stream_ended') {
                        this.videoState = 'error';
                        this.videoErrorMessage = (signal.data && signal.data.mode === 'screen')
                            ? 'Peserta telah menghentikan siaran layar.'
                            : 'Siaran kamera peserta dihentikan.';
                    } else if (signal.type === 'stop_stream') {
                        if (this.videoState === 'connected') {
                            this.videoState = 'error';
                            this.videoErrorMessage = 'Streaming dihentikan oleh peserta.';
                        }
                    }
                },

                stopProctorWebRtc(notifyServer = false) {
                    if (this.proctorSignalingTimer) {
                        clearInterval(this.proctorSignalingTimer);
                        this.proctorSignalingTimer = null;
                    }
                    if (this.proctorPeerConnection) {
                        this.proctorPeerConnection.close();
                        this.proctorPeerConnection = null;
                    }
                    if (notifyServer && this.targetVideoItem && this.targetVideoItem.sesi_id) {
                        fetch(`{{ url('/webrtc/sesi') }}/${this.targetVideoItem.sesi_id}/close`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        }).catch(() => {});
                    }
                    const videoEl = document.getElementById('remoteProctorVideo');
                    if (videoEl) {
                        videoEl.srcObject = null;
                    }
                    this.videoState = 'idle';
                },

                async sendProctorSignal(sesiId, target, type, data) {
                    try {
                        await fetch(`{{ url('/webrtc/sesi') }}/${sesiId}/signal`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({ target, type, data })
                        });
                    } catch (e) {}
                }
            };
        }
    </script>
</x-app-layout>
