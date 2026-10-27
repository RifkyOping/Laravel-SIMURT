<x-guest-layout>
    <x-slot name="title">Menunggu Verifikasi</x-slot>

    <div class="text-center py-2">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto shadow-sm">
            <!-- Clock SVG -->
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
            </svg>
        </div>

        <h2 class="text-lg font-bold text-slate-900 mt-4 tracking-tight">Akun Menunggu Verifikasi</h2>
        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
            Halo <strong class="text-slate-800">{{ $user->name }}</strong>, data pendaftaran Anda telah kami terima dan saat ini sedang menunggu proses verifikasi oleh panitia seleksi.
        </p>

        <div class="mt-5 p-4 rounded-xl bg-slate-50 border border-slate-200 text-left space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Lengkap:</span>
                <span class="font-semibold text-slate-800">{{ $user->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Email:</span>
                <span class="font-semibold text-slate-800">{{ $user->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">NIK:</span>
                <span class="font-semibold text-slate-800">{{ $user->peserta?->no_identitas ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status Akun:</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">
                    Menunggu Verifikasi
                </span>
            </div>
        </div>

        <div class="mt-6 space-y-2">
            <a href="javascript:location.reload()" 
               class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-[#1e385b] hover:bg-[#162a45] text-white font-semibold rounded-lg text-xs transition tactile-btn shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Segarkan Halaman</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full py-2 px-4 text-slate-500 hover:text-slate-800 text-xs font-medium transition">
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
