<x-guest-layout>
    <div class="text-center py-2 space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mx-auto shadow-sm">
            🔒
        </div>

        <div>
            <h2 class="text-base font-bold text-slate-900">Akses Ruang Ujian Terkunci</h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                {{ $pesan ?? 'Sesi ujian Anda telah terputus atau kesempatan masuk pertama telah digunakan.' }}
            </p>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-left text-xs space-y-1.5">
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Ujian:</span>
                <span class="font-bold text-slate-800">{{ $jadwal->nama_ujian }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status Sesi:</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">
                    {{ str_replace('_', ' ', $sesi?->status ?? 'Terkunci') }}
                </span>
            </div>
        </div>

        @if($sesi && $sesi->status === 'menunggu_izin')
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs text-left space-y-2">
                <p class="font-bold flex items-center gap-1.5">
                    <span>⏳</span> Permintaan Izin Sedang Diproses
                </p>
                <p class="text-[11px] leading-relaxed">
                    Pengawas sedang meninjau pengajuan Anda. Silakan tunggu di halaman ini atau tekan tombol Segarkan secara berkala.
                </p>
                <a href="{{ route('peserta.ujian.masuk', $jadwal->id) }}" 
                   class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-xs hover:bg-blue-700 transition">
                    🔄 Cek Status / Masuk Kembali
                </a>
            </div>
        @else
            <!-- Form Pengajuan Izin -->
            <form method="POST" action="{{ route('peserta.ujian.minta-izin', $sesi?->id ?? 0) }}" class="text-left space-y-3 pt-2">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Terputus / Minta Izin</label>
                    <textarea name="alasan" required rows="3" placeholder="Contoh: Perangkat restart mendadak / koneksi kabel LAN terlepas"
                              class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"></textarea>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-amber-500/20">
                    Ajukan Izin Masuk Kembali ke Pengawas
                </button>
            </form>
        @endif

        <div class="pt-2 border-t border-slate-100">
            <a href="{{ route('peserta.dashboard') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 transition">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</x-guest-layout>
