<x-app-layout>
    <x-slot name="header">
        Kelola Jadwal Ujian
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto" 
         x-data="{ 
             showAddModal: false, 
             showEditModal: false,
             pesertaMode: 'semua',
             searchPeserta: '',
             selectedPeserta: [],
             pesertaList: {{ Js::from($pesertas->map(fn($p) => ['id' => $p->id, 'name' => $p->user->name, 'nik' => $p->no_identitas ?? '-'])) }},
             get filteredPeserta() {
                 if (!this.searchPeserta.trim()) {
                     return this.pesertaList;
                 }
                 const q = this.searchPeserta.toLowerCase();
                 return this.pesertaList.filter(p => 
                     p.name.toLowerCase().includes(q) || (p.nik && p.nik.toLowerCase().includes(q))
                 );
             },
             toggleAllFilteredPeserta() {
                 const filteredIds = this.filteredPeserta.map(p => p.id);
                 const allSelected = filteredIds.length > 0 && filteredIds.every(id => this.selectedPeserta.includes(id));
                 if (allSelected) {
                     this.selectedPeserta = this.selectedPeserta.filter(id => !filteredIds.includes(id));
                 } else {
                     this.selectedPeserta = Array.from(new Set([...this.selectedPeserta, ...filteredIds]));
                 }
             },
             editForm: {
                 id: '',
                 nama_ujian: '',
                 tanggal: '',
                 jam_mulai: '',
                 jam_selesai: '',
                 durasi: 120,
                 paket_soal_id: '',
                 status: '',
                 passing_grade: ''
             },
             openEditModal(data) {
                 this.editForm = Object.assign({}, data);
                 this.showEditModal = true;
             }
         }">
        <!-- Header Title & Subtitle -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Jadwal Ujian</h1>
                <p class="text-sm text-slate-500 mt-1">Buat, ubah, dan hapus jadwal pelaksanaan ujian.</p>
            </div>
            <div>
                <button type="button" @click="showAddModal = true; pesertaMode = 'semua'; searchPeserta = ''; selectedPeserta = [];"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-semibold shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Jadwal</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.jadwal.index') }}" class="flex flex-wrap items-center gap-4">
                <!-- Search Input -->
                <div class="relative w-72 sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama ujian..."
                        class="w-full text-sm pl-9 pr-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                </div>

                <!-- Status Filter Dropdown -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-700">Status:</span>
                    <select name="status" onchange="this.form.submit()"
                        class="text-sm px-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 font-medium pr-8 cursor-pointer">
                        <option value="Semua" {{ request('status') === 'Semua' || !request('status') ? 'selected' : '' }}>Semua</option>
                        <option value="akan_datang" {{ request('status') === 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="berlangsung" {{ request('status') === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                @if(request('search') || (request('status') && request('status') !== 'Semua'))
                    <a href="{{ route('panitia.jadwal.index') }}"
                        class="text-xs text-slate-500 hover:text-slate-800 underline transition">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Data Jadwal Ujian -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                            <th class="py-3.5 px-4 w-14 text-center">No</th>
                            <th class="py-3.5 px-4">Nama Ujian</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Waktu Mulai</th>
                            <th class="py-3.5 px-4">Waktu Selesai</th>
                            <th class="py-3.5 px-4">Durasi</th>
                            <th class="py-3.5 px-4">Jumlah Peserta</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($jadwals as $index => $j)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-4 text-center text-slate-500 font-medium">
                                    {{ $jadwals->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ $j->nama_ujian }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($j->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap font-medium">
                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} WITA
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap font-medium">
                                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} WITA
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $j->durasi }} Menit
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $j->pesertas_count }} Peserta
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($j->status === 'berlangsung')
                                        <span class="inline-flex items-center px-3 py-1 rounded-md bg-[#fef9c3] text-[#854d0e] text-xs font-semibold">
                                            Berlangsung
                                        </span>
                                    @elseif($j->status === 'akan_datang')
                                        <span class="inline-flex items-center px-3 py-1 rounded-md bg-[#e8f0fe] text-[#1a73e8] text-xs font-semibold">
                                            Akan Datang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-md bg-[#e2e8f0] text-[#475569] text-xs font-semibold">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
                                        <button type="button"
                                            @click="openEditModal({{ json_encode([
                                                'id' => $j->id,
                                                'nama_ujian' => $j->nama_ujian,
                                                'tanggal' => \Carbon\Carbon::parse($j->tanggal)->format('Y-m-d'),
                                                'jam_mulai' => substr($j->jam_mulai, 0, 5),
                                                'jam_selesai' => substr($j->jam_selesai, 0, 5),
                                                'durasi' => $j->durasi,
                                                'paket_soal_id' => $j->paket_soal_id,
                                                'status' => $j->status,
                                                'passing_grade' => $j->passing_grade
                                            ]) }})"
                                            class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn cursor-pointer">
                                            Edit
                                        </button>
                                        <a href="{{ route('panitia.jadwal.edit', $j->id) }}"
                                            class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs font-medium shadow-xs transition tactile-btn">
                                            Peserta
                                        </a>
                                        <form method="POST" action="{{ route('panitia.jadwal.destroy', $j->id) }}"
                                            class="inline"
                                            data-confirm-title="Hapus Jadwal Ujian"
                                            data-confirm-message="Apakah Anda yakin ingin menghapus jadwal ujian &quot;{{ $j->nama_ujian }}&quot;? Seluruh sesi dan data peserta terkait jadwal ini akan terhapus secara permanen.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-rose-600 text-xs font-medium transition tactile-btn cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-400">
                                    Belum ada data jadwal ujian yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Bar -->
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs sm:text-sm text-slate-500 font-medium">
                    Menampilkan {{ $jadwals->firstItem() ?? 0 }}-{{ $jadwals->lastItem() ?? 0 }} dari {{ $jadwals->total() }} entries
                </div>
                <div>
                    {{ $jadwals->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>

        <!-- Pop Up Modal Edit Jadwal (screen-kelola-jadwal-ujian) -->
        <div x-show="showEditModal"
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div @click.away="showEditModal = false"
                 class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] overflow-hidden my-auto"
                 style="max-height: 90vh;">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white shrink-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Edit Jadwal Pelaksanaan</h3>
                    <button type="button" @click="showEditModal = false"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" :action="'{{ url('panitia/jadwal') }}/' + editForm.id" class="flex flex-col flex-1 overflow-hidden m-0">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="nama_ujian" :value="editForm.nama_ujian">
                    <input type="hidden" name="paket_soal_id" :value="editForm.paket_soal_id">
                    <input type="hidden" name="status" :value="editForm.status">
                    <input type="hidden" name="passing_grade" :value="editForm.passing_grade">

                    <!-- Scrollable Content -->
                    <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1 text-xs sm:text-sm" style="max-height: calc(90vh - 130px); overflow-y: auto;">
                        <!-- Title Ujian Centered -->
                        <div class="text-center pb-2 border-b border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Ujian</span>
                            <h4 class="text-base font-bold text-slate-900 mt-0.5" x-text="editForm.nama_ujian"></h4>
                        </div>

                        <!-- 2 Column Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                            <!-- Tanggal Pelaksanaan -->
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-slate-800 mb-1.5">Tanggal Pelaksanaan</label>
                                <input type="date" name="tanggal" x-model="editForm.tanggal" required
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-700 bg-white" />
                            </div>

                            <!-- Durasi -->
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-slate-800 mb-1.5">Durasi</label>
                                <div class="relative">
                                    <input type="number" name="durasi" x-model="editForm.durasi" min="1" required
                                           placeholder="120 Menit"
                                           class="w-full text-sm px-3.5 py-2.5 pr-14 rounded-xl border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-700 bg-white" />
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs font-semibold text-slate-500">
                                        Menit
                                    </span>
                                </div>
                            </div>

                            <!-- Waktu Mulai -->
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-slate-800 mb-1.5">Waktu Mulai</label>
                                <input type="time" name="jam_mulai" x-model="editForm.jam_mulai" required
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-700 bg-white" />
                            </div>

                            <!-- Waktu Selesai -->
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-slate-800 mb-1.5">Waktu Selesai</label>
                                <input type="time" name="jam_selesai" x-model="editForm.jam_selesai" required
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-700 bg-white" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showEditModal = false"
                                class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-semibold rounded-xl text-xs sm:text-sm shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Jadwal Ujian -->
        <div x-show="showAddModal"
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showAddModal = false"
                 class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] overflow-hidden my-auto"
                 style="max-height: 90vh;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1e385b] flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">Buat Jadwal Pelaksanaan Ujian Baru</h3>
                            <p class="text-xs text-slate-500">Konfigurasi paket soal, waktu sesi, serta penugasan peserta & pengawas.</p>
                        </div>
                    </div>
                    <button type="button" @click="showAddModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('panitia.jadwal.store') }}" class="flex flex-col flex-1 overflow-hidden m-0">
                    @csrf

                    <!-- Scrollable Form Body -->
                    <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1 text-xs sm:text-sm" style="max-height: calc(90vh - 130px); overflow-y: auto;">
                        <!-- Nama Ujian -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Ujian <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_ujian" required placeholder="Contoh: Seleksi Kemampuan Dasar (SKD) - Sesi 1"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                        </div>

                        <!-- Paket Soal & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Pilih Paket Soal <span class="text-rose-500">*</span></label>
                                <select name="paket_soal_id" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs">
                                    @foreach($paketSoals as $pkt)
                                        <option value="{{ $pkt->id }}">{{ $pkt->nama_paket }} ({{ $pkt->durasi }} Menit)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Pelaksanaan <span class="text-rose-500">*</span></label>
                                <select name="status" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs">
                                    <option value="akan_datang" selected>Akan Datang</option>
                                    <option value="berlangsung">Sedang Berlangsung</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tanggal, Jam Mulai, Jam Selesai -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                       class="w-full text-sm px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                                <input type="time" name="jam_mulai" value="08:00" required
                                       class="w-full text-sm px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                                <input type="time" name="jam_selesai" value="10:00" required
                                       class="w-full text-sm px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                            </div>
                        </div>

                        <!-- Durasi & Passing Grade -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Durasi Ujian (Menit) <span class="text-rose-500">*</span></label>
                                <input type="number" name="durasi" value="120" min="1" required
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Passing Grade <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                                <input type="number" step="0.01" name="passing_grade" placeholder="Default dari paket soal"
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                            </div>
                        </div>

                        <!-- Section: Peserta Ujian -->
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block font-bold text-slate-800 text-sm mb-2">Penugasan Peserta Ujian</label>
                            
                            <!-- Mode Selector: Semua vs Pilih Manual -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-3">
                                <label class="relative flex items-start p-3 rounded-xl border cursor-pointer transition"
                                       :class="pesertaMode === 'semua' ? 'border-[#1e385b] bg-blue-50/50 ring-1 ring-[#1e385b]' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                    <input type="radio" name="peserta_mode" value="semua" x-model="pesertaMode" class="mt-0.5 text-[#1e385b] focus:ring-[#1e385b]">
                                    <div class="ml-2.5">
                                        <span class="block text-xs font-bold text-slate-900">Semua Terverifikasi <span class="text-[10px] bg-blue-100 text-[#1e385b] px-1.5 py-0.5 rounded-full font-bold">Rekomendasi</span></span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">Otomatis ikutkan seluruh {{ $pesertas->count() }} peserta aktif.</span>
                                    </div>
                                </label>

                                <label class="relative flex items-start p-3 rounded-xl border cursor-pointer transition"
                                       :class="pesertaMode === 'pilih' ? 'border-[#1e385b] bg-blue-50/50 ring-1 ring-[#1e385b]' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                    <input type="radio" name="peserta_mode" value="pilih" x-model="pesertaMode" class="mt-0.5 text-[#1e385b] focus:ring-[#1e385b]">
                                    <div class="ml-2.5">
                                        <span class="block text-xs font-bold text-slate-900">Pilih Manual / Tertentu</span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">Pilih peserta tertentu (ujian susulan / batch khusus).</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Banner info jika Mode Semua -->
                            <div x-show="pesertaMode === 'semua'" class="flex items-center gap-2.5 p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-[#1e385b]">
                                <svg class="w-5 h-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-medium">Seluruh <strong>{{ $pesertas->count() }} peserta</strong> berstatus akun terverifikasi akan langsung otomatis didaftarkan ke jadwal ujian ini.</span>
                            </div>

                            <!-- Detail checklist jika Mode Pilih Manual -->
                            <div x-show="pesertaMode === 'pilih'" class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" x-model="searchPeserta" placeholder="Cari nama atau NIK peserta..."
                                               class="w-full text-xs px-3 py-1.5 pl-8 rounded-lg border border-slate-300 focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]">
                                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="toggleAllFilteredPeserta()" class="text-xs font-semibold text-[#1e385b] hover:underline">
                                            Pilih/Batal Semua
                                        </button>
                                        <span class="text-xs text-slate-400">|</span>
                                        <span class="text-xs font-bold text-slate-600" x-text="selectedPeserta.length + ' dipilih'"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 overflow-y-auto"
                                     style="max-height: 180px; overflow-y: auto;">
                                    <template x-for="ps in filteredPeserta" :key="ps.id">
                                        <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 bg-white p-2 rounded-lg border border-slate-100 hover:border-slate-300 transition">
                                            <input type="checkbox" name="peserta_ids[]" :value="ps.id" x-model="selectedPeserta"
                                                   class="rounded border-slate-300 text-[#1e385b] focus:ring-[#1e385b]">
                                            <div class="truncate">
                                                <span class="font-semibold block truncate" x-text="ps.name"></span>
                                                <span class="text-[10px] text-slate-400 font-normal block" x-text="ps.nik"></span>
                                            </div>
                                        </label>
                                    </template>
                                    <div x-show="filteredPeserta.length === 0" class="col-span-2 text-center py-4 text-xs text-slate-400">
                                        Tidak ada peserta yang cocok dengan pencarian.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Pengawas Bertugas -->
                        <div class="pt-2 border-t border-slate-100" x-data="{
                            allPengawas: {{ Js::from($pengawas->pluck('id')) }},
                            selectedPengawas: {{ Js::from($pengawas->pluck('id')) }},
                            toggleAll() {
                                if (this.selectedPengawas.length === this.allPengawas.length) {
                                    this.selectedPengawas = [];
                                } else {
                                    this.selectedPengawas = [...this.allPengawas];
                                }
                            }
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block font-bold text-slate-800 text-sm">Pilih Pengawas Bertugas</label>
                                <button type="button" @click="toggleAll()" class="text-xs font-semibold text-[#1e385b] hover:underline">
                                    <span x-text="selectedPengawas.length === allPengawas.length ? 'Batalkan Semua' : 'Pilih Semua (' + allPengawas.length + ')'"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 overflow-y-auto"
                                 style="max-height: 130px; overflow-y: auto;">
                                @forelse($pengawas as $pg)
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white p-2 rounded-lg border border-slate-100 hover:border-slate-300 transition">
                                        <input type="checkbox" name="pengawas_ids[]" value="{{ $pg->id }}" x-model="selectedPengawas"
                                               class="rounded border-slate-300 text-[#1e385b] focus:ring-[#1e385b]">
                                        <div class="truncate">
                                            <span class="block truncate">{{ $pg->name }}</span>
                                            <span class="text-[10px] text-slate-400 font-normal block">{{ $pg->email }}</span>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-xs text-slate-400 col-span-2 py-2 text-center">Belum ada pengawas terdaftar.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Sticky Modal Footer -->
                    <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 border border-slate-300 bg-white rounded-xl text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Simpan Jadwal Ujian</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
