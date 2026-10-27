<x-app-layout>
    <x-slot name="header">
        Laporan Sistem
    </x-slot>

    <div class="space-y-6">
        <!-- Header: Judul & Filter/Export Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Sistem</h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas dan performa sistem secara keseluruhan.</p>
            </div>

            <!-- Controls: Periode Dropdown & Export Button -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Dropdown Periode dengan Alpine.js -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                            class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-xs sm:text-sm font-medium text-slate-700 flex items-center gap-2 hover:bg-slate-50 transition shadow-none cursor-pointer tactile-btn">
                        <span>Periode: {{ $periodeLabel ?? 'Bulan Ini' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-spring duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                         class="absolute right-0 mt-2 z-30 bg-white border border-slate-200 rounded-xl p-1.5 shadow-xl w-44 space-y-1"
                         style="display: none;">
                        <a href="{{ route('admin.laporan.index', ['periode' => 'hari_ini']) }}" 
                           class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition {{ request('periode') == 'hari_ini' ? 'bg-slate-100 font-bold' : '' }}">
                            Hari Ini
                        </a>
                        <a href="{{ route('admin.laporan.index', ['periode' => 'minggu_ini']) }}" 
                           class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition {{ request('periode') == 'minggu_ini' ? 'bg-slate-100 font-bold' : '' }}">
                            Minggu Ini
                        </a>
                        <a href="{{ route('admin.laporan.index', ['periode' => 'bulan_ini']) }}" 
                           class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition {{ (request('periode', 'bulan_ini') == 'bulan_ini') ? 'bg-slate-100 font-bold' : '' }}">
                            Bulan Ini
                        </a>
                        <a href="{{ route('admin.laporan.index', ['periode' => 'tahun_ini']) }}" 
                           class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition {{ request('periode') == 'tahun_ini' ? 'bg-slate-100 font-bold' : '' }}">
                            Tahun Ini
                        </a>
                    </div>
                </div>

                <!-- Tombol Export Data -->
                <a href="{{ route('admin.laporan.export') }}" 
                   class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-xs sm:text-sm font-medium text-slate-700 flex items-center gap-2 hover:bg-slate-50 transition shadow-none cursor-pointer tactile-btn">
                    <!-- Icon Tray / Download SVG persis Mockup Figma -->
                    <svg class="w-4 h-4 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v11m0 0l-3.5-3.5M12 15l3.5-3.5" />
                    </svg>
                    <span>Export Data</span>
                </a>
            </div>
        </div>

        <!-- 4 KPI Stat Cards (Persis Mockup: Tanpa suffix label tambahan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Total Login Hari Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-none card-lift">
                <div class="text-xs sm:text-sm font-medium text-slate-600">Total Login Hari Ini</div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 sm:mt-3 tracking-tight">
                    {{ $totalLoginHariIni }}
                </div>
            </div>

            <!-- 2. Ujian Aktif -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-none card-lift">
                <div class="text-xs sm:text-sm font-medium text-slate-600">Ujian Aktif</div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 sm:mt-3 tracking-tight">
                    {{ $ujianAktif }}
                </div>
            </div>

            <!-- 3. Pelanggaran Bulan Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-none card-lift">
                <div class="text-xs sm:text-sm font-medium text-slate-600">Pelanggaran Bulan Ini</div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 sm:mt-3 tracking-tight">
                    {{ $pelanggaranBulanIni }}
                </div>
            </div>

            <!-- 4. Uptime Sistem -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-none card-lift">
                <div class="text-xs sm:text-sm font-medium text-slate-600">Uptime Sistem</div>
                <div class="text-3xl sm:text-4xl font-extrabold text-emerald-500 mt-2 sm:mt-3 tracking-tight">
                    {{ $uptimeSistem }}
                </div>
            </div>
        </div>

        <!-- Row 2: 2 Column Layout (Ringkasan Ujian & Distribusi Role Pengguna) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Kiri: Ringkasan Ujian (~60% / col-span-7) -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl p-6 shadow-none card-lift">
                <h2 class="text-base font-bold text-slate-900 mb-4">Ringkasan Ujian</h2>

                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#f0f4f8] text-slate-700 text-xs sm:text-sm font-semibold">
                                    <th class="py-3 px-4">Nama Ujian</th>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Peserta</th>
                                    <th class="py-3 px-4">Avg Nilai</th>
                                    <th class="py-3 px-4">Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                @forelse($ringkasanUjian as $ujian)
                                    <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                            {{ $ujian['nama'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                            {{ $ujian['tanggal'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                            {{ $ujian['peserta'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 font-medium whitespace-nowrap">
                                            {{ $ujian['avg_nilai'] }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-emerald-600 whitespace-nowrap">
                                            {{ $ujian['kelulusan'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada data ujian yang tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Distribusi Role Pengguna (~40% / col-span-5) -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-6 shadow-none card-lift">
                <h2 class="text-base font-bold text-slate-900 mb-6">Distribusi Role Pengguna</h2>

                <div class="space-y-5">
                    @foreach($distribusiRole as $roleKey => $item)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <span class="font-semibold text-slate-900">{{ $item['nama'] }}</span>
                                <span class="text-slate-500 font-medium">{{ $item['count'] }} ({{ $item['persen'] }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="{{ $item['color'] }} h-2.5 rounded-full transition-all duration-700 ease-out" 
                                     style="width: {{ max(1, $item['persen']) }}%; min-width: 4px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
