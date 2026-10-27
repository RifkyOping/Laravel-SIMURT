<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Monitoring Ujian Real-Time</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar ruangan ujian yang ditugaskan kepada Anda. Pilih ruangan dan klik <strong>Masuk</strong> untuk memantau aktivitas peserta secara langsung.</p>
        </div>

        <!-- Bar Filter Status & Pencarian -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Filter Tabs Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                @php
                    $tabs = [
                        'semua' => ['label' => 'Semua', 'count' => $counts['semua']],
                        'berlangsung' => ['label' => 'Berlangsung', 'count' => $counts['berlangsung']],
                        'akan_datang' => ['label' => 'Akan Datang', 'count' => $counts['akan_datang']],
                        'selesai' => ['label' => 'Selesai', 'count' => $counts['selesai']],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    @php
                        $isActive = ($statusFilter === $key);
                    @endphp
                    <a href="{{ route('pengawas.monitoring.index', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $isActive ? 'bg-[#1e385b] text-white shadow-xs' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}">
                        @if($key === 'berlangsung')
                            <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-emerald-300' : 'bg-emerald-500' }} animate-pulse"></span>
                        @endif
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Input Pencarian Nama Ujian -->
            <form method="GET" action="{{ route('pengawas.monitoring.index') }}" class="flex items-center gap-2 w-full md:w-80">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-linecap="round" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama ujian atau paket..." 
                           class="w-full pl-9 pr-8 py-2 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1e385b] focus:border-[#1e385b] bg-white placeholder:text-slate-400 shadow-2xs transition" />
                    @if(request('search'))
                        <a href="{{ route('pengawas.monitoring.index', array_merge(request()->query(), ['search' => null])) }}"
                           class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </a>
                    @endif
                </div>
                <button type="submit" 
                        class="px-3.5 py-2 rounded-lg bg-[#1e385b] text-white text-xs font-semibold hover:bg-[#152843] transition shadow-xs flex-shrink-0">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Ruangan Ujian -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Nama Ujian & Paket Soal</th>
                            <th class="py-3.5 px-4">Tanggal Pelaksanaan</th>
                            <th class="py-3.5 px-4">Waktu & Durasi (WITA)</th>
                            <th class="py-3.5 px-4">Peserta Terdaftar</th>
                            <th class="py-3.5 px-4">Status Ruangan</th>
                            <th class="py-3.5 px-4 text-center">Aksi Pengawasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($jadwals as $index => $j)
                            @php
                                $isBerlangsung = ($j->status === 'berlangsung');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-4 text-center text-slate-500 font-medium">
                                    {{ $jadwals->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 text-sm leading-snug">
                                        {{ $j->nama_ujian }}
                                    </div>
                                    <div class="text-slate-500 text-[11px] mt-0.5">
                                        Paket: {{ $j->paketSoal?->nama_paket ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-700 font-medium">
                                    {{ $j->tanggal ? $j->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    <div class="font-mono font-semibold text-slate-800">
                                        {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }} WITA
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Durasi: {{ $j->durasi }} Menit
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ $j->pesertas_count }} Peserta
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($isBerlangsung)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Berlangsung
                                        </span>
                                    @elseif($j->status === 'akan_datang')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                            Akan Datang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('pengawas.monitoring.show', $j->id) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition shadow-xs {{ $isBerlangsung ? 'bg-[#1e385b] hover:bg-[#152843] text-white' : 'border border-slate-300 hover:bg-slate-50 text-slate-800' }}">
                                        <span>Masuk</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <rect x="2" y="3" width="20" height="14" rx="2" />
                                            <line x1="8" y1="21" x2="16" y2="21" />
                                            <line x1="12" y1="17" x2="12" y2="21" />
                                        </svg>
                                    </div>
                                    <p class="font-medium text-slate-600">Tidak ada jadwal ujian yang ditemukan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter status atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($jadwals->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $jadwals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
