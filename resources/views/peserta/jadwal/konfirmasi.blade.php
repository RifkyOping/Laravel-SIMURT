<x-app-layout>
    <x-slot name="title">
        Konfirmasi Masuk Ruang Ujian
    </x-slot>

    <div class="max-w-2xl mx-auto py-2 sm:py-4" x-data="{ agreed: false }">
        <!-- Header Section (Centered) -->
        <div class="text-center mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Konfirmasi Masuk Ruang Ujian
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Silakan periksa detail ujian dan peraturan sebelum memulai.
            </p>
        </div>

        <!-- Detail Ujian Table Box (Zebra-striped matching mockup) -->
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-200/80 shadow-2xs">
            <div class="px-5 py-3 flex items-center justify-between text-xs sm:text-sm bg-slate-50/90">
                <span class="text-slate-600 font-normal">Nama Ujian</span>
                <span class="font-bold text-slate-900 text-right">{{ $jadwal->nama_ujian }}</span>
            </div>
            <div class="px-5 py-3 flex items-center justify-between text-xs sm:text-sm bg-white">
                <span class="text-slate-600 font-normal">Tanggal</span>
                <span class="font-medium text-slate-900 text-right">{{ $jadwal->tanggal ? $jadwal->tanggal->locale('id')->translatedFormat('d F Y') : '-' }}</span>
            </div>
            <div class="px-5 py-3 flex items-center justify-between text-xs sm:text-sm bg-slate-50/90">
                <span class="text-slate-600 font-normal">Waktu</span>
                <span class="font-medium text-slate-900 text-right">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA</span>
            </div>
            <div class="px-5 py-3 flex items-center justify-between text-xs sm:text-sm bg-white">
                <span class="text-slate-600 font-normal">Durasi</span>
                <span class="font-medium text-slate-900 text-right">{{ $jadwal->durasi }} Menit</span>
            </div>
            <div class="px-5 py-3 flex items-center justify-between text-xs sm:text-sm bg-slate-50/90">
                <span class="text-slate-600 font-normal">Jumlah Soal</span>
                <span class="font-medium text-slate-900 text-right">{{ $totalSoal }} Soal</span>
            </div>
        </div>

        <!-- Peraturan Ujian Section -->
        <div class="mt-6 space-y-2.5">
            <h2 class="text-xs sm:text-sm font-bold text-slate-900">
                Peraturan Ujian:
            </h2>
            <ol class="space-y-1.5 text-xs sm:text-sm text-slate-600 leading-relaxed list-none">
                <li>1. Ujian terdiri dari soal pilihan ganda sesuai sub-materi.</li>
                <li>2. Setiap peserta mendapatkan urutan soal yang diacak secara sistematis.</li>
                <li>3. Jawaban Anda akan tersimpan otomatis setiap kali berpindah soal.</li>
                <li>4. Anda hanya memiliki satu kesempatan masuk; sesi tidak dapat diulang.</li>
                <li>5. Dilarang membuka tab browser baru, aplikasi lain, atau beralih layar.</li>
            </ol>
        </div>

        <!-- Divider Line -->
        <div class="border-t border-slate-200 my-6"></div>

        <!-- Checkbox Persetujuan -->
        <div>
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" 
                       x-model="agreed" 
                       class="w-4 h-4 rounded border-slate-300 text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 transition cursor-pointer">
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                    Saya telah membaca dan menyetujui peraturan ujian
                </span>
            </label>
        </div>

        <!-- Action Buttons (Side by Side) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <a href="{{ route('peserta.dashboard') }}" 
               class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-lg border border-slate-700 hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition tactile-btn">
                Kembali ke Dashboard
            </a>

            <a :href="agreed ? '{{ route('peserta.ujian.masuk', $jadwal->id) }}' : 'javascript:void(0)'" 
               :class="agreed ? 'bg-[#1e385b] hover:bg-[#152843] text-white cursor-pointer shadow-xs' : 'bg-[#1e385b]/40 text-white/70 cursor-not-allowed pointer-events-none'"
               class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-lg text-xs sm:text-sm font-semibold transition tactile-btn">
                Mulai Ujian
            </a>
        </div>

        <!-- Warning Callout Banner (Below Buttons) -->
        <div class="mt-4 p-3 rounded-lg bg-amber-50/80 border border-amber-200 text-amber-800 text-xs sm:text-sm font-medium flex items-center justify-center gap-2 text-center">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Pastikan koneksi internet Anda stabil sebelum memulai ujian.</span>
        </div>
    </div>
</x-app-layout>
