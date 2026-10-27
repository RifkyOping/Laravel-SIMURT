<x-app-layout>
    <x-slot name="header">
        Detail Laporan Ujian — {{ $namaUjian }}
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Back Navigation & Title Header (Mockup screen-detail-laporan-ujian) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('panitia.laporan.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition mb-2">
                    <span>&larr;</span> Kembali
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Detail Laporan: {{ $namaUjian }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Tanggal pelaksanaan: {{ $jadwal->tanggal ? $jadwal->tanggal->translatedFormat('d M Y') : '28 Sep 2024' }}
                </p>
            </div>
            <div>
                <a href="{{ route('panitia.laporan.cetak', $jadwal->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1e385b] hover:bg-[#152740] text-white rounded-xl text-xs font-bold shadow-xs transition">
                    Download Laporan (PDF)
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Peserta -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Total Peserta</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalPeserta) }}</div>
                <p class="text-xs text-slate-400 mt-1">Peserta terdaftar</p>
            </div>

            <!-- Card 2: Hadir -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Hadir</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalHadir) }}</div>
                <p class="text-xs text-slate-400 mt-1">{{ $hadirPersen }}% dari total peserta</p>
            </div>

            <!-- Card 3: Rata-rata Nilai -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Rata-rata Nilai</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $rataRataNilai }}</div>
                <p class="text-xs text-slate-400 mt-1">Skala 0–500</p>
            </div>

            <!-- Card 4: Total Pelanggaran -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Total Pelanggaran</span>
                <div class="text-3xl font-extrabold text-rose-600 tracking-tight">{{ $totalPelanggaran }}</div>
                <p class="text-xs text-rose-500 font-medium mt-1">{{ $totalDiskualifikasi }} kasus diskualifikasi</p>
            </div>
        </div>

        <!-- Bagian Bawah: 2 Kolom (Distribusi Status & Log Pelanggaran) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Kolom Kiri: Distribusi Status Peserta (Donut Chart) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Distribusi Status Peserta</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ringkasan hasil berdasarkan status kelulusan</p>
                </div>

                <!-- Donut Chart & Legend -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-8">
                    <!-- Donut Chart Ring -->
                    <div class="relative w-44 h-44 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner"
                         style="background: conic-gradient(
                            #1e385b 0% {{ $lulusPersen }}%, 
                            #93c5fd {{ $lulusPersen }}% {{ $lulusPersen + $tidakLulusPersen }}%, 
                            #cbd5e1 {{ $lulusPersen + $tidakLulusPersen }}% 100%
                         );">
                        <!-- Inner Hole Cutout -->
                        <div class="w-28 h-28 bg-white rounded-full flex flex-col items-center justify-center shadow-2xs">
                            <span class="text-[11px] text-slate-400 font-medium tracking-wide">Status</span>
                            <span class="text-2xl font-black text-slate-900 leading-tight">{{ $totalPeserta }}</span>
                        </div>
                    </div>

                    <!-- Legend Status -->
                    <div class="space-y-4">
                        <!-- Lulus -->
                        <div class="flex items-start gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1e385b] mt-1 flex-shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Lulus</span>
                                <span class="text-xs text-slate-500">{{ $lulusCount }} peserta ({{ $lulusPersen }}%)</span>
                            </div>
                        </div>

                        <!-- Tidak Lulus -->
                        <div class="flex items-start gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#93c5fd] mt-1 flex-shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Tidak Lulus</span>
                                <span class="text-xs text-slate-500">{{ $tidakLulusCount }} peserta ({{ $tidakLulusPersen }}%)</span>
                            </div>
                        </div>

                        <!-- Diskualifikasi -->
                        <div class="flex items-start gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#cbd5e1] mt-1 flex-shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Diskualifikasi</span>
                                <span class="text-xs text-slate-500">{{ $diskualifikasiCount }} peserta ({{ $diskualifikasiPersen }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block"></div>
            </div>

            <!-- Kolom Kanan: Log Pelanggaran & Anomali -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Log Pelanggaran &amp; Anomali</h2>
                    <p class="text-xs text-slate-500 mt-0.5 mb-4">Catatan insiden selama pelaksanaan ujian</p>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-500 font-semibold">
                                <th class="pb-3 px-3 w-28 font-semibold">Waktu Kejadian</th>
                                <th class="pb-3 px-3 w-28 font-semibold">Nama Peserta</th>
                                <th class="pb-3 px-3 font-semibold">Detail Pelanggaran</th>
                                <th class="pb-3 px-3 text-center w-28 font-semibold">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logPelanggaran as $log)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-3 text-slate-500 whitespace-nowrap text-xs">
                                        {{ $log['waktu'] }}
                                    </td>
                                    <td class="py-4 px-3 font-bold text-slate-900 whitespace-nowrap text-xs">
                                        {{ $log['nama_peserta'] }}
                                    </td>
                                    <td class="py-4 px-3 text-xs leading-relaxed {{ $log['is_diskualifikasi'] ? 'text-rose-600 font-medium' : 'text-amber-600 font-medium' }}">
                                        {{ $log['detail'] }}
                                    </td>
                                    <td class="py-4 px-3 text-center whitespace-nowrap">
                                        @if($log['is_diskualifikasi'])
                                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                                                {{ $log['tindakan'] }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                                {{ $log['tindakan'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Tidak ada catatan pelanggaran atau anomali pada pelaksanaan ujian ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
