<x-app-layout>
    <x-slot name="header">
        Data Pengawas
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showAddModal: false }">
        <!-- Header Halaman & Tombol Tambah Pengawas -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pengawas</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data pengawas yang terdaftar dalam sistem</p>
            </div>
            <div>
                <button type="button" 
                        @click="showAddModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-semibold shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Pengawas</span>
                </button>
            </div>
        </div>

        <!-- Filter & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.pengawas.index') }}" class="flex flex-wrap items-center gap-3">
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
                <a href="{{ route('panitia.pengawas.export-csv') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-medium shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export Data</span>
                </a>
            </div>
        </div>

        <!-- Tabel Data Pengawas -->
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
                        @forelse($pengawas as $index => $p)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 text-center">{{ $pengawas->firstItem() + $index }}</td>
                                <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">{{ $p->name }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-500">{{ $p->email }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 font-mono">{{ $p->peserta?->no_identitas ?? ('3171' . str_pad($p->id, 12, '0', STR_PAD_LEFT)) }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ \Carbon\Carbon::parse($p->created_at)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-center">
                                    @if(in_array($p->status_akun, ['terverifikasi', 'aktif']))
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
                                        <a href="{{ route('panitia.pengawas.edit', $p->id) }}" 
                                           class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('panitia.pengawas.destroy', $p->id) }}" class="inline"
                                              data-confirm-title="Hapus Data Pengawas"
                                              data-confirm-message="Apakah Anda yakin ingin menghapus data pengawas &quot;{{ $p->nama }}&quot; (NIP: {{ $p->nip }})?">
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
                                    Tidak ada data pengawas ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="border-t border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <div class="text-xs sm:text-sm text-slate-500">
                    Menampilkan {{ $pengawas->firstItem() ?? 0 }}-{{ $pengawas->lastItem() ?? 0 }} dari {{ $pengawas->total() }} entries
                </div>
                <div>
                    {{ $pengawas->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah Pengawas -->
        <div x-show="showAddModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.away="showAddModal = false" 
                 class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Tambah Akun Pengawas Baru</h3>
                    <button type="button" @click="showAddModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition tactile-btn cursor-pointer" title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('panitia.pengawas.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama pengawas"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder:text-slate-400" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" required placeholder="pengawas@simurt.id"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder:text-slate-400" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Identitas (NIK)</label>
                        <input type="text" name="no_identitas" placeholder="16 digit NIK (opsional)"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs font-mono text-slate-800 placeholder:text-slate-400" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder:text-slate-400" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder:text-slate-400" />
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2.5">
                        <button type="button" @click="showAddModal = false" 
                                class="px-4 py-2 border border-slate-200 bg-white text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition tactile-btn cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 bg-[#1e385b] hover:bg-[#152843] text-white font-semibold rounded-lg text-sm shadow-xs transition tactile-btn cursor-pointer">
                            Simpan Pengawas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
