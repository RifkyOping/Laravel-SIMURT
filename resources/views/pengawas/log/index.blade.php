<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Log Aktivitas Ujian</h1>
            <p class="text-sm text-slate-500 mt-1">Riwayat seluruh aktivitas dan tindakan selama pengawasan ujian.</p>
        </div>

        <!-- Filter Dropdown Bar (Persis Mockup) -->
        <form id="filterLogForm" method="GET" action="{{ route('pengawas.log.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Filter Ujian -->
            <div class="relative inline-flex items-center bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-2xs hover:border-slate-300 transition">
                <span class="text-xs text-slate-500 mr-1.5 font-normal">Ujian:</span>
                <select name="jadwal_id" onchange="document.getElementById('filterLogForm').submit()" 
                        class="text-xs font-bold text-slate-800 bg-transparent border-none p-0 pr-5 focus:ring-0 focus:outline-none cursor-pointer">
                    @foreach($jadwals as $jd)
                        <option value="{{ $jd->id }}" {{ ($selectedJadwal && $selectedJadwal->id === $jd->id) ? 'selected' : '' }}>
                            {{ str_replace([' (SKD) - Batch 1', ' - Batch 1'], '', $jd->nama_ujian) }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Filter Jenis Aktivitas -->
            <div class="relative inline-flex items-center bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-2xs hover:border-slate-300 transition">
                <span class="text-xs text-slate-500 mr-1.5 font-normal">Jenis Aktivitas:</span>
                <select name="jenis_aktivitas" onchange="document.getElementById('filterLogForm').submit()" 
                        class="text-xs font-bold text-slate-800 bg-transparent border-none p-0 pr-5 focus:ring-0 focus:outline-none cursor-pointer">
                    <option value="Semua" {{ request('jenis_aktivitas', 'Semua') === 'Semua' ? 'selected' : '' }}>Semua</option>
                    <option value="Peringatan" {{ request('jenis_aktivitas') === 'Peringatan' ? 'selected' : '' }}>Peringatan</option>
                    <option value="Izin Masuk" {{ request('jenis_aktivitas') === 'Izin Masuk' ? 'selected' : '' }}>Izin Masuk</option>
                    <option value="Diskualifikasi" {{ request('jenis_aktivitas') === 'Diskualifikasi' ? 'selected' : '' }}>Diskualifikasi</option>
                    <option value="Sistem" {{ request('jenis_aktivitas') === 'Sistem' ? 'selected' : '' }}>Sistem</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Filter Tanggal -->
            <div class="relative inline-flex items-center bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-2xs hover:border-slate-300 transition">
                <span class="text-xs text-slate-500 mr-1.5 font-normal">Tanggal:</span>
                <select name="tanggal" onchange="document.getElementById('filterLogForm').submit()" 
                        class="text-xs font-bold text-slate-800 bg-transparent border-none p-0 pr-5 focus:ring-0 focus:outline-none cursor-pointer">
                    <option value="2024-10-15" {{ request('tanggal', '2024-10-15') === '2024-10-15' ? 'selected' : '' }}>15 Okt 2024</option>
                    <option value="{{ now()->toDateString() }}" {{ request('tanggal') === now()->toDateString() ? 'selected' : '' }}>Hari Ini ({{ now()->translatedFormat('d M Y') }})</option>
                    <option value="" {{ request('tanggal') === '' ? 'selected' : '' }}>Semua Tanggal</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </form>

        <!-- Tabel Log Aktivitas (Persis Mockup) -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4 w-28">Waktu</th>
                            <th class="py-3 px-4 w-44">Peserta</th>
                            <th class="py-3 px-4 w-36">Jenis Aktivitas</th>
                            <th class="py-3 px-4">Detail Aktivitas</th>
                            <th class="py-3 px-4 w-32">Tindakan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($logs as $index => $log)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                    {{ $logs->firstItem() + $index }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono">
                                    {{ $log->waktu->format('H:i:s') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $log->peserta->user->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $jenis = $log->jenis_pelanggaran;
                                    @endphp
                                    @if(str_contains($jenis, 'Peringatan'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#fef3c7] text-[#92400e]">
                                            Peringatan
                                        </span>
                                    @elseif(str_contains($jenis, 'Izin'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#d1fae5] text-[#065f46]">
                                            Izin Masuk
                                        </span>
                                    @elseif(str_contains($jenis, 'Diskualifikasi'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#fee2e2] text-[#991b1b]">
                                            Diskualifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#e0f2fe] text-[#0369a1]">
                                            Sistem
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $log->keterangan }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    {{ $log->tindakan ?? ($log->user ? 'Budi S.' : 'Sistem') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada log aktivitas untuk kriteria ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ $logs->total() }} entries
                    </div>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
