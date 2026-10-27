<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Selamat Datang Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Selamat Datang, {{ $user->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Gunakan dashboard ini untuk melihat jadwal ujian aktif Anda dan mengakses hasil seleksi.
            </p>
        </div>

        <!-- Status Akun: TERVERIFIKASI -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-4 sm:p-5 flex items-start sm:items-center gap-3.5 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-[#eaf8ee] text-[#16a34a] flex items-center justify-center flex-shrink-0 border border-[#bbf7d0]/60">
                <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <div class="text-xs sm:text-sm font-bold text-[#15803d] tracking-wide">
                    Status Akun: TERVERIFIKASI
                </div>
                <p class="text-xs sm:text-sm text-slate-600 mt-0.5 leading-relaxed">
                    Akun Anda telah disetujui oleh panitia. Anda berhak mengikuti ujian sesuai jadwal di bawah ini.
                </p>
            </div>
        </div>

        <!-- Jadwal Ujian Mendatang -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-3">Jadwal Ujian Mendatang</h2>

            <div class="rounded-xl border border-slate-200/90 overflow-hidden bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 text-slate-800 border-b border-slate-200/90 font-bold text-xs sm:text-sm">
                                <th class="py-3 px-4">Nama Ujian</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($jadwals as $j)
                                @php
                                    $sesi = $sesiMap->get($j->id);
                                    $isToday = $j->tanggal ? $j->tanggal->isToday() : false;
                                    $nowTime = now()->format('H:i:s');
                                    $isTime = ($nowTime >= $j->jam_mulai && $nowTime <= $j->jam_selesai);
                                    $isReady = ($j->status === 'berlangsung') || ($isToday && $isTime);

                                    $displayName = match($j->nama_ujian) {
                                        'UK (TI)', 'Ujian UK (TI)', 'Ujian Kompetensi Bidang (TI)' => 'Ujian Kompetensi Bidang (Teknologi Informasi)',
                                        'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan' => 'Tes Wawasan Kebangsaan (Susulan)',
                                        default => $j->nama_ujian,
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        {{ $displayName }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">
                                        {{ $j->tanggal ? $j->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">
                                        {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }} WITA
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($sesi && $sesi->status === 'selesai')
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                Selesai
                                            </span>
                                        @elseif($isReady)
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
                                                Siap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-[#eef2f6] text-[#64748b]">
                                                Belum Mulai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($sesi && $sesi->status === 'selesai')
                                            <a href="{{ route('peserta.hasil.show', $j->id) }}" 
                                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                                                Lihat Hasil
                                            </a>
                                        @elseif($isReady)
                                            <a href="{{ route('peserta.jadwal.konfirmasi', $j->id) }}" 
                                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#1e385b] hover:bg-[#162a45] text-white font-semibold text-xs shadow-sm transition">
                                                Masuk Ujian
                                            </a>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">
                                                Belum Tersedia
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                        Belum ada jadwal ujian mendatang yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pengumuman Terbaru -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-3">Pengumuman Terbaru</h2>

            <div class="rounded-xl border border-slate-200/90 bg-[#f0f4f9] p-3 sm:p-4">
                @forelse($notifikasis as $n)
                    <div class="bg-white rounded-lg border border-slate-200/70 p-4 shadow-sm space-y-1 {{ !$loop->first ? 'mt-3' : '' }}">
                        <div class="text-xs text-slate-400 font-normal">
                            {{ $n->waktu ? $n->waktu->locale('id')->translatedFormat('d F Y') : '10 Oktober 2024' }} • {{ $n->judul ?: 'Sistem Manajemen Ujian' }}
                        </div>
                        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            {{ $n->isi }}
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-slate-200/70 p-4 shadow-sm space-y-1">
                        <div class="text-xs text-slate-400 font-normal">
                            10 Oktober 2024 • Sistem Manajemen Ujian
                        </div>
                        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            Pendaftaran Anda berhasil diverifikasi. Silahkan melihat jadwal anda.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
