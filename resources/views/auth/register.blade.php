<x-guest-layout>
    <x-slot name="title">Registrasi Akun Peserta</x-slot>
    <x-slot name="maxWidth">sm:max-w-[460px]</x-slot>

    <!-- Top Icon Box -->
    <div class="w-12 h-12 rounded-xl overflow-hidden mx-auto mb-3.5 shadow-sm border border-slate-200">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SIMURT" class="w-full h-full object-cover" />
    </div>

    <!-- Title & Subtitle -->
    <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight text-center">Registrasi Akun Peserta</h1>
    <p class="text-xs text-slate-500 text-center mt-1 mb-6">Lengkapi formulir di bawah untuk mendaftar ujian</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Masukkan nama lengkap sesuai identitas"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   placeholder="contoh: nama@email.com"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Nomor Identitas -->
        <div>
            <label for="no_identitas" class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Identitas</label>
            <input id="no_identitas" type="text" name="no_identitas" value="{{ old('no_identitas') }}" required
                   placeholder="Masukkan nomor identitas"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('no_identitas')" class="mt-1" />
        </div>

        <!-- Instansi -->
        <div>
            <label for="instansi" class="block text-xs font-semibold text-slate-700 mb-1.5">Instansi</label>
            <input id="instansi" type="text" name="instansi" value="{{ old('instansi') }}"
                   placeholder="Asal instansi"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('instansi')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="Buat password (minimal 8 karakter)"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="Ulangi password"
                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Tombol Daftar Sekarang -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-[#1e385b] hover:bg-[#162a45] text-white font-semibold rounded-lg text-xs sm:text-sm shadow-sm transition tactile-btn">
                Daftar Sekarang
            </button>
        </div>

        <!-- Link ke Login -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
