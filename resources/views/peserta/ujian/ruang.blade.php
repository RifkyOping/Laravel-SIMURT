<x-exam-layout>
    <div x-data="examApp()" x-init="init()" class="flex flex-col h-screen overflow-hidden bg-white">
        <!-- Header Ujian (Fixed Top - Brand Navy #1e385b) -->
        <header class="h-16 bg-[#1e385b] text-white px-6 flex items-center justify-between flex-shrink-0 z-40 shadow-xs">
            <div>
                <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">
                    {{ $jadwal->nama_ujian }}
                </h1>
                <p class="text-xs text-white/80 mt-0.5 font-normal">
                    Peserta: {{ Auth::user()->name }} (No. Ujian: {{ Auth::user()->peserta?->no_ujian ?? Auth::user()->peserta?->no_identitas ?? '-' }})
                </p>
            </div>

            <!-- Sisa Waktu Badge (White pill with red border and text) -->
            <div class="bg-white text-rose-600 px-3.5 py-1.5 rounded-lg border border-rose-200 shadow-xs flex items-center gap-2 font-bold text-xs sm:text-sm">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>Sisa Waktu: <span x-text="formatTimer(remainingSeconds)">01:30:00</span></span>
            </div>
        </header>

        <!-- Floating Warning Toast (if violation detected) -->
        <div x-show="warningCount > 0" 
             x-cloak
             class="bg-amber-500 text-slate-950 px-4 py-2 text-xs font-bold text-center z-30 flex items-center justify-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-slate-950 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>PERINGATAN INTEGRITAS: Anda memiliki <span x-text="warningCount"></span> peringatan aktivitas mencurigakan. Jangan tinggalkan halaman ujian!</span>
        </div>

        <!-- Main Exam Area (Split Panel matching Mockup) -->
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0 bg-white">
            
            <!-- Panel Kiri: Navigasi Soal (Col Left) -->
            <aside class="w-full lg:w-80 flex-shrink-0 p-6 border-b lg:border-b-0 lg:border-r border-slate-200 flex flex-col overflow-y-auto">
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
            <main class="flex-1 p-6 sm:p-8 lg:p-10 flex flex-col justify-between overflow-y-auto">
                <div class="max-w-4xl">
                    <!-- Header Soal: Nomor Soal & Status Terjawab -->
                    <div class="flex items-center justify-between pb-2">
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
                            <span class="text-slate-700" x-text="hasAnswer(currentQuestion.id) ? 'Sudah Terjawab' : 'Belum Terjawab'">
                                Belum Terjawab
                            </span>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal -->
                    <div class="text-sm sm:text-base font-normal text-slate-800 leading-relaxed my-5" 
                         x-text="currentQuestion.pertanyaan">
                    </div>

                    <!-- Pilihan Jawaban (Radio Style matching mockup) -->
                    <div class="space-y-3.5 my-6">
                        <template x-for="(text, optKey) in currentQuestion.opsi_jawaban" :key="optKey">
                            <label @click="selectAnswer(optKey)" 
                                   class="flex items-start gap-3 cursor-pointer select-none group py-0.5">
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
                <div class="border-t border-slate-200 pt-6 mt-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Left Buttons: Sebelumnya & Simpan & Lanjut -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="button" 
                                    @click="prevQuestion()" 
                                    :disabled="currentIndex === 0"
                                    class="px-5 py-2.5 rounded-lg border border-slate-700 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition tactile-btn disabled:opacity-40 disabled:cursor-not-allowed">
                                Sebelumnya
                            </button>

                            <button type="button" 
                                    @click="nextQuestion()" 
                                    class="px-5 py-2.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn">
                                Simpan & Lanjut
                            </button>
                        </div>

                        <!-- Right Side: Warning Note & Selesai Ujian -->
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                            <div class="text-right hidden sm:block">
                                <span class="text-xs font-semibold text-slate-700 block leading-tight">Periksa kembali seluruh jawaban Anda.</span>
                                <span class="text-[11px] text-slate-400 block leading-tight mt-0.5">Aksi selesai tidak bisa dibatalkan.</span>
                            </div>

                            <button type="button" 
                                    @click="showFinishModal = true" 
                                    class="px-5 py-2.5 rounded-lg border border-slate-700 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold transition tactile-btn cursor-pointer whitespace-nowrap">
                                Selesai Ujian
                            </button>
                        </div>
                    </div>
                </div>
            </main>
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
    </div>

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

                get currentQuestion() {
                    return this.questions[this.currentIndex] || {};
                },

                init() {
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

                    // 3. Deteksi perpindahan tab & blur
                    window.addEventListener('blur', () => {
                        this.reportViolation('Pindah Tab Browser / Kehilangan Fokus', 'Peserta terdeteksi berpindah jendela atau aplikasi lain.');
                    });

                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.reportViolation('Visibility Change (Tab Sembunyi)', 'Halaman ujian diminimalkan atau disembunyikan.');
                        }
                    });
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
                        if (data.sisa_detik !== undefined) {
                            this.remainingSeconds = Math.max(0, Math.floor(Number(data.sisa_detik) || 0));
                        }
                        if (data.jumlah_peringatan !== undefined) {
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
                        if (data.peringatan_count) {
                            this.warningCount = data.peringatan_count;
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
                }
            };
        }
    </script>
</x-exam-layout>
