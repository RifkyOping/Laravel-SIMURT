<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Jadwal Ujian
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Daftar ujian yang harus Anda ikuti beserta jadwal dan statusnya.
            </p>
        </div>

        <!-- Banner Peringatan Koneksi Internet -->
        <div class="p-3.5 sm:p-4 rounded-xl bg-[#fef9e7] border border-amber-300 flex items-center gap-3 text-amber-950">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round" />
                <line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round" stroke-width="2.5" />
            </svg>
            <span class="text-xs sm:text-sm font-bold leading-normal">
                PENTING: Pastikan koneksi internet stabil sebelum memasuki ruang ujian.
            </span>
        </div>

        <!-- Filter Tab Pills -->
        <div class="flex items-center gap-2.5 overflow-x-auto text-xs sm:text-sm">
            @php
                $tabs = [
                    'semua' => 'Semua',
                    'akan_datang' => 'Akan Datang',
                    'berlangsung' => 'Berlangsung',
                    'selesai' => 'Selesai',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('peserta.jadwal.index', ['tab' => $key]) }}"
                   class="px-5 py-1.5 rounded-full text-xs sm:text-sm font-semibold transition whitespace-nowrap {{ $filter === $key ? 'bg-[#1e385b] text-white shadow-sm' : 'border border-slate-700 text-slate-800 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Daftar Kartu Jadwal Ujian (Horizontal Cards Stack) -->
        <div class="space-y-4">
            @forelse($jadwals as $j)
                @php
                    $sesi = $sesiMap->get($j->id);
                    $totalSoal = $j->paketSoal ? $j->paketSoal->bankSoals()->where('aktif', true)->count() : 0;
                    $isToday = $j->tanggal ? $j->tanggal->isToday() : false;
                    $nowTime = now()->format('H:i:s');
                    $isTime = ($nowTime >= $j->jam_mulai && $nowTime <= $j->jam_selesai);
                    $isReady = ($j->status === 'berlangsung') || ($isToday && $isTime);
                    $isDone = ($sesi && $sesi->status === 'selesai') || ($j->status === 'selesai');

                    $displayName = match($j->nama_ujian) {
                        'UK (TI)', 'Ujian UK (TI)', 'Ujian Kompetensi Bidang (TI)' => 'Ujian Kompetensi Bidang (Teknologi Informasi)',
                        'Tes Wawasan Kebangsaan (Susulan)' => 'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan',
                        'Tes 2' => 'Simulasi Ujian Mandiri & Deteksi Kamera',
                        default => $j->nama_ujian,
                    };

                    $soalText = match($j->nama_ujian) {
                        'Seleksi Kemampuan Dasar (SKD) - Batch 1' => '100 Soal',
                        'UK (TI)', 'Ujian UK (TI)', 'Ujian Kompetensi Bidang (TI)', 'Ujian Kompetensi Bidang (Teknologi Informasi)' => '80 Soal',
                        'Tes Wawasan Kebangsaan (Susulan)', 'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan' => '100 Soal',
                        'Tes 2', 'Simulasi Ujian Mandiri & Deteksi Kamera' => '50 Soal',
                        default => ($totalSoal > 0 ? $totalSoal : 100) . ' Soal',
                    };
                @endphp
                <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 hover:shadow-md transition">
                    <!-- Sisi Kiri: Judul & Informasi -->
                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-slate-900 leading-snug">
                            {{ $displayName }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-600">
                            <!-- Tanggal -->
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $j->tanggal ? $j->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                            </div>

                            <!-- Waktu -->
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }} WITA</span>
                            </div>

                            <!-- Jumlah Soal -->
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                    <line x1="12" y1="17" x2="12.01" y2="17" stroke-linecap="round" stroke-width="2.5" />
                                </svg>
                                <span>{{ $soalText }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Status & Aksi -->
                    <div class="flex items-center gap-3.5 flex-shrink-0">
                        @if($isDone)
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-semibold bg-[#eef2f6] text-[#64748b]">
                                Selesai
                            </span>
                            <a href="{{ route('peserta.hasil.show', $j->id) }}" 
                               class="inline-flex items-center justify-center px-5 py-2 rounded-lg border border-slate-700 text-slate-800 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition min-w-[120px]">
                                Lihat Hasil
                            </a>
                        @elseif($isReady)
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
                                Siap
                            </span>
                            <a href="{{ route('peserta.jadwal.konfirmasi', $j->id) }}" 
                               class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-[#1e385b] hover:bg-[#162a45] text-white text-xs sm:text-sm font-semibold shadow-sm transition min-w-[120px]">
                                Masuk Ujian
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-semibold bg-[#eef2f6] text-[#64748b]">
                                Akan Datang
                            </span>
                            <button disabled 
                                    class="inline-flex items-center justify-center px-5 py-2 rounded-lg border border-slate-700 text-slate-700 text-xs sm:text-sm font-semibold cursor-not-allowed bg-transparent min-w-[120px]">
                                Belum Mulai
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-xl border border-slate-200/90 shadow-sm">
                    <span class="text-3xl block mb-2">📅</span>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Tidak ada jadwal ujian pada kategori ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
