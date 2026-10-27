<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Hasil Ujian
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Berikut adalah nilai dari ujian yang telah Anda selesaikan beserta status seleksi Anda.
                </p>
            </div>
            <div>
                <a href="{{ route('peserta.hasil.index') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-700 text-slate-800 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                    ← Seluruh Hasil Ujian
                </a>
            </div>
        </div>

        @if(! $nilai)
            <!-- Status Masih Diproses (Mockup Screen Proses Hasil Ujian) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6 sm:p-9 max-w-xl mx-auto">
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 text-center mb-6 tracking-tight">
                    Hasil Ujian Sedang Diproses
                </h2>

                <div class="bg-slate-50/80 rounded-xl border border-slate-200/90 overflow-hidden divide-y divide-slate-200/80">
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Nama Ujian</span>
                        <span class="font-bold text-slate-900 text-right">{{ $jadwal->nama_ujian }}</span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Tanggal</span>
                        <span class="font-bold text-slate-900">
                            {{ $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Waktu</span>
                        <span class="font-bold text-slate-900">
                            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA
                        </span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Durasi</span>
                        <span class="font-bold text-slate-900">{{ $jadwal->durasi }} Menit</span>
                    </div>
                    <div class="px-5 py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-500 font-normal">Jumlah Soal</span>
                        <span class="font-bold text-slate-900">{{ $totalSoal }} Soal</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 my-6"></div>

                <div class="bg-[#fef9c3] border border-amber-300 rounded-lg py-3 px-4 flex items-center justify-center gap-2 text-amber-900 font-bold text-xs sm:text-sm shadow-2xs">
                    <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Silahkan Tunggu Beberapa Saat</span>
                </div>
            </div>
        @else
            <!-- Kartu Hasil Ujian Utama / Unggulan -->
            @php
                $isLulus = in_array(strtoupper($nilai->status_kelulusan), ['LULUS', 'LULUS SELEKSI']);
                $badgeText = $isLulus ? 'LULUS SELEKSI' : ($nilai->status_kelulusan === 'DISKUALIFIKASI' ? 'DISKUALIFIKASI' : 'TIDAK LULUS');
                $passingGradeVal = (int)($jadwal->passing_grade ?? 311);
                $passingGradeDisplay = $passingGradeVal > 0 ? $passingGradeVal : 311;
            @endphp

            <div class="bg-white rounded-xl border border-slate-700/80 p-6 sm:p-7 shadow-sm">
                <!-- Baris Atas: Judul Ujian, Tanggal, dan Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                            {{ $jadwal->nama_ujian }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Diselenggarakan pada: {{ $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        @if($isLulus)
                            <span class="inline-flex items-center justify-center px-3.5 py-1 rounded-md text-xs font-bold bg-[#dcfce7] text-[#15803d] tracking-wide uppercase">
                                {{ $badgeText }}
                            </span>
                        @elseif($nilai->status_kelulusan === 'DISKUALIFIKASI')
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
                            {{ (int)$nilai->nilai_akhir }}
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
                            <span class="font-bold text-slate-900">{{ $nilai->jumlah_benar }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Jawaban Salah:</span>
                            <span class="font-bold text-slate-900">{{ $nilai->jumlah_salah }}</span>
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
