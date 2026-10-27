<x-app-layout>
    <x-slot name="header">
        Laporan Ujian
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Laporan Ujian</h1>
            <p class="text-xs text-slate-500 mt-0.5">Laporan aktivitas dan hasil pelaksanaan ujian secara terstruktur.</p>
        </div>

        <!-- Filter Controls & Export PDF Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Dropdowns Form -->
            <form method="GET" action="{{ route('panitia.laporan.index') }}" class="flex flex-wrap items-center gap-3">
                <!-- Filter Periode -->
                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-2xs">
                    <span class="text-xs text-slate-500 font-medium">Periode:</span>
                    <select name="periode" onchange="this.form.submit()" 
                            class="text-xs font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer pr-4 focus:outline-none">
                        <option value="bulan_ini" {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="minggu_ini" {{ $periode === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="hari_ini" {{ $periode === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="tahun_ini" {{ $periode === 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Periode</option>
                    </select>
                </div>

                <!-- Filter Ujian -->
                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-2xs">
                    <span class="text-xs text-slate-500 font-medium">Ujian:</span>
                    <select name="jadwal_id" onchange="this.form.submit()"
                            class="text-xs font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer pr-4 focus:outline-none max-w-[200px] truncate">
                        <option value="semua" {{ $jadwalId === 'semua' ? 'selected' : '' }}>Semua Ujian</option>
                        @foreach($semuaJadwalList as $sj)
                            <option value="{{ $sj->id }}" {{ $jadwalId == $sj->id ? 'selected' : '' }}>
                                {{ $sj->nama_ujian }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Action Export PDF Button -->
            <div>
                <a href="{{ route('panitia.laporan.export-pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-xs font-bold shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Ringkasan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Ujian -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Total Ujian</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalUjian }}</div>
            </div>

            <!-- Total Peserta -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Total Peserta</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalPesertaTerdaftar) }}</div>
            </div>

            <!-- Rata-rata Kelulusan -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Rata-rata Kelulusan</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $rataRataKelulusan }}%</div>
            </div>

            <!-- Pelanggaran -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">Pelanggaran</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalPelanggaran }}</div>
            </div>
        </div>

        <!-- Tren Tingkat Kelulusan Per Ujian (%) -->
        <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-4 tracking-tight">Tren Tingkat Kelulusan Per Ujian (%)</h2>

            <div class="space-y-3.5">
                @foreach($trenKelulusan as $item)
                    <div class="flex items-center text-xs">
                        <!-- Nama Ujian -->
                        <span class="font-bold text-slate-800 w-64 sm:w-72 flex-shrink-0 truncate">
                            {{ $item['nama'] }}
                        </span>

                        <!-- Horizontal Bar -->
                        <div class="flex-1 h-3.5 bg-slate-100 rounded-full overflow-hidden mx-4">
                            <div class="h-full rounded-full bg-[#1e385b] transition-all duration-500"
                                 style="width: {{ $item['persen'] }}%;"></div>
                        </div>

                        <!-- Persentase Kelulusan -->
                        <span class="font-bold text-slate-800 w-10 text-right flex-shrink-0">
                            {{ $item['persen'] }}%
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tabel Laporan Pelaksanaan Ujian -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-500 font-semibold">
                            <th class="py-3.5 px-4 font-semibold">Nama Ujian</th>
                            <th class="py-3.5 px-4 font-semibold">Tanggal</th>
                            <th class="py-3.5 px-4 font-semibold">Peserta</th>
                            <th class="py-3.5 px-4 font-semibold">Rata-rata Nilai</th>
                            <th class="py-3.5 px-4 font-semibold">Kelulusan (%)</th>
                            <th class="py-3.5 px-4 font-semibold">Pelanggaran</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laporanList as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $item['nama_ujian'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $item['tanggal'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $item['peserta_str'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $item['rata_nilai'] }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-emerald-600">
                                    {{ $item['kelulusan_persen'] }}%
                                </td>
                                <td class="py-3.5 px-4 {{ $item['total_pelanggaran'] > 0 ? 'font-bold text-rose-600' : 'text-slate-600' }}">
                                    {{ $item['total_pelanggaran'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if(!empty($item['jadwal_id']))
                                        <a href="{{ route('panitia.laporan.detail', $item['jadwal_id']) }}"
                                           class="inline-block px-4 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152740] text-white text-xs font-semibold shadow-2xs transition">
                                            Detail
                                        </a>
                                    @else
                                        <span class="inline-block px-4 py-1.5 rounded-lg bg-[#1e385b] text-white text-xs font-semibold shadow-2xs opacity-80 cursor-default">
                                            Detail
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada data pelaksanaan ujian ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500">
                    Menampilkan {{ $laporanList->firstItem() ?? 1 }}-{{ $laporanList->lastItem() ?? 10 }} dari {{ $laporanList->total() ?? 48 }} entries
                </p>
                <div>
                    {{ $laporanList->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
