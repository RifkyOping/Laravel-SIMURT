<x-app-layout>
    <x-slot name="header">
        Edit Jadwal Pelaksanaan Ujian
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Perbarui Jadwal Ujian</h1>
                    <p class="text-xs text-slate-500">Edit parameter tanggal, jam, peserta, dan pengawas</p>
                </div>
                <a href="{{ route('panitia.jadwal.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('panitia.jadwal.update', $jadwal->id) }}" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Ujian</label>
                    <input type="text" name="nama_ujian" value="{{ old('nama_ujian', $jadwal->nama_ujian) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Paket Soal</label>
                        <select name="paket_soal_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                            @foreach($paketSoals as $pkt)
                                <option value="{{ $pkt->id }}" {{ $jadwal->paket_soal_id === $pkt->id ? 'selected' : '' }}>
                                    {{ $pkt->nama_paket }} ({{ $pkt->durasi }} Menit)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Pelaksanaan</label>
                        <select name="status" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                            <option value="akan_datang" {{ $jadwal->status === 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="berlangsung" {{ $jadwal->status === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="selesai" {{ $jadwal->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}" required
                               class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" required
                               class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" required
                               class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Durasi Ujian (Menit)</label>
                        <input type="number" name="durasi" value="{{ old('durasi', $jadwal->durasi) }}" min="1" required
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Passing Grade</label>
                        <input type="number" step="0.01" name="passing_grade" value="{{ old('passing_grade', $jadwal->passing_grade) }}" min="0" max="500"
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                </div>

                <!-- Section: Peserta Ujian -->
                <div class="pt-2 border-t border-slate-100" x-data="{
                    pesertaMode: '{{ $jadwal->pesertas->count() === $pesertas->count() && $pesertas->count() > 0 ? 'semua' : 'pilih' }}',
                    searchPeserta: '',
                    selectedPeserta: {{ Js::from($jadwal->pesertas->pluck('id')) }},
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
                    }
                }">
                    <label class="block font-bold text-slate-800 text-xs sm:text-sm mb-2">Penugasan Peserta Ujian</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-3">
                        <label class="relative flex items-start p-2.5 rounded-xl border cursor-pointer transition"
                               :class="pesertaMode === 'semua' ? 'border-[#1e385b] bg-blue-50/50 ring-1 ring-[#1e385b]' : 'border-slate-200 bg-white hover:bg-slate-50'">
                            <input type="radio" name="peserta_mode" value="semua" x-model="pesertaMode" class="mt-0.5 text-[#1e385b] focus:ring-[#1e385b]">
                            <div class="ml-2">
                                <span class="block text-xs font-bold text-slate-900">Semua Terverifikasi</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Ikutkan seluruh {{ $pesertas->count() }} peserta.</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-2.5 rounded-xl border cursor-pointer transition"
                               :class="pesertaMode === 'pilih' ? 'border-[#1e385b] bg-blue-50/50 ring-1 ring-[#1e385b]' : 'border-slate-200 bg-white hover:bg-slate-50'">
                            <input type="radio" name="peserta_mode" value="pilih" x-model="pesertaMode" class="mt-0.5 text-[#1e385b] focus:ring-[#1e385b]">
                            <div class="ml-2">
                                <span class="block text-xs font-bold text-slate-900">Pilih Tertentu</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5" x-text="selectedPeserta.length + ' peserta dipilih saat ini.'"></span>
                            </div>
                        </label>
                    </div>

                    <div x-show="pesertaMode === 'semua'" class="flex items-center gap-2 p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-[#1e385b]">
                        <svg class="w-4 h-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs">Seluruh <strong>{{ $pesertas->count() }} peserta</strong> berstatus akun terverifikasi akan didaftarkan ke jadwal ujian ini.</span>
                    </div>

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
                    selectedPengawas: {{ Js::from($jadwal->pengawas->pluck('id')) }},
                    toggleAll() {
                        if (this.selectedPengawas.length === this.allPengawas.length) {
                            this.selectedPengawas = [];
                        } else {
                            this.selectedPengawas = [...this.allPengawas];
                        }
                    }
                }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-bold text-slate-800 text-xs sm:text-sm">Pengawas Bertugas</label>
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

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('panitia.jadwal.index') }}" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-bold text-xs rounded-xl shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
