<x-app-layout>
    <x-slot name="title">
        Konfirmasi Masuk Ruang Ujian
    </x-slot>

    <div class="max-w-3xl mx-auto py-2 sm:py-4" x-data="confirmationApp()">
        <!-- Header Section (Centered) -->
        <div class="text-center mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Konfirmasi Masuk Ruang Ujian
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Silakan periksa detail ujian dan uji perangkat kamera & layar Anda sebelum memulai.
            </p>
        </div>

        <!-- Detail Ujian Table Box (Zebra-striped matching mockup) -->
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-200/80 shadow-2xs mb-6">
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

        <!-- Card Pemeriksaan Perangkat & Izin Pengawasan -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-2xs mb-6 space-y-4">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e385b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        Pemeriksaan Perangkat & Izin Pengawasan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Uji izin kamera dan layar sekarang agar perizinan tersimpan di browser dan tidak memicu deteksi pelanggaran (jendela kehilangan fokus) saat ujian berlangsung.
                    </p>
                </div>
            </div>

            <!-- Grid 2 Kolom: Kamera & Layar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kolom 1: Kamera Peserta (Wajib) -->
                <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/50 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5l-3.75 3.75m0 0l-3.75 3.75m3.75-3.75l3.75 3.75m-3.75-3.75l-3.75-3.75M3 13.5v-3a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 10.5v3a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 13.5z" />
                            </svg>
                            Kamera Peserta <span class="text-rose-500">*Wajib</span>
                        </span>

                        <span x-show="cameraStatus === 'granted'" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Terverifikasi
                        </span>
                        <span x-show="cameraStatus === 'checking'" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Memeriksa...
                        </span>
                        <span x-show="cameraStatus === 'denied'" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Perlu Izin
                        </span>
                        <span x-show="cameraStatus === 'idle'" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-200 text-slate-700">
                            Belum Diuji
                        </span>
                    </div>

                    <!-- Video Preview Container -->
                    <div class="w-full h-44 bg-slate-950 rounded-lg overflow-hidden border border-slate-800 flex items-center justify-center relative shadow-inner">
                        <video id="cameraPreviewVideo" 
                               autoplay 
                               playsinline 
                               muted 
                               x-show="cameraStatus === 'granted'" 
                               class="w-full h-full object-cover" 
                               style="transform: scaleX(-1);"></video>

                        <!-- Idle State -->
                        <div x-show="cameraStatus === 'idle'" class="text-center p-3 text-slate-400 space-y-2">
                            <svg class="w-8 h-8 mx-auto text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <p class="text-[11px] leading-tight text-slate-400">
                                Kamera belum diaktifkan. Klik tombol di bawah untuk memberikan izin.
                            </p>
                        </div>

                        <!-- Checking State -->
                        <div x-show="cameraStatus === 'checking'" class="text-center p-3 text-slate-300 space-y-2">
                            <svg class="w-7 h-7 mx-auto animate-spin text-amber-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <p class="text-[11px] leading-tight text-amber-300">Menghubungkan sensor kamera...</p>
                        </div>

                        <!-- Denied State -->
                        <div x-show="cameraStatus === 'denied'" class="p-3 text-center space-y-2">
                            <svg class="w-7 h-7 mx-auto text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-[10.5px] leading-snug text-rose-200" x-text="cameraErrorMessage"></p>
                        </div>

                        <!-- Watermark Ready Badge -->
                        <div x-show="cameraStatus === 'granted'" class="absolute top-2 left-2 bg-emerald-600/90 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow-xs tracking-wider flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            KAMERA SIAP
                        </div>
                    </div>

                    <!-- Button Uji Kamera -->
                    <div>
                        <button type="button" 
                                @click="requestCamera()"
                                :disabled="cameraStatus === 'checking'"
                                :class="cameraStatus === 'granted' ? 'border border-slate-300 bg-white hover:bg-slate-100 text-slate-700' : 'bg-[#1e385b] hover:bg-[#152843] text-white'"
                                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-semibold transition tactile-btn cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <span x-text="cameraStatus === 'granted' ? 'Uji Ulang Kamera' : (cameraStatus === 'checking' ? 'Menghubungkan...' : 'Izinkan & Uji Kamera')"></span>
                        </button>
                    </div>
                </div>

                <!-- Kolom 2: Izin Berbagi Layar (Laptop/PC) atau Info Mobile -->
                <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/50 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                            </svg>
                            Pemantauan Layar
                        </span>

                        <template x-if="isMobile">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                Mode HP
                            </span>
                        </template>

                        <template x-if="!isMobile">
                            <div>
                                <span x-show="screenStatus === 'granted'" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Izin Layar Siap
                                </span>
                                <span x-show="screenStatus === 'checking'" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                    Memilih...
                                </span>
                                <span x-show="screenStatus === 'denied'" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Dibatalkan
                                </span>
                                <span x-show="screenStatus === 'idle'" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-200 text-slate-700">
                                    Dianjurkan
                                </span>
                            </div>
                        </template>
                    </div>

                    <!-- Screen Preview Container -->
                    <div class="w-full h-44 bg-slate-950 rounded-lg overflow-hidden border border-slate-800 flex items-center justify-center relative shadow-inner">
                        <!-- Mode Mobile (HP) Info -->
                        <div x-show="isMobile" class="text-center p-3 text-slate-300 space-y-2">
                            <svg class="w-8 h-8 mx-auto text-blue-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                            <p class="text-[11px] leading-relaxed text-slate-400">
                                Anda terdeteksi menggunakan perangkat HP/Ponsel. Pengawasan dilakukan secara penuh melalui sensor kamera depan.
                            </p>
                        </div>

                        <!-- Mode Laptop/PC -->
                        <template x-if="!isMobile">
                            <div class="w-full h-full flex items-center justify-center">
                                <video id="screenPreviewVideo" 
                                       autoplay 
                                       playsinline 
                                       muted 
                                       x-show="screenStatus === 'granted'" 
                                       class="w-full h-full object-contain"></video>

                                <!-- Idle State -->
                                <div x-show="screenStatus === 'idle'" class="text-center p-3 text-slate-400 space-y-2">
                                    <svg class="w-8 h-8 mx-auto text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                    </svg>
                                    <p class="text-[11px] leading-tight text-slate-400">
                                        Uji izin berbagi layar sekarang agar saat pengawas memantau, tidak memicu pop-up kehilangan fokus.
                                    </p>
                                </div>

                                <!-- Checking State -->
                                <div x-show="screenStatus === 'checking'" class="text-center p-3 text-slate-300 space-y-2">
                                    <svg class="w-7 h-7 mx-auto animate-spin text-amber-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <p class="text-[11px] leading-tight text-amber-300">Pilih "Seluruh Layar (Entire Screen)" pada jendela browser...</p>
                                </div>

                                <!-- Denied State -->
                                <div x-show="screenStatus === 'denied'" class="p-3 text-center space-y-2">
                                    <svg class="w-7 h-7 mx-auto text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-[10.5px] leading-snug text-amber-200" x-text="screenErrorMessage"></p>
                                </div>

                                <!-- Watermark Ready Badge -->
                                <div x-show="screenStatus === 'granted'" class="absolute top-2 left-2 bg-emerald-600/90 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow-xs tracking-wider flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    LAYAR SIAP
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Button Uji Layar (Hanya di Laptop) -->
                    <div>
                        <template x-if="!isMobile">
                            <button type="button" 
                                    @click="requestScreenShare()"
                                    :disabled="screenStatus === 'checking'"
                                    :class="screenStatus === 'granted' ? 'border border-slate-300 bg-white hover:bg-slate-100 text-slate-700' : 'border border-slate-300 bg-white hover:bg-slate-100 text-slate-800'"
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-semibold transition tactile-btn cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                </svg>
                                <span x-text="screenStatus === 'granted' ? 'Uji Ulang Layar' : (screenStatus === 'checking' ? 'Memilih Layar...' : 'Uji Izin Berbagi Layar')"></span>
                            </button>
                        </template>
                        <template x-if="isMobile">
                            <div class="py-2 text-center text-xs text-slate-500 font-medium">
                                Tidak membutuhkan izin layar di HP
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peraturan Ujian Section -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-2xs space-y-3 mb-6">
            <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                Peraturan & Tata Tertib Ujian:
            </h2>
            <ol class="space-y-1.5 text-xs sm:text-sm text-slate-600 leading-relaxed list-none">
                <li>1. Ujian terdiri dari soal pilihan ganda sesuai sub-materi.</li>
                <li>2. Setiap peserta mendapatkan urutan soal yang diacak secara sistematis.</li>
                <li>3. Jawaban Anda akan tersimpan otomatis setiap kali berpindah soal.</li>
                <li>4. Anda hanya memiliki satu kesempatan masuk; sesi tidak dapat diulang.</li>
                <li>5. Dilarang membuka tab browser baru, aplikasi lain, atau beralih layar.</li>
                <li>6. Kamera harus tetap aktif dan wajah peserta harus terlihat jelas selama ujian.</li>
            </ol>
        </div>

        <!-- Checkbox Persetujuan -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs mb-6">
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" 
                       x-model="agreed" 
                       class="w-4 h-4 rounded border-slate-300 text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 transition cursor-pointer">
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                    Saya telah membaca peraturan ujian dan memastikan kamera serta perangkat saya telah siap.
                </span>
            </label>
        </div>

        <!-- Action Buttons (Side by Side) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('peserta.dashboard') }}" 
               @click="stopAllStreams()"
               class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition tactile-btn">
                Kembali ke Dashboard
            </a>

            <button type="button" 
                    @click="proceedToExam('{{ route('peserta.ujian.masuk', $jadwal->id) }}')" 
                    :disabled="!canStartExam"
                    :class="canStartExam ? 'bg-[#1e385b] hover:bg-[#152843] text-white cursor-pointer shadow-xs' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs sm:text-sm font-semibold transition tactile-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
                <span>Mulai Ujian Sekarang</span>
            </button>
        </div>

        <!-- Helper Notice jika Belum Siap -->
        <div x-show="!canStartExam" class="mt-3 text-center">
            <p class="text-xs text-amber-700 font-medium">
                <span x-show="cameraStatus !== 'granted'">* Harap izinkan dan aktifkan kamera di atas terlebih dahulu. </span>
                <span x-show="!agreed">* Centang kotak persetujuan tata tertib untuk melanjutkan.</span>
            </p>
        </div>
    </div>

    <!-- Script Pengujian Kamera & Layar Sebelum Ujian -->
    <script>
        function confirmationApp() {
            return {
                agreed: false,
                isMobile: false,
                supportsScreenShare: false,
                isSecure: window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1',
                
                // Status Kamera
                cameraStatus: 'idle', // 'idle' | 'checking' | 'granted' | 'denied'
                cameraStream: null,
                cameraErrorMessage: '',
                
                // Status Layar
                screenStatus: 'idle', // 'idle' | 'checking' | 'granted' | 'denied'
                screenStream: null,
                screenErrorMessage: '',

                get canStartExam() {
                    return this.agreed && this.cameraStatus === 'granted';
                },

                init() {
                    this.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    this.supportsScreenShare = !this.isMobile && !!(navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia);

                    // Cek otomatis apakah izin kamera sudah pernah diberikan sebelumnya
                    if (navigator.permissions && navigator.permissions.query) {
                        navigator.permissions.query({ name: 'camera' }).then((perm) => {
                            if (perm.state === 'granted') {
                                this.requestCamera();
                            }
                        }).catch(() => {});
                    }
                },

                async requestCamera() {
                    this.cameraStatus = 'checking';
                    this.cameraErrorMessage = '';

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraStatus = 'denied';
                        this.cameraErrorMessage = !this.isSecure 
                            ? 'Akses kamera diblokir browser di jaringan HTTP lokal. Aktifkan flag "Insecure origins treated as secure" di browser (chrome://flags atau brave://flags).'
                            : 'Perangkat atau browser Anda tidak mendukung akses sensor kamera.';
                        return;
                    }

                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                width: { ideal: 640 },
                                height: { ideal: 480 },
                                facingMode: 'user'
                            },
                            audio: false
                        });

                        this.cameraStream = stream;
                        this.cameraStatus = 'granted';

                        this.$nextTick(() => {
                            const videoEl = document.getElementById('cameraPreviewVideo');
                            if (videoEl) {
                                videoEl.srcObject = stream;
                                videoEl.play().catch(() => {});
                            }
                        });
                    } catch (err) {
                        this.cameraStatus = 'denied';
                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                            this.cameraErrorMessage = 'Izin kamera ditolak. Klik ikon gembok / kamera di sebelah kiri kolom URL browser, ubah menjadi "Izinkan (Allow)", lalu coba lagi.';
                        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                            this.cameraErrorMessage = 'Kamera tidak ditemukan. Pastikan webcam terpasang dan tidak digunakan aplikasi lain.';
                        } else {
                            this.cameraErrorMessage = !this.isSecure 
                                ? 'Kamera tidak dapat diakses pada HTTP lokal. Butuh izin flag browser "Insecure origins treated as secure".'
                                : ('Gagal mengakses kamera: ' + (err.message || 'Error tidak diketahui'));
                        }
                    }
                },

                async requestScreenShare() {
                    if (!this.supportsScreenShare) return;

                    this.screenStatus = 'checking';
                    this.screenErrorMessage = '';

                    try {
                        const stream = await navigator.mediaDevices.getDisplayMedia({
                            video: {
                                cursor: 'always',
                                frameRate: { ideal: 15, max: 30 }
                            },
                            audio: false
                        });

                        this.screenStream = stream;
                        this.screenStatus = 'granted';

                        this.$nextTick(() => {
                            const videoEl = document.getElementById('screenPreviewVideo');
                            if (videoEl) {
                                videoEl.srcObject = stream;
                                videoEl.play().catch(() => {});
                            }
                        });

                        stream.getVideoTracks().forEach(track => {
                            track.onended = () => {
                                this.screenStatus = 'idle';
                                this.screenStream = null;
                            };
                        });
                    } catch (err) {
                        this.screenStatus = 'denied';
                        if (err.name === 'NotAllowedError') {
                            this.screenErrorMessage = 'Izin berbagi layar dibatalkan. Pastikan Anda memilih "Seluruh Layar (Entire Screen)".';
                        } else {
                            this.screenErrorMessage = 'Gagal mengakses izin layar: ' + (err.message || '');
                        }
                    }
                },

                stopAllStreams() {
                    if (this.cameraStream) {
                        this.cameraStream.getTracks().forEach(t => t.stop());
                        this.cameraStream = null;
                    }
                    if (this.screenStream) {
                        this.screenStream.getTracks().forEach(t => t.stop());
                        this.screenStream = null;
                    }
                },

                proceedToExam(url) {
                    if (!this.canStartExam) return;
                    // Hentikan stream uji coba agar webcam dilepas sebelum ruang ujian mengambil akses eksklusif
                    this.stopAllStreams();
                    window.location.href = url;
                }
            }
        }
    </script>
</x-app-layout>
