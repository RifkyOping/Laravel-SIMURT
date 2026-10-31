<x-exam-layout>
    <div x-data="examApp()" x-init="init()" class="flex flex-col h-screen overflow-hidden bg-white">
        <!-- Header Ujian (Fixed Top - Brand Navy #1e385b) -->
        <header class="h-16 bg-[#1e385b] text-white px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-40 shadow-xs">
            <div class="truncate mr-2">
                <h1 class="text-sm sm:text-base md:text-lg font-bold text-white tracking-tight leading-tight truncate">
                    {{ $jadwal->nama_ujian }}
                </h1>
                <p class="text-[11px] sm:text-xs text-white/80 mt-0.5 font-normal truncate">
                    Peserta: {{ Auth::user()->name }} (No. Ujian: {{ Auth::user()->peserta?->no_ujian ?? Auth::user()->peserta?->no_identitas ?? '-' }})
                </p>
            </div>

            <!-- Kontrol Kanan Header: Tombol Navigasi Mobile & Timer -->
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <!-- Tombol Navigasi Soal Khusus Mobile -->
                <button type="button" 
                        @click="showMobileNav = true" 
                        class="lg:hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                    </svg>
                    <span>Soal (<span x-text="countAnswered()"></span>/<span x-text="questions.length"></span>)</span>
                </button>

                <!-- Sisa Waktu Badge (White pill with red border and text) -->
                <div class="bg-white text-rose-600 px-2.5 sm:px-3.5 py-1.5 rounded-lg border border-rose-200 shadow-xs flex items-center gap-1.5 sm:gap-2 font-bold text-xs sm:text-sm font-mono">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span><span class="hidden sm:inline">Sisa Waktu: </span><span x-text="formatTimer(remainingSeconds)">01:30:00</span></span>
                </div>
            </div>
        </header>

        <!-- Floating Warning Toast (if violation detected) -->
        <div x-show="warningCount > 0" 
             x-cloak
             class="bg-amber-500 text-slate-950 px-4 py-2 text-xs font-bold text-center z-30 flex items-center justify-center gap-2 shadow-xs transition">
            <svg class="w-4 h-4 text-slate-950 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>PERINGATAN INTEGRITAS: Anda memiliki <span x-text="warningCount"></span> dari maksimal 3 peringatan pelanggaran. Jangan tinggalkan halaman ujian!</span>
        </div>

        <!-- Main Exam Area (Split Panel matching Mockup) -->
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0 bg-white">
            
            <!-- Panel Kiri: Navigasi Soal (Desktop View) -->
            <aside class="hidden lg:flex w-80 flex-shrink-0 p-6 border-r border-slate-200 flex-col overflow-y-auto">
                <h2 class="text-sm font-bold text-slate-900 mb-4 tracking-tight">
                    Navigasi Soal
                </h2>

                <!-- Grid Nomor Soal (6 Kolom persis seperti mockup) -->
                <div class="grid grid-cols-6 gap-2">
                    @foreach($soals as $index => $soal)
                        <button type="button" 
                                @click="goToQuestion({{ $index }})" 
                                :class="getQuestionBtnClass({{ $index }}, {{ $soal->id }})"
                                class="h-9 w-full rounded-md text-xs flex items-center justify-center transition cursor-pointer select-none">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <!-- Divider -->
                <div class="border-t border-slate-200 my-5"></div>

                <!-- Legenda Status (Persis seperti mockup) -->
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded border-2 border-slate-800 bg-[#e6f4ea] flex-shrink-0"></span>
                        <span class="font-normal text-slate-700">Soal Aktif</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded border border-slate-300 bg-[#f1f3f4] flex-shrink-0"></span>
                        <span class="font-normal text-slate-700">Sudah Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded bg-[#1e385b] border border-[#1e385b] flex-shrink-0"></span>
                        <span class="font-normal text-slate-700">Belum Dijawab</span>
                    </div>
                </div>

                <!-- Autosave Subtle Indicator at Bottom -->
                <div class="mt-auto pt-6 text-[11px] flex items-center gap-1.5 text-slate-400">
                    <span class="w-2 h-2 rounded-full" :class="saveStatusColor === 'text-emerald-600' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    <span x-text="saveStatusText">Tersimpan Otomatis</span>
                </div>
            </aside>

            <!-- Panel Kanan: Lembar Pertanyaan & Pilihan Jawaban -->
            <main class="flex-1 p-4 sm:p-8 lg:p-10 flex flex-col justify-between overflow-y-auto">
                <div class="max-w-4xl mx-auto w-full">
                    <!-- Header Soal: Nomor Soal & Status Terjawab -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 sm:border-transparent">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" 
                            x-text="'Soal No. ' + (currentIndex + 1)">
                            Soal No. 1
                        </h2>

                        <!-- Status Terjawab Circle Indicator -->
                        <div class="flex items-center gap-1.5 text-xs font-normal">
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0"
                                  :class="hasAnswer(currentQuestion.id) ? 'border-2 border-slate-800' : 'border border-slate-400'">
                                <span x-show="hasAnswer(currentQuestion.id)" class="w-2 h-2 rounded-full bg-slate-800"></span>
                            </span>
                            <span class="text-slate-700 text-xs" x-text="hasAnswer(currentQuestion.id) ? 'Sudah Terjawab' : 'Belum Terjawab'">
                                Belum Terjawab
                            </span>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal -->
                    <div class="text-sm sm:text-base font-normal text-slate-800 leading-relaxed my-4 sm:my-5" 
                         x-text="currentQuestion.pertanyaan">
                    </div>

                    <!-- Pilihan Jawaban (Radio Style matching mockup, finger-friendly) -->
                    <div class="space-y-3 sm:space-y-3.5 my-4 sm:my-6">
                        <template x-for="(text, optKey) in currentQuestion.opsi_jawaban" :key="optKey">
                            <label @click="selectAnswer(optKey)" 
                                   class="flex items-start gap-3 p-2.5 sm:p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer select-none group transition">
                                <span class="mt-0.5 w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0 transition"
                                      :class="answers[currentQuestion.id] === optKey ? 'border-2 border-slate-800' : 'border border-slate-400 group-hover:border-slate-600'">
                                    <span x-show="answers[currentQuestion.id] === optKey" 
                                          class="w-2 h-2 rounded-full bg-slate-800"></span>
                                </span>
                                <span class="text-xs sm:text-sm text-slate-800 leading-relaxed font-normal group-hover:text-slate-950">
                                    <strong class="font-semibold" x-text="optKey + '.'"></strong>
                                    <span x-text="' ' + text"></span>
                                </span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Bottom Navigation & Action Bar -->
                <div class="border-t border-slate-200 pt-4 sm:pt-6 mt-6 sm:mt-8 max-w-4xl mx-auto w-full">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                        <!-- Left Buttons: Sebelumnya & Simpan & Lanjut -->
                        <div class="flex items-center gap-2.5 sm:gap-3 w-full sm:w-auto">
                            <button type="button" 
                                    @click="prevQuestion()" 
                                    :disabled="currentIndex === 0"
                                    class="flex-1 sm:flex-initial px-4 sm:px-5 py-2.5 rounded-lg border border-slate-700 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition tactile-btn disabled:opacity-40 disabled:cursor-not-allowed">
                                Sebelumnya
                            </button>

                            <button type="button" 
                                    @click="nextQuestion()" 
                                    class="flex-1 sm:flex-initial px-4 sm:px-5 py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn">
                                Simpan & Lanjut
                            </button>
                        </div>

                        <!-- Right Side: Warning Note & Selesai Ujian -->
                        <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="text-right hidden md:block">
                                <span class="text-xs font-semibold text-slate-700 block leading-tight">Periksa kembali seluruh jawaban Anda.</span>
                                <span class="text-[11px] text-slate-400 block leading-tight mt-0.5">Aksi selesai tidak bisa dibatalkan.</span>
                            </div>

                            <button type="button" 
                                    @click="showFinishModal = true" 
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-slate-700 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition tactile-btn cursor-pointer whitespace-nowrap text-center">
                                Selesai Ujian
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Drawer / Modal Navigasi Soal Khusus Mobile -->
        <div x-show="showMobileNav" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 lg:hidden">
            <div @click.away="showMobileNav = false" 
                 class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg max-h-[85vh] p-5 flex flex-col shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Navigasi Soal Ujian</h3>
                        <p class="text-[11px] text-slate-500">Pilih nomor soal untuk langsung menuju ke soal tersebut.</p>
                    </div>
                    <button type="button" @click="showMobileNav = false" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-5 sm:grid-cols-6 gap-2 overflow-y-auto py-1 max-h-[50vh]">
                    <template x-for="(soal, index) in questions" :key="soal.id || index">
                        <button type="button" 
                                @click="goToQuestion(index); showMobileNav = false;" 
                                :class="getQuestionBtnClass(index, soal.id)"
                                class="h-10 w-full rounded-lg text-xs font-semibold flex items-center justify-center transition">
                            <span x-text="index + 1"></span>
                        </button>
                    </template>
                </div>
                <div class="pt-4 border-t border-slate-200 mt-4 flex items-center justify-between text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-[#e6f4ea] border-2 border-slate-800"></span>
                        <span>Aktif</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-[#f1f3f4] border border-slate-300"></span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-[#1e385b]"></span>
                        <span>Belum</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay Wajib Fullscreen (Desktop & Android) -->
        <div x-show="showFullscreenOverlay" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border border-slate-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Wajib Mode Layar Penuh</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                        Anda terdeteksi keluar dari mode layar penuh. Untuk menjaga integritas ujian, silakan kembali ke mode layar penuh.
                    </p>
                </div>
                <button type="button" 
                        @click="enterFullscreen()"
                        class="w-full py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold transition shadow-xs tactile-btn cursor-pointer">
                    Kembali ke Layar Penuh
                </button>
            </div>
        </div>

        <!-- Modal Peringatan Pelanggaran Bertingkat (3-Strikes Warning) -->
        <div x-show="showViolationModal" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border border-amber-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 mb-2"
                          x-text="'Peringatan Integritas: ' + warningCount + ' dari 3'">
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Aktivitas Mencurigakan Terdeteksi!
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed" x-text="lastViolationMessage">
                    </p>
                    <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-800 text-left space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-rose-900">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Batas Toleransi: Maksimal 3 Peringatan
                        </div>
                        <p>
                            Jika Anda terdeteksi melanggar sebanyak 3 kali (berpindah tab, keluar aplikasi, atau membuka jendela lain), ujian Anda akan <strong>terkunci secara otomatis</strong> dan Anda harus meminta izin kepada pengawas.
                        </p>
                    </div>
                </div>
                <button type="button" 
                        @click="showViolationModal = false"
                        class="w-full py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold transition shadow-xs tactile-btn cursor-pointer">
                    Saya Mengerti & Lanjutkan Ujian
                </button>
            </div>
        </div>

        <!-- Modal Teguran Langsung dari Pengawas Ujian (SIMURT Design System) -->
        <div x-show="showProctorWarningModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border-2 border-amber-400">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 mb-2 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        Teguran Pengawas: Peringatan <span x-text="warningCount"></span> dari 3
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Teguran dari Pengawas Ujian!
                    </h3>
                    <div class="mt-3 p-3.5 rounded-xl bg-amber-50/90 border border-amber-200 text-left">
                        <div class="text-[11px] font-semibold text-amber-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            Pesan dari Pengawas:
                        </div>
                        <p class="text-xs sm:text-sm text-slate-800 font-medium leading-relaxed" x-text="proctorWarningMessage"></p>
                    </div>
                    <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-800 text-left space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-rose-900">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Batas Toleransi: Maksimal 3 Peringatan
                        </div>
                        <p>
                            Perhatian: Jika Anda mencapai 3 kali peringatan, ujian akan <strong>terkunci secara otomatis</strong> dan Anda harus meminta izin masuk kembali kepada pengawas.
                        </p>
                    </div>
                </div>
                <button type="button" 
                        @click="showProctorWarningModal = false"
                        class="w-full py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold transition shadow-xs tactile-btn cursor-pointer">
                    Saya Mengerti & Lanjutkan Ujian
                </button>
            </div>
        </div>

        <!-- Modal Sesi Ujian Terkunci Otomatis (Auto-Locked) -->
        <div x-show="isAutoLocked" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-7 text-center space-y-4 shadow-2xl border border-rose-300">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto border border-rose-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0110 0v4" />
                    </svg>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 mb-2">
                        Sesi Ujian Dikunci
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Ujian Terkunci Otomatis</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Anda telah mencapai batas maksimal 3 kali peringatan pelanggaran. Halaman soal telah dikunci otomatis oleh sistem.
                    </p>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-2 font-medium">
                        Anda akan diarahkan ke halaman izin pengawas dalam beberapa detik...
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('peserta.ujian.masuk', $jadwal->id) }}"
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition shadow-xs tactile-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Buka Halaman Izin Pengawas
                    </a>
                </div>
            </div>
        </div>


        <!-- Modal Konfirmasi Selesai Ujian (SIMURT Design System) -->
        <div x-show="showFinishModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6" 
             style="display: none;">
            <div @click.away="showFinishModal = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-4">
                <div class="text-center sm:text-left">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shadow-xs mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Selesaikan Ujian Sekarang?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                        Periksa kembali seluruh jawaban Anda. Selesai ujian tidak dapat dibatalkan dan sistem akan langsung melakukan penilaian otomatis.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-normal">Soal Dijawab:</span>
                        <span class="font-bold text-emerald-700" x-text="countAnswered() + ' Soal'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-normal">Belum Dijawab:</span>
                        <span class="font-bold text-rose-700" x-text="(questions.length - countAnswered()) + ' Soal'"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="showFinishModal = false" 
                            class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 font-medium text-xs sm:text-sm hover:bg-slate-50 transition tactile-btn">
                        Batal & Lanjut Ujian
                    </button>

                    <form method="POST" action="{{ route('peserta.ujian.selesai', $sesi->id) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs sm:text-sm transition shadow-xs tactile-btn">
                            Ya, Selesaikan Ujian
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Floating Proctoring Camera Widget (WebRTC) -->
        <div class="fixed bottom-4 left-4 z-40 select-none">
            <!-- Expanded Camera Box -->
            <div x-show="!isCameraMinimized" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-slate-900/95 backdrop-blur-sm p-2.5 rounded-xl border border-slate-700 shadow-2xl text-white space-y-2 w-48 sm:w-56">
                <div class="flex items-center justify-between text-[11px] pb-1 border-b border-slate-800">
                    <div class="flex items-center gap-1.5 font-bold">
                        <span class="w-2 h-2 rounded-full" 
                              :class="{
                                  'bg-rose-500 animate-pulse': cameraActive && isStreamingToProctor,
                                  'bg-emerald-400': cameraActive && !isStreamingToProctor,
                                  'bg-rose-500': cameraError,
                                  'bg-amber-400 animate-pulse': !cameraActive && !cameraError
                              }"></span>
                        <span x-text="cameraError ? 'Kamera Error' : (isStreamingToProctor ? 'Live Diawasi' : (cameraActive ? 'Kamera Aktif' : 'Memuat Kamera...'))"></span>
                    </div>
                    <button type="button" 
                            @click="isCameraMinimized = true"
                            class="text-slate-400 hover:text-white p-0.5 rounded transition"
                            title="Kecilkan Tampilan Kamera">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>
                    </button>
                </div>

                <!-- Video Element Preview -->
                <div class="relative w-full h-32 sm:h-36 bg-slate-950 rounded-lg overflow-hidden border border-slate-800 flex items-center justify-center">
                    <video id="localCameraVideo" 
                           autoplay 
                           playsinline 
                           muted 
                           x-show="cameraActive && !cameraError" 
                           class="w-full h-full object-cover"
                           style="transform: scaleX(-1);"></video>
                    
                    <!-- Fallback placeholder if loading or error -->
                    <div x-show="!cameraActive || cameraError" class="text-center p-3 text-slate-400 space-y-1.5">
                        <svg class="w-6 h-6 mx-auto opacity-70" :class="cameraError ? 'text-rose-400' : 'text-amber-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5l-3.75 3.75m0 0l-3.75 3.75m3.75-3.75l3.75 3.75m-3.75-3.75l-3.75-3.75M3 13.5v-3a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 10.5v3a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 13.5z" />
                        </svg>
                        <p class="text-[10px] leading-tight" x-text="cameraError ? 'Izin kamera ditolak / kamera tidak tersedia' : 'Menghubungkan sensor kamera...'"></p>
                        <button type="button" 
                                x-show="cameraError" 
                                @click="initCamera()" 
                                class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-white underline cursor-pointer">
                            Coba Lagi
                        </button>
                    </div>

                    <!-- Watermark Live Badge -->
                    <div x-show="cameraActive && isStreamingToProctor" 
                         class="absolute top-1.5 left-1.5 bg-rose-600/90 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded flex items-center gap-1 shadow-xs tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                        LIVE STREAM
                    </div>

                    <!-- Warning Overlay: Wajah Tidak Terdeteksi -->
                    <div x-show="cameraActive && isAiFaceLoaded && faceDetectedCount === 0 && faceMissingSeconds >= 2"
                         x-cloak
                         x-transition
                         class="absolute inset-x-0 bottom-0 bg-amber-600/95 text-white text-[9.5px] font-bold p-1 text-center shadow-lg animate-pulse tracking-tight">
                        Wajah Hilang! Hadap kamera (<span x-text="Math.max(1, Math.ceil(6 - faceMissingSeconds))"></span>s)
                    </div>

                    <!-- Warning Overlay: Terdeteksi Lebih Dari 1 Wajah -->
                    <div x-show="cameraActive && isAiFaceLoaded && faceDetectedCount > 1 && multipleFacesSeconds >= 2"
                         x-cloak
                         x-transition
                         class="absolute inset-x-0 bottom-0 bg-rose-600/95 text-white text-[9.5px] font-bold p-1 text-center shadow-lg animate-pulse tracking-tight">
                        Peringatan: Terdeteksi >1 Orang!
                    </div>
                </div>

                <!-- Footer Status (AI Face + WebRTC) -->
                <div class="text-[10px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-1">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full" 
                              :class="{
                                  'bg-emerald-400': isAiFaceLoaded && faceDetectedCount === 1,
                                  'bg-amber-400 animate-pulse': isAiFaceLoaded && faceDetectedCount === 0,
                                  'bg-rose-500 animate-ping': isAiFaceLoaded && faceDetectedCount > 1,
                                  'bg-slate-500': !isAiFaceLoaded
                              }"></span>
                        <span class="text-[9.5px]" 
                              :class="{
                                  'text-emerald-400': isAiFaceLoaded && faceDetectedCount === 1,
                                  'text-amber-300 font-semibold': isAiFaceLoaded && faceDetectedCount === 0,
                                  'text-rose-400 font-semibold': isAiFaceLoaded && faceDetectedCount > 1,
                                  'text-slate-400': !isAiFaceLoaded
                              }"
                              x-text="!isAiFaceLoaded ? 'AI Face Memuat' : (faceDetectedCount === 1 ? 'Wajah Terdeteksi' : (faceDetectedCount === 0 ? 'Wajah Kosong!' : '>1 Orang'))"></span>
                    </div>
                    <span class="text-slate-500 font-mono text-[9px]" x-text="isStreamingToProctor ? (activeStreamMode === 'screen' ? 'Layar Live' : 'Kamera Live') : 'P2P Ready'"></span>
                </div>
            </div>

            <!-- Minimized Pill Toggle -->
            <button type="button" 
                    x-show="isCameraMinimized"
                    x-cloak
                    @click="isCameraMinimized = false"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 hover:bg-slate-900 text-white text-xs font-semibold shadow-lg border border-slate-700 transition tactile-btn cursor-pointer">
                <span class="w-2 h-2 rounded-full" 
                      :class="{
                          'bg-rose-500 animate-pulse': cameraError || (isAiFaceLoaded && faceDetectedCount > 1),
                          'bg-amber-400 animate-pulse': isAiFaceLoaded && faceDetectedCount === 0,
                          'bg-emerald-400': cameraActive && (!isAiFaceLoaded || faceDetectedCount === 1)
                      }"></span>
                <span x-text="(isAiFaceLoaded && faceDetectedCount === 0) ? 'Wajah Hilang! (Buka)' : (isStreamingToProctor ? (activeStreamMode === 'screen' ? 'Layar Diawasi' : 'Kamera Diawasi') : 'Kamera AI')"></span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
            </button>
        </div>

        <!-- Modal Permintaan Berbagi Layar dari Pengawas -->
        <div x-show="showScreenSharePrompt" 
             class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;"
             x-cloak
             x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Pengawas Meminta Pantauan Layar</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Pengawas sedang mengawasi ujian dan meminta Anda untuk membagikan siaran layar. Silakan klik tombol di bawah dan pilih opsi <strong>Seluruh Layar (Entire Screen)</strong> pada pop-up perizinan browser.
                    </p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-left">
                    <p class="text-[11px] text-amber-800 leading-normal font-medium">
                        Catatan: Menolak atau membatalkan permintaan berbagi layar ini akan dicatat dalam riwayat pelanggaran integritas ujian.
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" 
                            @click="startScreenShare()" 
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs sm:text-sm shadow-md transition tactile-btn cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                        <span>Bagikan Layar Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Face Detection Library (Pico.js 100% Offline Lokal) -->
    <script src="{{ asset('js/pico.js') }}"></script>

    <!-- Alpine.js Application Logic -->
    <script>
        function examApp() {
            return {
                questions: @json($soals),
                answers: @json($jawabanMap),
                currentIndex: 0,
                remainingSeconds: Math.max(0, Math.floor(Number({{ (int) $sisaDetik }}) || 0)),
                saveStatusText: 'Tersimpan Otomatis',
                saveStatusColor: 'text-emerald-600',
                showFinishModal: false,
                warningCount: {{ $sesi->jumlah_peringatan }},
                sesiId: {{ $sesi->id }},
                csrfToken: '{{ csrf_token() }}',
                
                // Variabel Pengawasan & Multi-Perangkat
                isMobile: false,
                isIOS: false,
                supportsFullscreen: false,
                showFullscreenOverlay: false,
                showViolationModal: false,
                showProctorWarningModal: false,
                proctorWarningMessage: '',
                lastHandledTeguranId: null,
                showMobileNav: false,
                isAutoLocked: {{ in_array($sesi->status, ['menunggu_izin', 'terputus', 'diskualifikasi']) ? 'true' : 'false' }},
                lastViolationMessage: '',
                lastViolationTime: 0,
                isViolationInFlight: false,
                isRequestingMediaPermission: false,

                // WebRTC Proctoring State (Kamera & Layar)
                cameraActive: false,
                cameraError: false,
                isCameraMinimized: false,
                localStream: null,
                screenStream: null,
                activeStreamMode: 'camera',
                showScreenSharePrompt: false,
                isScreenSharing: false,
                peerConnection: null,
                webrtcPollTimer: null,
                isStreamingToProctor: false,

                // AI Face Detection State (100% Offline Lokal)
                isAiFaceLoaded: false,
                faceClassifier: null,
                faceMemory: null,
                faceDetectTimer: null,
                faceDetectedCount: 1,
                faceMissingSeconds: 0,
                multipleFacesSeconds: 0,
                nativeFaceDetector: null,

                get currentQuestion() {
                    return this.questions[this.currentIndex] || {};
                },

                init() {
                    // Inisialisasi Kamera & WebRTC Proctoring
                    this.initCamera();
                    this.startWebRtcSignalingPoll();

                    // Deteksi Perangkat & Kemampuan Browser
                    this.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                    this.supportsFullscreen = !!(document.documentElement.requestFullscreen || document.documentElement.webkitRequestFullscreen);

                    // 1. Timer server-side authoritative
                    setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        } else {
                            this.submitAutoFinished();
                        }
                    }, 1000);

                    // 2. Heartbeat berkala tiap 15 detik
                    setInterval(() => {
                        this.sendHeartbeat();
                    }, 15000);

                    // 3. Pengawasan Layar Penuh (Non-iOS)
                    if (this.supportsFullscreen && !this.isIOS) {
                        const onFsChange = () => {
                            const inFs = !!(document.fullscreenElement || document.webkitFullscreenElement);
                            if (!inFs && !this.isAutoLocked && !this.showFinishModal) {
                                this.showFullscreenOverlay = true;
                                this.triggerViolation(
                                    this.isMobile ? 'Keluar Layar Penuh di HP' : 'Keluar dari Mode Layar Penuh',
                                    'Peserta keluar dari mode tampilan penuh (Fullscreen).'
                                );
                            } else if (inFs) {
                                this.showFullscreenOverlay = false;
                            }
                        };
                        document.addEventListener('fullscreenchange', onFsChange);
                        document.addEventListener('webkitfullscreenchange', onFsChange);

                        // Trigger otomatis saat interaksi pertama
                        const promptFullscreenOnce = () => {
                            if (!document.fullscreenElement && !document.webkitFullscreenElement && !this.isAutoLocked) {
                                this.enterFullscreen();
                            }
                            document.removeEventListener('click', promptFullscreenOnce);
                            document.removeEventListener('touchstart', promptFullscreenOnce);
                        };
                        document.addEventListener('click', promptFullscreenOnce, { once: true });
                        document.addEventListener('touchstart', promptFullscreenOnce, { once: true });
                    }

                    // 4. Deteksi perpindahan tab & blur (Desktop & HP)
                    // Deduplikasi blur + visibilitychange yang sering terpicu bersamaan saat pindah aplikasi di HP
                    let blurViolationTimer = null;
                    const triggerBlurViolation = (jenis, keterangan) => {
                        if (this.isAutoLocked || this.showFinishModal) return;
                        if (blurViolationTimer) return; // Sudah dijadwalkan, hindari duplikat
                        blurViolationTimer = setTimeout(() => {
                            blurViolationTimer = null;
                            this.triggerViolation(jenis, keterangan);
                        }, 300);
                    };

                    window.addEventListener('blur', () => {
                        if (this.isAutoLocked || this.showFinishModal || this.isRequestingMediaPermission || this.showScreenSharePrompt) return;
                        triggerBlurViolation(
                            this.isMobile ? 'Pindah Aplikasi di HP' : 'Kehilangan Fokus Jendela',
                            this.isMobile ? 'Peserta terdeteksi beralih ke aplikasi lain atau menarik menu HP.' : 'Jendela ruang ujian kehilangan fokus aktif.'
                        );
                    });

                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden && !this.isAutoLocked && !this.showFinishModal && !this.isRequestingMediaPermission && !this.showScreenSharePrompt) {
                            triggerBlurViolation(
                                this.isMobile ? 'Aplikasi HP Diminimalkan / Pindah' : 'Visibility Change (Tab Sembunyi)',
                                this.isMobile ? 'Halaman ujian diminimalkan atau membuka aplikasi lain di HP.' : 'Halaman ujian diminimalkan atau disembunyikan.'
                            );
                        }
                    });

                    // 5. Blokir Tombol Pintas Berbahaya (F12, DevTools, Ctrl+C/V/U/P/S)
                    window.addEventListener('keydown', (e) => {
                        // F12
                        if (e.key === 'F12' || e.keyCode === 123) {
                            e.preventDefault();
                            this.triggerViolation('Mencoba Buka DevTools (F12)', 'Peserta mencoba membuka Developer Tools dengan tombol F12.');
                            return false;
                        }
                        // Ctrl+Shift+I / J / C (DevTools)
                        if (e.ctrlKey && e.shiftKey && ['I', 'J', 'C', 'i', 'j', 'c'].includes(e.key)) {
                            e.preventDefault();
                            this.triggerViolation('Mencoba Inspect Element', 'Peserta mencoba membuka Developer Tools melalui shortcut keyboard.');
                            return false;
                        }
                        // Ctrl+U (View Source)
                        if (e.ctrlKey && ['u', 'U'].includes(e.key)) {
                            e.preventDefault();
                            this.triggerViolation('Mencoba Lihat Source Code', 'Peserta mencoba melihat source code ujian dengan shortcut Ctrl+U.');
                            return false;
                        }
                        // Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+P, Ctrl+S
                        if (e.ctrlKey && ['c', 'v', 'x', 'p', 's', 'C', 'V', 'X', 'P', 'S'].includes(e.key)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                },

                enterFullscreen() {
                    const el = document.documentElement;
                    if (el.requestFullscreen) {
                        el.requestFullscreen().catch(() => {});
                    } else if (el.webkitRequestFullscreen) {
                        el.webkitRequestFullscreen().catch(() => {});
                    }
                    this.showFullscreenOverlay = false;
                },

                triggerViolation(jenis, keterangan) {
                    const now = Date.now();
                    // Throttle agar tidak terpicu ganda dalam interval 2.5 detik
                    if (now - this.lastViolationTime < 2500) {
                        return;
                    }
                    this.lastViolationTime = now;
                    this.reportViolation(jenis, keterangan);
                },

                formatTimer(seconds) {
                    const total = Math.max(0, Math.floor(Number(seconds) || 0));
                    const h = Math.floor(total / 3600);
                    const m = Math.floor((total % 3600) / 60);
                    const s = total % 60;
                    return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
                },

                goToQuestion(index) {
                    this.currentIndex = index;
                },

                prevQuestion() {
                    if (this.currentIndex > 0) this.currentIndex--;
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    } else {
                        this.showFinishModal = true;
                    }
                },

                hasAnswer(qId) {
                    return this.answers[qId] !== undefined && this.answers[qId] !== null && this.answers[qId] !== '';
                },

                countAnswered() {
                    return Object.values(this.answers).filter(v => v !== null && v !== '').length;
                },

                getQuestionBtnClass(index, qId) {
                    if (this.currentIndex === index) {
                        return 'bg-[#e6f4ea] text-slate-900 border-2 border-slate-800 font-bold shadow-2xs';
                    }
                    if (this.hasAnswer(qId)) {
                        return 'bg-[#f1f3f4] text-slate-700 hover:bg-slate-200/80 border border-slate-300 font-medium';
                    }
                    return 'bg-[#1e385b] text-white hover:bg-[#152843] border border-[#1e385b] font-medium shadow-2xs';
                },

                selectAnswer(optKey) {
                    const qId = this.currentQuestion.id;
                    this.answers[qId] = optKey;
                    this.autosaveAnswer(qId, optKey);
                },

                async autosaveAnswer(qId, optKey) {
                    this.saveStatusText = 'Menyimpan...';
                    this.saveStatusColor = 'text-amber-500';

                    try {
                        const response = await fetch(`{{ url('/peserta/ujian/sesi') }}/${this.sesiId}/autosave`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                bank_soal_id: qId,
                                jawaban: optKey
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.saveStatusText = 'Tersimpan Otomatis';
                            this.saveStatusColor = 'text-emerald-600';
                        } else {
                            throw new Error();
                        }
                    } catch (e) {
                        this.saveStatusText = 'Koneksi Gangguan - Mencoba Lagi';
                        this.saveStatusColor = 'text-rose-600';
                    }
                },

                async sendHeartbeat() {
                    try {
                        const response = await fetch(`{{ url('/peserta/ujian/sesi') }}/${this.sesiId}/heartbeat`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.status === 'diskualifikasi') {
                            alert('Anda telah didiskualifikasi dari ujian ini.');
                            window.location.href = '{{ route("peserta.hasil.show", $jadwal->id) }}';
                        }
                        if (data.status === 'selesai' && data.redirect) {
                            window.location.href = data.redirect;
                        }
                        if (data.terkunci || data.status === 'menunggu_izin') {
                            this.isAutoLocked = true;
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        }
                        if (data.sisa_detik !== undefined) {
                            this.remainingSeconds = Math.max(0, Math.floor(Number(data.sisa_detik) || 0));
                        }
                        if (data.jumlah_peringatan !== undefined) {
                            if (data.latest_teguran_id && data.latest_teguran_id !== this.lastHandledTeguranId && data.pesan_teguran) {
                                this.lastHandledTeguranId = data.latest_teguran_id;
                                this.proctorWarningMessage = data.pesan_teguran;
                                this.showProctorWarningModal = true;
                                this.playWarningSound();
                            }
                            this.warningCount = data.jumlah_peringatan;
                        }
                    } catch (e) {
                        console.warn('Heartbeat gagal dikirim');
                    }
                },

                async reportViolation(jenis, keterangan) {
                    try {
                        const response = await fetch(`{{ url('/peserta/ujian/sesi') }}/${this.sesiId}/pelanggaran`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ jenis, keterangan })
                        });
                        const data = await response.json();
                        if (data.peringatan_count !== undefined) {
                            this.warningCount = data.peringatan_count;
                        }
                        if (data.terkunci || this.warningCount >= 3) {
                            this.isAutoLocked = true;
                            // Beri waktu 4 detik agar peserta dapat membaca pesan sebelum dialihkan
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route("peserta.ujian.masuk", $jadwal->id) }}';
                            }, 4000);
                        } else {
                            this.lastViolationMessage = keterangan || jenis;
                            this.showViolationModal = true;
                        }
                    } catch (e) {}
                },

                submitAutoFinished() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('/peserta/ujian/sesi') }}/${this.sesiId}/selesai`;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = this.csrfToken;
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                },

                // ==========================================
                // WEBRTC PROCTORING CLIENT METHODS
                // ==========================================
                async initCamera() {
                    this.cameraError = false;
                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            throw new Error('getUserMedia tidak didukung pada browser ini');
                        }
                        const constraints = {
                            video: {
                                facingMode: 'user',
                                width: { ideal: 640 },
                                height: { ideal: 480 }
                            },
                            audio: false
                        };
                        const stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.localStream = stream;
                        this.cameraActive = true;
                        
                        // Deteksi jika kamera dimatikan, dicabut, atau ditutup saat ujian
                        stream.getVideoTracks().forEach(track => {
                            track.onended = () => {
                                this.cameraActive = false;
                                this.cameraError = true;
                                this.triggerViolation(
                                    'Kamera Pengawasan Dimatikan',
                                    'Kamera pengawasan dimatikan atau terputus saat ujian berlangsung.'
                                );
                            };
                            track.onmute = () => {
                                this.cameraActive = false;
                                this.triggerViolation(
                                    'Feed Kamera Terputus/Ditutup',
                                    'Feed kamera pengawasan berhenti mengirim gambar atau ditutup.'
                                );
                            };
                            track.onunmute = () => {
                                this.cameraActive = true;
                                this.cameraError = false;
                            };
                        });

                        this.$nextTick(() => {
                            const videoEl = document.getElementById('localCameraVideo');
                            if (videoEl) {
                                videoEl.srcObject = stream;
                                videoEl.onloadedmetadata = () => {
                                    this.initFaceDetection();
                                };
                                setTimeout(() => {
                                    this.initFaceDetection();
                                }, 600);
                            }
                        });
                    } catch (e) {
                        console.warn('Izin kamera tidak diberikan atau sensor kamera tidak tersedia:', e);
                        this.cameraActive = false;
                        this.cameraError = true;
                        setTimeout(() => {
                            if (!this.cameraActive && !this.isAutoLocked && !this.showFinishModal) {
                                this.triggerViolation(
                                    'Izin Kamera Ditolak / Tidak Aktif',
                                    'Kamera pengawasan tidak diizinkan atau dinonaktifkan oleh peserta.'
                                );
                            }
                        }, 4000);
                    }
                },

                startWebRtcSignalingPoll() {
                    if (this.webrtcPollTimer) clearInterval(this.webrtcPollTimer);
                    // Poll setiap 1 detik (sama dengan pengawas) agar sinyal WebRTC lebih responsif
                    this.webrtcPollTimer = setInterval(async () => {
                        if (this.isAutoLocked || this.showFinishModal) return;
                        try {
                            const res = await fetch(`{{ url('/webrtc/sesi') }}/${this.sesiId}/signal`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken
                                }
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.signals && data.signals.length > 0) {
                                for (const sig of data.signals) {
                                    await this.handleWebRtcSignal(sig);
                                }
                            }
                        } catch (err) {}
                    }, 1000);
                },

                normalizeSdp(sdp) {
                    if (!sdp || typeof sdp !== 'string') return sdp;
                    const lines = sdp
                        .replace(/\r\n/g, '\n')
                        .replace(/\r/g, '\n')
                        .split('\n')
                        .map(line => line.trim())
                        .filter(line => line.length > 0);
                    return lines.join('\r\n') + '\r\n';
                },

                async handleWebRtcSignal(signal) {
                    if (signal.type === 'request_stream') {
                        const mode = (signal.data && signal.data.mode) ? signal.data.mode : 'camera';
                        console.log('[WebRTC Peserta] Menerima request_stream, mode:', mode);
                        await this.startStreamingToProctor(mode);
                    } else if (signal.type === 'answer') {
                        if (this.peerConnection && signal.data) {
                            try {
                                let answerPayload = signal.data;
                                if (typeof answerPayload === 'string') {
                                    try { answerPayload = JSON.parse(answerPayload); } catch (e) {}
                                }
                                let ansType = (answerPayload && answerPayload.type) ? answerPayload.type : 'answer';
                                let ansSdp = (answerPayload && answerPayload.sdp)
                                    ? (typeof answerPayload.sdp === 'string' ? answerPayload.sdp : (answerPayload.sdp.sdp || ''))
                                    : (typeof answerPayload === 'string' ? answerPayload : '');

                                ansSdp = this.normalizeSdp(ansSdp);

                                console.log('[WebRTC Peserta] Menerima answer dari pengawas');
                                try {
                                    await this.peerConnection.setRemoteDescription(new RTCSessionDescription({ type: ansType, sdp: ansSdp }));
                                } catch (sdpErr) {
                                    if (sdpErr.message && (sdpErr.message.includes('msid') || sdpErr.message.includes('Invalid SDP line'))) {
                                        console.warn('[WebRTC Peserta] Retrying setRemoteDescription without legacy a=ssrc msid line:', sdpErr);
                                        const fallbackLines = ansSdp.split('\r\n').filter(line => !(line.startsWith('a=ssrc:') && line.includes('msid:')));
                                        const fallbackSdp = fallbackLines.join('\r\n') + '\r\n';
                                        await this.peerConnection.setRemoteDescription(new RTCSessionDescription({ type: ansType, sdp: fallbackSdp }));
                                    } else {
                                        throw sdpErr;
                                    }
                                }
                            } catch (e) {
                                console.warn('[WebRTC Peserta] Gagal set remote description answer:', e);
                            }
                        }
                    } else if (signal.type === 'candidate') {
                        if (this.peerConnection && signal.data) {
                            try {
                                await this.peerConnection.addIceCandidate(new RTCIceCandidate(signal.data));
                            } catch (e) {}
                        }
                    } else if (signal.type === 'teguran_pengawas') {
                        const data = signal.data || {};
                        this.warningCount = data.jumlah_peringatan || (this.warningCount + 1);
                        this.proctorWarningMessage = data.pesan || 'Pengawas mengirimkan teguran atas aktivitas mencurigakan.';
                        this.playWarningSound();
                        if (data.terkunci || this.warningCount >= 3) {
                            this.isAutoLocked = true;
                            setTimeout(() => {
                                window.location.href = '{{ route("peserta.ujian.masuk", $jadwal->id) }}';
                            }, 4000);
                        } else {
                            this.showProctorWarningModal = true;
                        }
                    } else if (signal.type === 'stop_stream') {
                        this.stopStreamingToProctor();
                    }
                },

                playWarningSound() {
                    try {
                        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContextClass) return;
                        const audioCtx = new AudioContextClass();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                        osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.15); // A5
                        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.45);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.45);
                    } catch (e) {}
                },

                async startScreenShare() {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
                        this.showScreenSharePrompt = false;
                        await this.sendWebRtcSignal('pengawas', 'error', 'Perangkat atau browser tidak mendukung fitur berbagi layar.');
                        return;
                    }

                    this.isRequestingMediaPermission = true;
                    try {
                        const stream = await navigator.mediaDevices.getDisplayMedia({
                            video: {
                                cursor: 'always',
                                frameRate: { ideal: 15, max: 30 }
                            },
                            audio: false
                        });

                        this.screenStream = stream;
                        this.isScreenSharing = true;
                        this.showScreenSharePrompt = false;

                        stream.getVideoTracks().forEach(track => {
                            track.onended = () => {
                                this.screenStream = null;
                                this.isScreenSharing = false;
                                this.sendWebRtcSignal('pengawas', 'stream_ended', { mode: 'screen' });
                                this.triggerViolation(
                                    'Berbagi Layar Dihentikan',
                                    'Peserta menghentikan siaran berbagi layar saat diawasi.'
                                );
                            };
                        });

                        await this.startStreamingToProctor('screen');
                    } catch (err) {
                        this.showScreenSharePrompt = false;
                        await this.sendWebRtcSignal('pengawas', 'error', 'Peserta membatalkan atau menolak izin berbagi layar.');
                        this.triggerViolation(
                            'Izin Berbagi Layar Ditolak',
                            'Peserta membatalkan atau menolak permintaan pemantauan layar dari pengawas.'
                        );
                    } finally {
                        setTimeout(() => {
                            this.isRequestingMediaPermission = false;
                        }, 1500);
                    }
                },

                async startStreamingToProctor(mode = 'camera') {
                    this.activeStreamMode = mode;
                    let streamToShare = null;

                    if (mode === 'screen') {
                        if (!this.screenStream || !this.screenStream.active || this.screenStream.getVideoTracks().length === 0) {
                            if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
                                await this.sendWebRtcSignal('pengawas', 'error', 'Perangkat/browser peserta (HP/Mobile) tidak mendukung fitur pantau layar. Fitur ini hanya didukung di browser Laptop/PC.');
                                return;
                            }
                            this.showScreenSharePrompt = true;
                            return;
                        }
                        streamToShare = this.screenStream;
                    } else {
                        // mode === 'camera'
                        if (!this.localStream) {
                            await this.initCamera();
                        }
                        if (!this.localStream) {
                            const isLocalInsecure = (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1');
                            const errReason = isLocalInsecure 
                                ? 'Browser peserta memblokir kamera di jaringan HTTP lokal. Butuh HTTPS atau Chrome flag unsafely-treat-insecure-origin-as-secure.'
                                : 'Kamera peserta tidak dapat diakses atau izin ditolak.';
                            await this.sendWebRtcSignal('pengawas', 'error', errReason);
                            return;
                        }
                        streamToShare = this.localStream;
                    }

                    if (this.peerConnection) {
                        try {
                            this.peerConnection.close();
                        } catch (e) {}
                        this.peerConnection = null;
                    }

                    const config = {
                        iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
                    };

                    this.peerConnection = new RTCPeerConnection(config);

                    streamToShare.getTracks().forEach(track => {
                        this.peerConnection.addTrack(track, streamToShare);
                    });

                    this.peerConnection.onicecandidate = (event) => {
                        if (event.candidate) {
                            this.sendWebRtcSignal('pengawas', 'candidate', event.candidate);
                        }
                    };

                    this.peerConnection.onconnectionstatechange = () => {
                        if (this.peerConnection.connectionState === 'connected') {
                            this.isStreamingToProctor = true;
                        } else if (['disconnected', 'failed', 'closed'].includes(this.peerConnection.connectionState)) {
                            this.isStreamingToProctor = false;
                        }
                    };

                    const offer = await this.peerConnection.createOffer();
                    await this.peerConnection.setLocalDescription(offer);

                    await this.sendWebRtcSignal('pengawas', 'offer', {
                        sdp: {
                            type: offer.type,
                            sdp: this.normalizeSdp(offer.sdp)
                        },
                        mode: mode
                    });
                    this.isStreamingToProctor = true;
                },

                stopStreamingToProctor() {
                    if (this.peerConnection) {
                        try {
                            this.peerConnection.close();
                        } catch (e) {}
                        this.peerConnection = null;
                    }
                    this.isStreamingToProctor = false;
                    this.showScreenSharePrompt = false;
                },

                async sendWebRtcSignal(target, type, data) {
                    try {
                        await fetch(`{{ url('/webrtc/sesi') }}/${this.sesiId}/signal`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({ target, type, data })
                        });
                    } catch (e) {}
                },

                // ==========================================
                // AI FACE DETECTION PROCTORING (100% OFFLINE LOKAL)
                // ==========================================
                async initFaceDetection() {
                    if (this.isAiFaceLoaded) return;
                    try {
                        if ('FaceDetector' in window) {
                            this.nativeFaceDetector = new FaceDetector({ fastMode: true, maxDetectedFaces: 3 });
                            this.isAiFaceLoaded = true;
                        } else if (typeof pico !== 'undefined') {
                            const res = await fetch('{{ asset('js/facefinder') }}');
                            if (res.ok) {
                                const buffer = await res.arrayBuffer();
                                const bytes = new Int8Array(buffer);
                                this.faceClassifier = pico.unpack_cascade(bytes);
                                this.faceMemory = pico.instantiate_detection_memory(5);
                                this.isAiFaceLoaded = true;
                            }
                        }
                    } catch (e) {
                        console.warn('Gagal memuat AI Face Detection:', e);
                    }

                    if (this.isAiFaceLoaded) {
                        this.startFaceDetectionLoop();
                    }
                },

                startFaceDetectionLoop() {
                    if (this.faceDetectTimer) clearInterval(this.faceDetectTimer);

                    // Canvas offscreen kecil untuk deteksi ultra-ringan (~160x120)
                    const offscreenCanvas = document.createElement('canvas');
                    offscreenCanvas.width = 160;
                    offscreenCanvas.height = 120;
                    const ctx = offscreenCanvas.getContext('2d', { willReadFrequently: true });

                    const rgba_to_grayscale = (rgba, nrows, ncols) => {
                        const gray = new Uint8Array(nrows * ncols);
                        for (let r = 0; r < nrows; ++r) {
                            for (let c = 0; c < ncols; ++c) {
                                const idx = 4 * (r * ncols + c);
                                gray[r * ncols + c] = (2 * rgba[idx] + 7 * rgba[idx + 1] + rgba[idx + 2]) / 10;
                            }
                        }
                        return gray;
                    };

                    // Jalankan deteksi berkala tiap 1.5 detik agar konsumsi CPU minimal (<1%)
                    this.faceDetectTimer = setInterval(async () => {
                        if (!this.cameraActive || this.cameraError || this.isAutoLocked || this.showFinishModal) {
                            return;
                        }

                        const videoEl = document.getElementById('localCameraVideo');
                        if (!videoEl || videoEl.readyState < 2) return;

                        let detectedFaces = 0;

                        try {
                            if (this.nativeFaceDetector) {
                                const faces = await this.nativeFaceDetector.detect(videoEl);
                                detectedFaces = faces.length;
                            } else if (this.faceClassifier) {
                                ctx.drawImage(videoEl, 0, 0, 160, 120);
                                const frame = ctx.getImageData(0, 0, 160, 120);
                                const image = {
                                    pixels: rgba_to_grayscale(frame.data, 120, 160),
                                    nrows: 120,
                                    ncols: 160,
                                    ldim: 160
                                };
                                const params = {
                                    shiftfactor: 0.1,
                                    scalefactor: 1.1,
                                    minsize: 20,
                                    maxsize: 110
                                };
                                let dets = pico.run_cascade(image, this.faceClassifier, params);
                                dets = this.faceMemory(dets);
                                dets = pico.cluster_detections(dets, 0.2);
                                detectedFaces = dets.filter(d => d[3] > 4.5).length;
                            }
                        } catch (err) {
                            return;
                        }

                        this.faceDetectedCount = detectedFaces;

                        if (detectedFaces === 0) {
                            this.faceMissingSeconds += 1.5;
                            this.multipleFacesSeconds = 0;

                            // Jika wajah hilang lebih dari 6 detik terus menerus, catat pelanggaran
                            if (this.faceMissingSeconds >= 6) {
                                this.triggerViolation(
                                    'Wajah Tidak Terdeteksi di Kamera',
                                    'Wajah peserta tidak berada di depan kamera pengawasan selama lebih dari 5 detik.'
                                );
                                this.faceMissingSeconds = 2; // toleransi sebelum pemicuan berikutnya
                            }
                        } else if (detectedFaces > 1) {
                            this.faceMissingSeconds = 0;
                            this.multipleFacesSeconds += 1.5;

                            // Jika terdeteksi lebih dari 1 orang selama lebih dari 4 detik
                            if (this.multipleFacesSeconds >= 4) {
                                this.triggerViolation(
                                    'Terdeteksi Lebih dari Satu Orang',
                                    'Terdeteksi lebih dari satu orang di depan kamera pengawasan peserta.'
                                );
                                this.multipleFacesSeconds = 1;
                            }
                        } else {
                            // Tepat 1 wajah (normal)
                            this.faceMissingSeconds = 0;
                            this.multipleFacesSeconds = 0;
                        }
                    }, 1500);
                }
            };
        }
    </script>
</x-exam-layout>
