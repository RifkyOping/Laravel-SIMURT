<x-app-layout>
    <x-slot name="header">
        Data Peserta
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Peserta</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data peserta yang terdaftar dalam sistem.</p>
        </div>

        <!-- Filter & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.peserta.index') }}" class="flex flex-wrap items-center gap-3">
                <!-- Search Input with Magnifying Glass Icon -->
                <div class="relative w-full sm:w-64 md:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama atau email..."
                           class="w-full text-sm pl-9 pr-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                </div>

                <!-- Status Filter Dropdown: Status: Semua -->
                <div class="flex items-center gap-1.5 bg-white border border-slate-300 rounded-lg px-3 py-1.5 shadow-xs">
                    <span class="text-sm font-medium text-slate-700 whitespace-nowrap">Status:</span>
                    <select name="status" onchange="this.form.submit()" class="text-sm font-semibold text-slate-800 bg-transparent border-none py-0 pl-1 pr-6 focus:ring-0 cursor-pointer">
                        <option value="Semua" {{ request('status') == 'Semua' || !request('status') ? 'selected' : '' }}>Semua</option>
                        <option value="terverifikasi" {{ strtolower(request('status')) == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="menunggu" {{ strtolower(request('status')) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="ditolak" {{ strtolower(request('status')) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </form>

            <!-- Export Data Button -->
            <div>
                <a href="{{ route('panitia.peserta.export-csv') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-medium shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export Data</span>
                </a>
            </div>
        </div>

        <!-- Tabel Data Peserta -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                            <th class="py-3.5 px-4 sm:px-6 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 sm:px-6">Nama Lengkap</th>
                            <th class="py-3.5 px-4 sm:px-6">Email</th>
                            <th class="py-3.5 px-4 sm:px-6">Nomor Identitas (NIK)</th>
                            <th class="py-3.5 px-4 sm:px-6">Tanggal Daftar</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pesertas as $index => $p)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 text-center">{{ $pesertas->firstItem() + $index }}</td>
                                <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">{{ $p->name }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-500">{{ $p->email }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 font-mono">{{ $p->peserta?->no_identitas ?? '-' }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ \Carbon\Carbon::parse($p->created_at)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-center">
                                    @if($p->status_akun === 'terverifikasi')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            Terverifikasi
                                        </span>
                                    @elseif($p->status_akun === 'menunggu')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/60">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
                                        <a href="{{ route('panitia.peserta.edit', $p->id) }}" 
                                           class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('panitia.peserta.destroy', $p->id) }}" class="inline"
                                              data-confirm-title="Hapus Data Peserta"
                                              data-confirm-message="Apakah Anda yakin ingin menghapus data peserta &quot;{{ $p->nama }}&quot; (NISN: {{ $p->nisn }})? Data ujian dan riwayat peserta ini akan dihapus secara permanen.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-rose-600 text-xs font-medium transition tactile-btn cursor-pointer">
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
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data peserta ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="border-t border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <div class="text-xs sm:text-sm text-slate-500">
                    Menampilkan {{ $pesertas->firstItem() ?? 0 }}-{{ $pesertas->lastItem() ?? 0 }} dari {{ $pesertas->total() }} entries
                </div>
                <div>
                    {{ $pesertas->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
