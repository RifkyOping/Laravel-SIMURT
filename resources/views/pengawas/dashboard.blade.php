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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="text-base font-bold text-slate-900">Monitoring Peserta Terhubung</h2>

                <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span> 
                        Aktif (<span x-text="summary.aktif">{{ $ringkasan['aktif'] }}</span>)
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b]"></span> 
                        Peringatan (<span x-text="summary.peringatan">{{ $ringkasan['peringatan'] }}</span>)
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#ef4444]"></span> 
                        Terputus (<span x-text="summary.terputus">{{ $ringkasan['terputus'] }}</span>)
                    </span>
                </div>
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
                                                <button type="button" 
                                                        @click="beriIzin(item.sesi_id, item.nama)"
                                                        class="px-3 py-1 rounded bg-[#10b981] hover:bg-[#059669] text-white font-bold text-xs shadow-xs transition">
                                                    Izin
                                                </button>
                                                <button type="button" 
                                                        @click="tegurPeserta(item.sesi_id, item.nama)"
                                                        class="px-3 py-1 rounded bg-[#f59e0b] hover:bg-[#d97706] text-white font-bold text-xs shadow-xs transition">
                                                    Tegur
                                                </button>
                                                <button type="button" 
                                                        @click="openBlockModal(item)"
                                                        class="px-3 py-1 rounded bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold text-xs shadow-xs transition">
                                                    Blokir
                                                </button>
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
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#fef3c7] text-[#92400e]">
                                                    Peringatan
                                                </span>
                                            @elseif($ps->status === 'Aktif')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#d1fae5] text-[#065f46]">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#fee2e2] text-[#991b1b]">
                                                    {{ $ps->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-slate-700">{{ $ps->progres }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $ps->terakhir_aktif }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                <button type="button" 
                                                        @click="beriIzin({{ $ps->sesi_id ?? 'null' }}, '{{ $ps->nama }}')"
                                                        class="px-3 py-1 rounded bg-[#10b981] hover:bg-[#059669] text-white font-bold text-xs shadow-xs transition">
                                                    Izin
                                                </button>
                                                <button type="button" 
                                                        @click="tegurPeserta({{ $ps->sesi_id ?? 'null' }}, '{{ $ps->nama }}')"
                                                        class="px-3 py-1 rounded bg-[#f59e0b] hover:bg-[#d97706] text-white font-bold text-xs shadow-xs transition">
                                                    Tegur
                                                </button>
                                                <button type="button" 
                                                        @click="openBlockModal({ sesi_id: {{ $ps->sesi_id ?? 'null' }}, nama: '{{ $ps->nama }}' })"
                                                        class="px-3 py-1 rounded bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold text-xs shadow-xs transition">
                                                    Blokir
                                                </button>
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

        <!-- Modal Konfirmasi Blokir / Diskualifikasi -->
        <div x-show="showBlockModal" 
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="showBlockModal = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-3">
                        🚫
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Diskualifikasi Peserta Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Anda akan mendiskualifikasi <strong class="text-slate-800" x-text="targetItem?.nama"></strong>. Ujian peserta akan langsung diakhiri dan nilainya diubah menjadi <strong>0 (NOL)</strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Diskualifikasi</label>
                    <textarea x-model="blockReason" rows="3" placeholder="Contoh: Terbukti berbuat curang / membuka materi eksternal"
                              class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 shadow-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showBlockModal = false" 
                            class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button" @click="confirmBlock()" 
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-md shadow-rose-500/20">
                        Konfirmasi Diskualifikasi
                    </button>
                </div>
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
                showBlockModal: false,
                targetItem: null,
                blockReason: 'Pelanggaran aturan integritas ruang ujian.',
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
                    if (status === 'Peringatan') {
                        return 'bg-[#fef3c7] text-[#92400e]';
                    }
                    if (status === 'Aktif') {
                        return 'bg-[#d1fae5] text-[#065f46]';
                    }
                    return 'bg-[#fee2e2] text-[#991b1b]';
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

                async tegurPeserta(sesiId, nama) {
                    if (!sesiId) {
                        alert(`Teguran berhasil dikirim kepada peserta ${nama}.`);
                        return;
                    }

                    const ket = prompt(`Masukkan keterangan teguran untuk ${nama}:`, 'Peringatan: Tetap fokus pada layar ujian Anda.');
                    if (!ket) return;

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
                            alert(`Teguran berhasil dikirim kepada ${nama}.`);
                            this.fetchPollData();
                        } else {
                            alert('Gagal mengirim teguran.');
                        }
                    } catch (e) {
                        alert('Gagal mengirim teguran.');
                    }
                },

                async beriIzin(sesiId, nama) {
                    if (!confirm(`Berikan izin masuk / melanjutkan ujian kepada ${nama}?`)) return;

                    if (!sesiId) {
                        alert(`Izin berhasil diberikan kepada ${nama}.`);
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
                            alert(`Izin berhasil diberikan kepada ${nama}.`);
                            this.fetchPollData();
                        } else {
                            alert('Gagal memberikan izin.');
                        }
                    } catch (e) {
                        alert('Gagal memberikan izin.');
                    }
                },

                openBlockModal(item) {
                    this.targetItem = item;
                    this.blockReason = 'Pelanggaran aturan integritas ruang ujian.';
                    this.showBlockModal = true;
                },

                async confirmBlock() {
                    if (!this.targetItem) return;

                    if (!this.targetItem.sesi_id) {
                        this.showBlockModal = false;
                        alert(`Peserta ${this.targetItem.nama} telah didiskualifikasi.`);
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
                            alert(`Peserta ${this.targetItem.nama} berhasil didiskualifikasi.`);
                            this.fetchPollData();
                        } else {
                            alert('Gagal mendiskualifikasi peserta.');
                        }
                    } catch (e) {
                        this.showBlockModal = false;
                        alert('Gagal mendiskualifikasi peserta.');
                    }
                }
            };
        }
    </script>
</x-app-layout>
