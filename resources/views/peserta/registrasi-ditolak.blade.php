<x-guest-layout>
    <x-slot name="title">Verifikasi Ditolak</x-slot>

    <div class="text-center py-2">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6m0-6l6 6" />
            </svg>
        </div>

        <h2 class="text-lg font-bold text-slate-900 mt-4 tracking-tight">Verifikasi Akun Ditolak</h2>
        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
            Mohon maaf <strong class="text-slate-800">{{ $user->name }}</strong>, data pendaftaran Anda ditolak oleh panitia. Anda dapat memperbarui data dan mengajukan pendaftaran ulang di bawah ini.
        </p>

        <form method="POST" action="{{ route('registrasi.ulang') }}" class="mt-5 text-left space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Identitas (NIK Valid)</label>
                <input type="text" name="no_identitas" value="{{ old('no_identitas', $user->peserta?->no_identitas) }}" required
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Asal Sekolah / Instansi</label>
                <input type="text" name="instansi" value="{{ old('instansi', $user->peserta?->instansi) }}"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition" />
            </div>

            <div class="pt-2 space-y-2">
                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-[#1e385b] hover:bg-[#162a45] text-white font-semibold rounded-lg text-xs sm:text-sm transition tactile-btn shadow-sm">
                    Ajukan Ulang Verifikasi
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full py-2 px-4 text-slate-500 hover:text-slate-800 text-xs font-medium transition">
                Keluar Akun
            </button>
        </form>
    </div>
</x-guest-layout>
