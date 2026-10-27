<x-app-layout>
    <x-slot name="header">
        Bank Soal — {{ $paket->nama_paket }}
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto" 
         x-data="{ 
            showAddModal: false, 
            showEditModal: false,
            showImportModal: false, 
            editForm: {
                id: null,
                pertanyaan: '',
                kategori: '',
                tingkat_kesulitan: 'sedang',
                bobot: 50.00,
                opsi_a: '',
                opsi_b: '',
                opsi_c: '',
                opsi_d: '',
                opsi_e: '',
                kunci_jawaban: 'A',
                aktif: true
            },
            openEditModal(soal) {
                this.editForm.id = soal.id;
                this.editForm.pertanyaan = soal.pertanyaan;
                this.editForm.kategori = soal.kategori || '';
                this.editForm.tingkat_kesulitan = soal.tingkat_kesulitan || 'sedang';
                this.editForm.bobot = soal.bobot || 50.00;
                this.editForm.kunci_jawaban = soal.kunci_jawaban || 'A';
                this.editForm.aktif = Boolean(soal.aktif);
                const ops = typeof soal.opsi_jawaban === 'string' ? JSON.parse(soal.opsi_jawaban) : (soal.opsi_jawaban || {});
                this.editForm.opsi_a = ops.A || '';
                this.editForm.opsi_b = ops.B || '';
                this.editForm.opsi_c = ops.C || '';
                this.editForm.opsi_d = ops.D || '';
                this.editForm.opsi_e = ops.E || '';
                this.showEditModal = true;
            }
         }">

        <!-- Header Halaman -->
        <div>
            <a href="{{ route('panitia.paket.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#1e385b] font-medium transition mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Paket Ujian ({{ $paket->nama_paket }})</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bank Soal</h1>
            <p class="text-sm text-slate-500 mt-1">Unggah, kelola, dan atur soal ujian.</p>
        </div>

        <!-- 3 Kartu Statistik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Total Soal</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $totalSoal }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Soal Aktif</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $soalAktif }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Soal Non-Aktif</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $soalNonAktif }}</span>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.soal.index', $paket->id) }}" class="w-full sm:w-64 md:w-72">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari soal..."
                           class="w-full text-sm pl-9 pr-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                </div>
            </form>

            <div class="flex items-center gap-2.5 sm:gap-3">
                <button type="button" 
                        @click="showImportModal = true"
                        class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-lg text-sm font-semibold border border-slate-700 shadow-xs transition whitespace-nowrap">
                    Import Soal
                </button>

                <button type="button" 
                        @click="showAddModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-semibold shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Soal</span>
                </button>
            </div>
        </div>

        <!-- Tabel Bank Soal (3 Kolom: No, Pertanyaan, Aksi) -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                            <th class="py-3.5 px-4 sm:px-6 w-14 text-center">No</th>
                            <th class="py-3.5 px-4 sm:px-6">Pertanyaan</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($soals as $index => $s)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 text-center">{{ $soals->firstItem() + $index }}</td>
                                <td class="py-3.5 px-4 sm:px-6 font-medium text-slate-900 leading-relaxed">
                                    {{ $s->pertanyaan }}
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($s) }})"
                                                class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn cursor-pointer">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('panitia.soal.destroy', [$paket->id, $s->id]) }}" class="inline"
                                              data-confirm-title="Hapus Butir Soal"
                                              data-confirm-message="Apakah Anda yakin ingin menghapus butir soal ini? Tindakan ini tidak dapat dibatalkan.">
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
                                <td colspan="3" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada soal dalam paket ini. Klik tombol "Tambah Soal" atau "Import Soal".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="border-t border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <div class="text-xs sm:text-sm text-slate-500">
                    Menampilkan {{ $soals->firstItem() ?? 0 }}-{{ $soals->lastItem() ?? 0 }} dari {{ $soals->total() }} entries
                </div>
                <div>
                    {{ $soals->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah Soal -->
        <div x-show="showAddModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" 
             style="display: none;">
            <div @click.away="showAddModal = false" 
                 class="bg-white rounded-xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 my-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Tambah Soal</h3>
                    <button type="button" @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('panitia.soal.store', $paket->id) }}" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1.5">Pertanyaan</label>
                        <textarea name="pertanyaan" rows="4" required placeholder="Tulis pertanyaan soal di sini..."
                                  class="w-full text-sm p-3.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400"></textarea>
                    </div>

                    <!-- Pilihan Jawaban A s.d E -->
                    <div class="space-y-3 pt-1">
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Pilihan Jawaban</label>
                            <span class="text-xs text-slate-400 mt-0.5 block">Pilih bulatan untuk menentukan Kunci Jawaban</span>
                        </div>

                        @foreach(['A' => 'Jawaban A', 'B' => 'Jawaban B', 'C' => 'Jawaban C', 'D' => 'Jawaban D', 'E' => 'Jawaban E'] as $opt => $placeholder)
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                    <input type="radio" name="kunci_jawaban" value="{{ $opt }}" required {{ $opt === 'B' ? 'checked' : '' }}
                                           class="text-[#1e385b] focus:ring-[#1e385b] w-4 h-4 cursor-pointer text-slate-900">
                                    <span class="font-bold text-sm text-slate-800 w-3 text-center">
                                        {{ $opt }}
                                    </span>
                                </label>
                                <input type="text" name="opsi_{{ strtolower($opt) }}" required placeholder="{{ $placeholder }}"
                                       class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showAddModal = false" 
                                class="px-5 py-2 border border-slate-300 bg-white text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-medium rounded-lg text-sm shadow-xs transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Soal -->
        <div x-show="showEditModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" 
             style="display: none;">
            <div @click.away="showEditModal = false" 
                 class="bg-white rounded-xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 my-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Edit Soal</h3>
                    <button type="button" @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="`{{ url('/panitia/paket/' . $paket->id . '/soal') }}/${editForm.id}`" class="space-y-4 text-sm">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1.5">Pertanyaan</label>
                        <textarea name="pertanyaan" x-model="editForm.pertanyaan" rows="4" required placeholder="Tulis pertanyaan soal di sini..."
                                  class="w-full text-sm p-3.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400"></textarea>
                    </div>

                    <!-- Pilihan Jawaban A s.d E -->
                    <div class="space-y-3 pt-1">
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Pilihan Jawaban</label>
                            <span class="text-xs text-slate-400 mt-0.5 block">Pilih bulatan untuk menentukan Kunci Jawaban</span>
                        </div>

                        @foreach(['A' => 'Jawaban A', 'B' => 'Jawaban B', 'C' => 'Jawaban C', 'D' => 'Jawaban D', 'E' => 'Jawaban E'] as $opt => $placeholder)
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                    <input type="radio" name="kunci_jawaban" value="{{ $opt }}" x-model="editForm.kunci_jawaban" required
                                           class="text-[#1e385b] focus:ring-[#1e385b] w-4 h-4 cursor-pointer text-slate-900">
                                    <span class="font-bold text-sm text-slate-800 w-3 text-center">
                                        {{ $opt }}
                                    </span>
                                </label>
                                <input type="text" name="opsi_{{ strtolower($opt) }}" x-model="editForm.opsi_{{ strtolower($opt) }}" required placeholder="{{ $placeholder }}"
                                       class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" 
                                class="px-5 py-2 border border-slate-300 bg-white text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-medium rounded-lg text-sm shadow-xs transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Import Soal CSV -->
        <div x-show="showImportModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="showImportModal = false" 
                 class="bg-white rounded-xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Import Butir Soal (CSV)</h3>
                    <button type="button" @click="showImportModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-3.5 rounded-lg bg-blue-50/80 border border-blue-200 text-blue-900 text-xs space-y-1.5">
                    <p class="font-bold">Format Kolom File CSV:</p>
                    <p class="text-[11px] leading-relaxed text-blue-800">
                        Pertanyaan, Opsi A, Opsi B, Opsi C, Opsi D, Opsi E, Kunci Jawaban (A-E), Bobot, Kategori
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('panitia.soal.template') }}" class="font-semibold underline text-blue-700 hover:text-blue-900 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Download Template CSV Soal</span>
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('panitia.soal.import', $paket->id) }}" enctype="multipart/form-data" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih File CSV</label>
                        <input type="file" name="file_soal" accept=".csv,.txt" required
                               class="w-full text-xs p-2 rounded-lg border border-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1e385b] hover:file:bg-blue-100 cursor-pointer" />
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showImportModal = false" 
                                class="px-5 py-2 border border-slate-300 bg-white text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-medium rounded-lg text-sm shadow-xs transition">
                            Unggah & Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
