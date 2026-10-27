<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Hasil Ujian
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Berikut adalah nilai dari ujian yang telah Anda selesaikan beserta status seleksi Anda.
            </p>
        </div>

        @if(! $featuredNilai)
            <div class="bg-white rounded-xl border border-slate-200/90 p-12 text-center space-y-3 shadow-sm">
                <span class="text-4xl block mb-2">📜</span>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Hasil Ujian</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                    Anda belum menyelesaikan ujian atau hasil ujian Anda masih dalam proses penilaian panitia.
                </p>
                <div class="pt-2">
                    <a href="{{ route('peserta.jadwal.index') }}" 
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#162a45] text-white text-xs sm:text-sm font-semibold shadow-sm transition">
                        Lihat Jadwal Ujian
                    </a>
                </div>
            </div>
        @else
            <!-- Kartu Hasil Ujian Utama / Unggulan -->
            @php
                $isLulus = in_array(strtoupper($featuredNilai->status_kelulusan), ['LULUS', 'LULUS SELEKSI']);
                $badgeText = $isLulus ? 'LULUS SELEKSI' : ($featuredNilai->status_kelulusan === 'DISKUALIFIKASI' ? 'DISKUALIFIKASI' : 'TIDAK LULUS');
                $passingGradeVal = (int)($jadwal->passing_grade ?? 311);
                $passingGradeDisplay = $passingGradeVal > 0 ? $passingGradeVal : 311;
            @endphp

            <div class="bg-white rounded-xl border border-slate-700/80 p-6 sm:p-7 shadow-sm">
                <!-- Baris Atas: Judul Ujian, Tanggal, dan Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                            {{ $jadwal ? $jadwal->nama_ujian : 'Seleksi Kemampuan Dasar (SKD) - Batch 1' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Diselenggarakan pada: {{ $jadwal && $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '15 Oktober 2025' }}
                        </p>
                    </div>

                    <div>
                        @if($isLulus)
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-bold bg-[#dcfce7] text-[#15803d] tracking-wide uppercase">
                                {{ $badgeText }}
                            </span>
                        @elseif($featuredNilai->status_kelulusan === 'DISKUALIFIKASI')
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-bold bg-[#fee2e2] text-[#dc2626] tracking-wide uppercase">
                                DISKUALIFIKASI
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-bold bg-[#fee2e2] text-[#dc2626] tracking-wide uppercase">
                                TIDAK LULUS
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-b border-slate-100 my-6"></div>

                <!-- Baris Bawah: Statistik Nilai (3 Kolom Bersekat) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <!-- Kolom 1: Nilai Akhir -->
                    <div class="md:border-r md:border-slate-200 md:pr-8">
                        <span class="text-xs font-medium text-slate-500 block">Nilai Akhir</span>
                        <span class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight block mt-1">
                            {{ (int)$featuredNilai->nilai_akhir }}
                        </span>
                    </div>

                    <!-- Kolom 2: Passing Grade & Jumlah Soal -->
                    <div class="space-y-3 md:border-r md:border-slate-200 md:px-8">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Passing Grade:</span>
                            <span class="font-bold text-slate-900">{{ $passingGradeDisplay }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Jumlah Soal:</span>
                            <span class="font-bold text-slate-900">{{ $totalSoal }} Soal</span>
                        </div>
                    </div>

                    <!-- Kolom 3: Jawaban Benar & Jawaban Salah -->
                    <div class="space-y-3 md:pl-8">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Jawaban Benar:</span>
                            <span class="font-bold text-slate-900">{{ $featuredNilai->jumlah_benar }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Jawaban Salah:</span>
                            <span class="font-bold text-slate-900">{{ $featuredNilai->jumlah_salah }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Hasil Seleksi Lainnya -->
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-3">Riwayat Hasil Seleksi Lainnya</h2>

                <div class="rounded-xl border border-slate-200/90 overflow-hidden bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-700 border-b border-slate-200/80 font-bold text-xs sm:text-sm">
                                    <th class="py-3.5 px-4 sm:px-6">Nama Ujian</th>
                                    <th class="py-3.5 px-4 sm:px-6">Tanggal</th>
                                    <th class="py-3.5 px-4 sm:px-6">Nilai Akhir</th>
                                    <th class="py-3.5 px-4 sm:px-6">Status Seleksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($riwayatLain as $r)
                                    @php
                                        $rIsLulus = in_array(strtoupper($r->status_kelulusan), ['LULUS', 'LULUS SELEKSI']);
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-4 px-4 sm:px-6 font-semibold text-slate-900">
                                            {{ $r->jadwal ? $r->jadwal->nama_ujian : 'Ujian Lain' }}
                                        </td>
                                        <td class="py-4 px-4 sm:px-6 text-slate-600 whitespace-nowrap">
                                            {{ $r->jadwal && $r->jadwal->tanggal ? $r->jadwal->tanggal->locale('id')->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="py-4 px-4 sm:px-6 font-bold text-slate-900 whitespace-nowrap">
                                            {{ (int)$r->nilai_akhir }}
                                        </td>
                                        <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                            @if($rIsLulus)
                                                <span class="inline-flex items-center justify-center px-3 py-0.5 rounded-md text-xs font-bold bg-[#dcfce7] text-[#15803d]">
                                                    LULUS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center px-3 py-0.5 rounded-md text-xs font-bold bg-[#fee2e2] text-[#dc2626]">
                                                    TIDAK LULUS
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                            Belum ada riwayat hasil seleksi lainnya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
