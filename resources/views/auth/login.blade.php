<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>
    <x-slot name="maxWidth">sm:max-w-[400px]</x-slot>

    <!-- Top Icon Box -->
    <div class="w-12 h-12 rounded-xl overflow-hidden mx-auto mb-3.5 shadow-sm border border-slate-200">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SIMURT" class="w-full h-full object-cover" />
    </div>

    <!-- Title & Subtitle -->
    <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight text-center">Sistem Manajemen Ujian</h1>
    <p class="text-xs text-slate-500 text-center mt-1 mb-6">Silakan masuk ke akun Anda</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('login'))
        <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2 animate-page-enter">
            <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>Email dan password yang Anda masukkan salah.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold {{ $errors->has('email') ? 'text-rose-600' : 'text-slate-700' }} mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="contoh: peserta@email.com"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20' : 'border-slate-200 focus:ring-[#1e385b] focus:border-[#1e385b]' }} placeholder:text-slate-400 focus:outline-none focus:ring-1 transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-xs font-semibold {{ $errors->has('password') ? 'text-rose-600' : 'text-slate-700' }} mb-1.5">Password</label>
            <div class="relative">
                <input id="password"
                       :type="show ? 'text' : 'password'"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="Masukkan password"
                       class="w-full text-xs sm:text-sm pl-3.5 pr-10 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20' : 'border-slate-200 focus:ring-[#1e385b] focus:border-[#1e385b]' }} placeholder:text-slate-400 focus:outline-none focus:ring-1 transition" />
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                        :title="show ? 'Sembunyikan password' : 'Lihat password'"
                        :aria-label="show ? 'Sembunyikan password' : 'Lihat password'">
                    <!-- Icon mata terbuka: saat password tersembunyi (klik untuk melihat) -->
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <!-- Icon mata dicoret: saat password terlihat (klik untuk menyembunyikan) -->
                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Tombol Masuk -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-[#1e385b] hover:bg-[#152843] text-white font-semibold rounded-lg text-xs sm:text-sm shadow-xs transition tactile-btn">
                Masuk
            </button>
        </div>

        <!-- Links Bawah -->
        <div class="text-center pt-2 space-y-1">
            <a href="{{ route('register') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline block">
                Registrasi Akun Peserta
            </a>
            <span class="text-[11px] text-slate-400 block cursor-default">
                Lupa Password? Hubungi Panitia
            </span>
        </div>
    </form>
</x-guest-layout>
