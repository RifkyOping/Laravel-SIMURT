<x-app-layout>
    <x-slot name="header">
        Hasil Ujian
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto"
         x-data="{ 
             showDetailModal: false, 
             detailData: {
                 nama: '',
                 nomor_identitas: '',
                 status_kelulusan: '',
                 nilai_akhir: '',
                 jumlah_benar: '',
                 jumlah_salah: '',
                 jumlah_tidak_dijawab: '',
                 waktu_mulai: '',
                 waktu_selesai: '',
                 catatan: ''
             },
             openDetailModal(data) {
                 this.detailData = Object.assign({}, data);
                 this.showDetailModal = true;
             }
         }">
        <!-- Header Title & Subtitle -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekap Nilai Ujian</h1>
            <p class="text-sm text-slate-500 mt-1">Lihat dan kelola hasil penilaian seluruh peserta ujian.</p>
        </div>

        <!-- Filter Bar & Download Button -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.nilai.index') }}" class="flex flex-wrap items-center gap-3 sm:gap-4 flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64 md:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama peserta..."
                        class="w-full text-sm pl-9 pr-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                </div>

                <!-- Dropdown Ujian -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-700">Ujian:</span>
                    <select name="jadwal_id" onchange="this.form.submit()"
                        class="text-sm px-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 font-medium pr-8 cursor-pointer">
                        @foreach($jadwals as $jd)
                            <option value="{{ $jd->id }}" {{ $selectedJadwalId == $jd->id ? 'selected' : '' }}>
                                {{ str_replace(['(', ')'], '', $jd->nama_ujian) }}
                            </option>
                        @endforeach
                        <option value="Semua" {{ request('jadwal_id') === 'Semua' ? 'selected' : '' }}>Semua Ujian</option>
                    </select>
                </div>

                <!-- Dropdown Kelulusan -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-700">Kelulusan:</span>
                    <select name="status_kelulusan" onchange="this.form.submit()"
                        class="text-sm px-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 font-medium pr-8 cursor-pointer">
                        <option value="Semua" {{ request('status_kelulusan') === 'Semua' || !request('status_kelulusan') ? 'selected' : '' }}>Semua</option>
                        <option value="LULUS" {{ request('status_kelulusan') === 'LULUS' ? 'selected' : '' }}>LULUS</option>
                        <option value="TIDAK LULUS" {{ request('status_kelulusan') === 'TIDAK LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                        <option value="DISKUALIFIKASI" {{ request('status_kelulusan') === 'DISKUALIFIKASI' ? 'selected' : '' }}>DISKUALIFIKASI</option>
                    </select>
                </div>

                @if(request('search') || (request('status_kelulusan') && request('status_kelulusan') !== 'Semua'))
                    <a href="{{ route('panitia.nilai.index', ['jadwal_id' => $selectedJadwalId]) }}"
                        class="text-xs text-slate-500 hover:text-slate-800 underline transition">
                        Reset Filter
                    </a>
                @endif
            </form>

            <!-- Download PDF Rekap Action Button -->
            <div>
                <a href="{{ route('panitia.nilai.cetak', array_merge(request()->all(), ['jadwal_id' => $selectedJadwalId])) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-semibold shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Download PDF Rekap</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Statistik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Total Peserta</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $totalPeserta }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Rata-rata Nilai</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ round($rataRataNilai) }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Lulus</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $totalLulus }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Tidak Lulus</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $totalTidakLulus }}</span>
            </div>
        </div>

        <!-- Tabel Data Rekap Nilai Ujian -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                            <th class="py-3.5 px-4 w-14 text-center">No</th>
                            <th class="py-3.5 px-4">Nama Peserta</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Nomor Identitas</th>
                            <th class="py-3.5 px-4">Jawaban Benar</th>
                            <th class="py-3.5 px-4">Nilai Akhir</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($nilais as $index => $n)
                            @php
                                $pelanggaran = $n->peserta->pelanggarans->where('jadwal_id', $n->jadwal_id)->first();
                                $catatanText = $pelanggaran 
                                    ? $pelanggaran->keterangan 
                                    : ($n->status_kelulusan === 'DISKUALIFIKASI' 
                                        ? 'Terdeteksi membuka aplikasi lain saat ujian berlangsung.' 
                                        : 'Peserta menjawab dengan lancar. Tidak ditemukan tindakan pelanggaran selama ujian berlangsung.');

                                $waktuMulaiFormatted = $n->sesiUjian?->waktu_masuk 
                                    ? $n->sesiUjian->waktu_masuk->format('H:i') . ' WITA' 
                                    : (\Carbon\Carbon::parse($n->jadwal->jam_mulai)->format('H:i') . ' WITA');

                                $waktuSelesaiFormatted = $n->sesiUjian?->waktu_selesai 
                                    ? $n->sesiUjian->waktu_selesai->format('H:i') . ' WITA' 
                                    : (\Carbon\Carbon::parse($n->jadwal->jam_selesai)->format('H:i') . ' WITA');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-4 text-center text-slate-500 font-medium">
                                    {{ $nilais->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ $n->peserta->user->name }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    {{ $n->peserta->user->email }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 font-mono">
                                    {{ $n->peserta->no_identitas }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    {{ $n->jumlah_benar }} Soal
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ (int) $n->nilai_akhir }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($n->status_kelulusan === 'LULUS')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-[#dcfce7] text-[#16a34a]">
                                            LULUS
                                        </span>
                                    @elseif($n->status_kelulusan === 'DISKUALIFIKASI')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-[#ef4444] text-white">
                                            DISKUALIFIKASI
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-[#fee2e2] text-[#dc2626]">
                                            TIDAK LULUS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button type="button"
                                        @click="openDetailModal({{ json_encode([
                                            'nama' => $n->peserta->user->name,
                                            'nomor_identitas' => $n->peserta->no_identitas,
                                            'status_kelulusan' => $n->status_kelulusan,
                                            'nilai_akhir' => (int) $n->nilai_akhir,
                                            'jumlah_benar' => $n->jumlah_benar,
                                            'jumlah_salah' => $n->jumlah_salah,
                                            'jumlah_tidak_dijawab' => $n->jumlah_tidak_dijawab,
                                            'waktu_mulai' => $waktuMulaiFormatted,
                                            'waktu_selesai' => $waktuSelesaiFormatted,
                                            'catatan' => $catatanText
                                        ]) }})"
                                        class="inline-flex items-center justify-center px-4 py-1.5 bg-[#1e385b] hover:bg-[#152740] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">
                                    Belum ada data nilai ujian yang sesuai kriteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Bar -->
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs sm:text-sm text-slate-500 font-medium">
                    Menampilkan {{ $nilais->firstItem() ?? 0 }}-{{ $nilais->lastItem() ?? 0 }} dari {{ $nilais->total() }} entries
                </div>
                <div>
                    {{ $nilais->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>

        <!-- Pop Up Modal Detail Hasil Ujian (screen-rekap-nilai-ujian) -->
        <div x-show="showDetailModal"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div @click.away="showDetailModal = false"
                 class="bg-white rounded-xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 my-8">
                <!-- Header -->
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Detail Hasil Ujian</h3>
                    <button type="button" @click="showDetailModal = false"
                            class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition p-1.5 rounded-lg cursor-pointer tactile-btn"
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Info Peserta (Nama, No Identitas, Badge Status) -->
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="text-xs text-slate-500 font-medium block">Nama Peserta</span>
                        <span class="text-base font-bold text-slate-900 mt-0.5 block" x-text="detailData.nama"></span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-medium block">Nomor Identitas</span>
                        <span class="text-base font-bold text-slate-900 mt-0.5 block font-mono" x-text="detailData.nomor_identitas"></span>
                    </div>
                    <div>
                        <template x-if="detailData.status_kelulusan === 'LULUS'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                LULUS
                            </span>
                        </template>
                        <template x-if="detailData.status_kelulusan === 'DISKUALIFIKASI'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-600 text-white">
                                DISKUALIFIKASI
                            </span>
                        </template>
                        <template x-if="detailData.status_kelulusan === 'TIDAK LULUS'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                TIDAK LULUS
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Statistik Hasil -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-slate-500 block text-center">Statistik Hasil</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-white rounded-lg border border-slate-200 p-3 sm:p-4 shadow-xs">
                            <span class="text-xs text-slate-500 font-medium block">Nilai Akhir</span>
                            <span class="text-2xl font-bold text-slate-900 mt-1 block" x-text="detailData.nilai_akhir"></span>
                        </div>
                        <div class="bg-white rounded-lg border border-slate-200 p-3 sm:p-4 shadow-xs">
                            <span class="text-xs text-slate-500 font-medium block">Jawaban Benar</span>
                            <span class="text-2xl font-bold text-slate-900 mt-1 block" x-text="detailData.jumlah_benar"></span>
                        </div>
                        <div class="bg-white rounded-lg border border-slate-200 p-3 sm:p-4 shadow-xs">
                            <span class="text-xs text-slate-500 font-medium block">Jawaban Salah</span>
                            <span class="text-2xl font-bold text-slate-900 mt-1 block" x-text="detailData.jumlah_salah"></span>
                        </div>
                        <div class="bg-white rounded-lg border border-slate-200 p-3 sm:p-4 shadow-xs">
                            <span class="text-xs text-slate-500 font-medium block">Tidak Dijawab</span>
                            <span class="text-2xl font-bold text-slate-900 mt-1 block" x-text="detailData.jumlah_tidak_dijawab"></span>
                        </div>
                    </div>
                </div>

                <!-- Log Waktu -->
                <div class="space-y-1.5">
                    <span class="text-xs font-bold text-slate-700 block">Log Waktu</span>
                    <div class="flex items-center gap-10">
                        <div>
                            <span class="text-xs text-slate-400 block">Waktu Mulai</span>
                            <span class="text-sm font-bold text-slate-900" x-text="detailData.waktu_mulai"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Waktu Selesai</span>
                            <span class="text-sm font-bold text-slate-900" x-text="detailData.waktu_selesai"></span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Pengawasan / Pelanggaran -->
                <div class="space-y-1.5">
                    <span class="text-xs font-bold text-slate-700 block">Catatan Pengawasan / Pelanggaran</span>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-lg p-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        <p x-text="detailData.catatan"></p>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex justify-end pt-2">
                    <button type="button" @click="showDetailModal = false"
                            class="px-6 py-2 bg-[#1e385b] hover:bg-[#152843] text-white font-semibold rounded-lg text-sm shadow-xs transition cursor-pointer tactile-btn">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
