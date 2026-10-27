<x-app-layout>
    <x-slot name="header">
        Dashboard Panitia
    </x-slot>

    <div class="space-y-7 max-w-7xl mx-auto">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Panitia</h1>
            <p class="text-sm text-slate-500 mt-1">Manajemen registrasi peserta, monitoring jadwal, dan kelola kelulusan ujian.</p>
        </div>

        <!-- 4 Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Total Peserta</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ number_format($totalPeserta) }}</span>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Ujian Aktif</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ number_format($ujianAktif) }}</span>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Soal Tersedia</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ number_format($soalTersedia) }}</span>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Peserta Menunggu Verifikasi</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ number_format($pesertaMenunggu) }}</span>
            </div>
        </div>

        <!-- Bagian 1: Peserta Menunggu Verifikasi -->
        <div class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900">Peserta Menunggu Verifikasi</h2>
            
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                                <th class="py-3.5 px-4 sm:px-6">Nama</th>
                                <th class="py-3.5 px-4 sm:px-6">Email</th>
                                <th class="py-3.5 px-4 sm:px-6">Tanggal Daftar</th>
                                <th class="py-3.5 px-4 sm:px-6 text-center sm:text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($antreanVerifikasi as $p)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">{{ $p->name }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-slate-500">{{ $p->email }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ \Carbon\Carbon::parse($p->created_at)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-center sm:text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <form method="POST" action="{{ route('panitia.peserta.verifikasi', $p->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="terverifikasi">
                                                <button type="submit" class="px-4 py-1.5 rounded-md bg-[#1e385b] hover:bg-[#152740] text-white font-semibold text-xs transition shadow-sm">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('panitia.peserta.verifikasi', $p->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="ditolak">
                                                <button type="submit" class="px-4 py-1.5 rounded-md border border-rose-500 hover:bg-rose-50 text-rose-600 font-semibold text-xs transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">
                                        Tidak ada peserta dalam antrean verifikasi saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Jadwal Ujian Mendatang -->
        <div class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900">Jadwal Ujian Mendatang</h2>

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                                <th class="py-3.5 px-4 sm:px-6">Nama Ujian</th>
                                <th class="py-3.5 px-4 sm:px-6">Tanggal</th>
                                <th class="py-3.5 px-4 sm:px-6">Waktu</th>
                                <th class="py-3.5 px-4 sm:px-6">Jumlah Peserta</th>
                                <th class="py-3.5 px-4 sm:px-6 text-center sm:text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($jadwalMendatang as $jm)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">{{ $jm->nama_ujian }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ \Carbon\Carbon::parse($jm->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ substr($jm->jam_mulai, 0, 5) }} - {{ substr($jm->jam_selesai, 0, 5) }} WITA</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ $jm->pesertas_count }} Peserta</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-center sm:text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('panitia.jadwal.edit', $jm->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs transition tactile-btn">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('panitia.jadwal.destroy', $jm->id) }}" class="inline"
                                                  data-confirm-title="Hapus Jadwal Ujian"
                                                  data-confirm-message="Apakah Anda yakin ingin menghapus jadwal ujian &quot;{{ $jm->nama_ujian }}&quot;? Seluruh sesi dan data peserta terkait jadwal ini akan terhapus.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg border border-rose-300 hover:bg-rose-50 text-rose-600 font-medium text-xs transition tactile-btn cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada jadwal ujian yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
