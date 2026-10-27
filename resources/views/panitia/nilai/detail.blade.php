<x-app-layout>
    <x-slot name="header">
        Detail Hasil Ujian Peserta
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Detail Evaluasi</span>
                    <h1 class="text-lg font-bold text-slate-900">{{ $nilai->peserta->user->name }}</h1>
                    <p class="text-xs text-slate-500">{{ $nilai->jadwal->nama_ujian }} (NIK: {{ $nilai->peserta->no_identitas }})</p>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-bold uppercase
                    @if($nilai->status_kelulusan === 'LULUS') bg-emerald-100 text-emerald-800
                    @elseif($nilai->status_kelulusan === 'DISKUALIFIKASI') bg-rose-100 text-rose-800
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ $nilai->status_kelulusan }}
                </span>
            </div>

            <!-- Statistik Kartu -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Nilai Akhir</span>
                    <span class="text-2xl font-black text-blue-600 font-mono">{{ $nilai->nilai_akhir }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                    <span class="text-[10px] text-emerald-700 uppercase font-semibold block">Jawaban Benar</span>
                    <span class="text-2xl font-black text-emerald-700 font-mono">{{ $nilai->jumlah_benar }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-100">
                    <span class="text-[10px] text-rose-700 uppercase font-semibold block">Jawaban Salah</span>
                    <span class="text-2xl font-black text-rose-700 font-mono">{{ $nilai->jumlah_salah }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Tidak Dijawab</span>
                    <span class="text-2xl font-black text-slate-700 font-mono">{{ $nilai->jumlah_tidak_dijawab }}</span>
                </div>
            </div>

            <!-- Log Waktu Sesi Ujian (Rule 05-10) -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[10px]">Log Waktu Pelaksanaan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-600">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Waktu Mulai:</span>
                        <span class="font-mono font-semibold">{{ $nilai->sesiUjian?->waktu_masuk ? $nilai->sesiUjian->waktu_masuk->format('d/m/Y H:i:s') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Waktu Selesai:</span>
                        <span class="font-mono font-semibold">{{ $nilai->sesiUjian?->waktu_selesai ? $nilai->sesiUjian->waktu_selesai->format('d/m/Y H:i:s') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Status Sesi:</span>
                        <span class="font-semibold">{{ $nilai->sesiUjian?->status ?? 'Tercatat' }}</span>
                    </div>
                </div>
            </div>

            <!-- Catatan Pengawasan / Pelanggaran (Rule 05-10) -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Catatan Pengawasan & Pelanggaran</h3>
                @php
                    $pelanggarans = $nilai->peserta->pelanggarans()->where('jadwal_id', $nilai->jadwal_id)->get();
                @endphp

                @if($pelanggarans->isEmpty())
                    <p class="text-xs text-slate-400 italic">Tidak ada catatan pelanggaran selama ujian berlangsung (Integritas Baik).</p>
                @else
                    <div class="space-y-2">
                        @foreach($pelanggarans as $plg)
                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-xs text-rose-900 flex items-start justify-between">
                                <div>
                                    <span class="font-bold">{{ $plg->jenis_pelanggaran }}</span>
                                    <p class="text-[11px] text-rose-800 mt-0.5">{{ $plg->keterangan }}</p>
                                </div>
                                <span class="text-[10px] text-rose-500 font-mono">{{ $plg->waktu->format('H:i:s') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <a href="{{ route('panitia.nilai.index') }}" 
                   class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                    Tutup / Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
