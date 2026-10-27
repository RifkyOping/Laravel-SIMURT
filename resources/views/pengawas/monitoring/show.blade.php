<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="monitoringApp()" x-init="init()">
        <!-- Header Halaman: Tombol Kembali & Pemilih Ruangan Ujian -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <a href="{{ route('pengawas.monitoring.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#1e385b] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Ujian</span>
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Monitoring: {{ $selectedJadwal ? ($selectedJadwal->nama_ujian === '(SKD) - Batch 1' ? 'Seleksi Kemampuan Dasar (SKD) - Batch 1' : $selectedJadwal->nama_ujian) : 'Seleksi Kemampuan Dasar (SKD) - Batch 1' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">Pantau aktivitas peserta ujian secara real-time dan berikan tindakan pengawasan.</p>
            </div>

            <!-- Selector Pindah Ruangan Ujian -->
            <div class="flex items-center gap-2.5 bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                <div class="w-8 h-8 rounded-lg bg-[#1e385b]/10 text-[#1e385b] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-[#1e385b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <label for="selectJadwalRoom" class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Ganti Ruang Ujian</label>
                    <select id="selectJadwalRoom" 
                            onchange="window.location.href = '{{ url('/pengawas/monitoring') }}/' + this.value"
                            class="text-xs font-bold text-slate-800 bg-transparent border-0 p-0 pr-6 focus:ring-0 cursor-pointer">
                        @foreach($allJadwals as $j)
                            <option value="{{ $j->id }}" {{ $selectedJadwal && $selectedJadwal->id === $j->id ? 'selected' : '' }}>
                                {{ $j->nama_ujian }} ({{ ucfirst(str_replace('_', ' ', $j->status)) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 1. Kartu Ujian Aktif Saat Ini -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 gap-4 md:gap-0">
                <!-- Kolom 1: Ujian Aktif -->
                <div class="md:pr-8 space-y-1">
                    <span class="text-xs font-semibold uppercase text-slate-500">UJIAN AKTIF SAAT INI</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        {{ $selectedJadwal ? ($selectedJadwal->nama_ujian === '(SKD) - Batch 1' ? 'Seleksi Kemampuan Dasar (SKD) - Batch 1' : $selectedJadwal->nama_ujian) : 'Seleksi Kemampuan Dasar (SKD) - Batch 1' }}
                    </div>
                </div>

                <!-- Kolom 2: Waktu Mulai -->
                <div class="md:px-8 pt-3 md:pt-0 space-y-1">
                    <span class="text-xs font-semibold text-slate-500">Waktu Mulai</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        {{ $selectedJadwal ? substr($selectedJadwal->jam_mulai, 0, 5) . ' WITA' : '08:00 WITA' }}
                    </div>
                </div>

                <!-- Kolom 3: Sisa Waktu Countdown -->
                <div class="md:pl-8 pt-3 md:pt-0 space-y-1">
                    <span class="text-xs font-semibold text-slate-500">Sisa Waktu</span>
                    <div class="text-base sm:text-lg font-bold text-slate-900 tracking-tight font-mono" x-text="countdownFormatted">
                        {{ $sisaWaktuText }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Bar Kontrol: Status Badges & Search Box -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Status Indicators -->
            <div class="flex items-center gap-5 text-xs font-medium text-slate-700">
                <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span> 
                    Aktif (<span x-text="summary.aktif">{{ $ringkasan['aktif'] }}</span>)
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b]"></span> 
                    Peringatan (<span x-text="summary.peringatan">{{ $ringkasan['peringatan'] }}</span>)
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ef4444]"></span> 
                    Terputus (<span x-text="summary.terputus">{{ $ringkasan['terputus'] }}</span>)
                </span>
            </div>

            <!-- Input Pencarian Peserta -->
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-linecap="round" />
                    </svg>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="currentPage = 1"
                       placeholder="Cari nama peserta..." 
                       class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] bg-white placeholder:text-slate-400 shadow-2xs transition" />
            </div>
        </div>

        <!-- 3. Tabel Monitoring Peserta -->
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
                        <template x-if="paginatedItems.length > 0">
                            <template x-for="(item, idx) in paginatedItems" :key="item.peserta_id || idx">
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-slate-500 font-medium" x-text="(currentPage - 1) * perPage + idx + 1"></td>
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

                        <!-- Fallback jika daftar item kosong -->
                        <template x-if="paginatedItems.length === 0">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada peserta yang terhubung pada sesi ujian ini.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-slate-800" x-text="filteredItems.length > 0 ? (currentPage - 1) * perPage + 1 : 0"></span> s.d. 
                    <span class="font-bold text-slate-800" x-text="Math.min(currentPage * perPage, filteredItems.length)"></span> dari 
                    <span class="font-bold text-slate-800" x-text="filteredItems.length"></span> peserta
                </div>

                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" 
                            @click="goToPage(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-2.5 py-1 rounded border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <template x-for="p in totalPages" :key="p">
                        <button type="button" 
                                @click="goToPage(p)" 
                                :class="currentPage === p ? 'bg-[#1e385b] text-white font-bold' : 'hover:bg-slate-50 text-slate-700'"
                                class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center font-medium"
                                x-text="p">
                        </button>
                    </template>
                    <button type="button" 
                            @click="goToPage(currentPage + 1)" 
                            :disabled="currentPage === totalPages"
                            class="px-2.5 py-1 rounded border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Blokir Peserta -->
        <div x-show="showBlockModal" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border border-slate-200">
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Diskualifikasi / Blokir Peserta</h3>
                        <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600">
                    Apakah Anda yakin ingin mendiskualifikasi peserta <strong x-text="targetItem ? targetItem.nama : ''"></strong> dari ujian ini?
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Diskualifikasi</label>
                    <textarea x-model="blockReason" rows="3" class="w-full text-xs rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showBlockModal = false" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                    <button type="button" @click="confirmBlock()" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs">Diskualifikasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function monitoringApp() {
            return {
                items: @json($pesertaList),
                summary: @json($ringkasan),
                jadwalId: {{ $selectedJadwal?->id ?? 'null' }},
                sisaDetik: {{ $sisaDetik }},
                countdownFormatted: '{{ $sisaWaktuText }}',
                countdownTimer: null,
                pollTimer: null,
                searchQuery: '',
                currentPage: 1,
                perPage: 10,
                csrfToken: '{{ csrf_token() }}',
                showBlockModal: false,
                targetItem: null,
                blockReason: '',

                init() {
                    this.startCountdown();
                    this.pollTimer = setInterval(() => {
                        this.fetchPollData();
                    }, 5000);
                },

                get filteredItems() {
                    if (!this.searchQuery) return this.items;
                    const q = this.searchQuery.toLowerCase();
                    return this.items.filter(item => {
                        return (item.nama && item.nama.toLowerCase().includes(q)) ||
                               (item.no_ujian && item.no_ujian.toLowerCase().includes(q)) ||
                               (item.status && item.status.toLowerCase().includes(q));
                    });
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredItems.length / this.perPage));
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
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
                        // Silent fallback
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
