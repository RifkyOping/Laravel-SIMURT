<x-app-layout>
    <x-slot name="header">
        Bank Soal
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showAddModal: false }">
        <!-- Header Halaman -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bank Soal</h1>
            <p class="text-sm text-slate-500 mt-1">Unggah, kelola, dan atur soal ujian.</p>
        </div>

        <!-- 3 Kartu Statistik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Total Paket Soal</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $totalPaket }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Paket Soal Berlangsung</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $paketAktif }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
                <span class="text-sm font-medium text-slate-600 block">Paket Soal Selesai</span>
                <span class="text-3xl font-bold text-slate-900 block mt-3">{{ $paketSelesai }}</span>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('panitia.paket.index') }}" class="w-full sm:w-64 md:w-72">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Paket Ujian..."
                        class="w-full text-sm pl-9 pr-3 py-2 bg-white rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                </div>
            </form>

            <button type="button" @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#1e385b] hover:bg-[#152740] text-white rounded-lg text-sm font-semibold shadow-xs transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Paket Soal</span>
            </button>
        </div>

        <!-- Tabel Data Paket Soal -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-semibold text-xs sm:text-sm">
                            <th class="py-3.5 px-4 sm:px-6 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 sm:px-6">Nama Paket Soal</th>
                            <th class="py-3.5 px-4 sm:px-6">Jumlah Soal</th>
                            <th class="py-3.5 px-4 sm:px-6">Durasi</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($paketSoals as $index => $p)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600 text-center">
                                    {{ $paketSoals->firstItem() + $index }}</td>
                                <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">{{ $p->nama_paket }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ $p->bank_soals_count }} Soal</td>
                                <td class="py-3.5 px-4 sm:px-6 text-slate-600">{{ $p->durasi }} Menit</td>
                                <td class="py-3.5 px-4 sm:px-6 text-center">
                                    @if ($p->status_paket === 'akan_datang')
                                        <span
                                            class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-[#e8f0fe] text-[#1a73e8]">
                                            Akan Datang
                                        </span>
                                    @elseif($p->status_paket === 'berlangsung')
                                        <span
                                            class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-[#fef3c7] text-[#b45309]">
                                            Berlangsung
                                        </span>
                                    @else
                                        <span
                                            class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-[#f1f5f9] text-[#64748b]">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <a href="{{ route('panitia.soal.index', $p->id) }}"
                                            class="px-3 py-1.5 rounded-md bg-[#1e385b] hover:bg-[#152740] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            <span>Kelola Soal</span>
                                        </a>
                                        <a href="{{ route('panitia.paket.edit', $p->id) }}"
                                            class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 font-medium text-xs shadow-xs transition inline-flex items-center gap-1 tactile-btn">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('panitia.paket.destroy', $p->id) }}" class="inline"
                                              data-confirm-title="Hapus Paket Soal"
                                              data-confirm-message="Apakah Anda yakin ingin menghapus paket soal &quot;{{ $p->nama_paket }}&quot;? Seluruh butir soal di dalamnya akan terhapus secara permanen.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-lg border border-rose-300 bg-white text-rose-600 hover:bg-rose-50 font-medium text-xs shadow-xs transition inline-flex items-center gap-1 tactile-btn cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada paket soal dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div
                class="border-t border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <div class="text-xs sm:text-sm text-slate-500">
                    Menampilkan {{ $paketSoals->firstItem() ?? 0 }}-{{ $paketSoals->lastItem() ?? 0 }} dari
                    {{ $paketSoals->total() }} entries
                </div>
                <div>
                    {{ $paketSoals->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>

        <!-- Modal Tambah Paket Soal -->
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;">
            <div @click.away="showAddModal = false"
                class="bg-white rounded-xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Tambah Paket Soal</h3>
                    <button type="button" @click="showAddModal = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition"
                        title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('panitia.paket.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5">Nama Paket Soal</label>
                        <input type="text" name="nama_paket" required
                            placeholder="misal: Seleksi Kemampuan Dasar (SKD) - Batch 1"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5">Durasi Ujian (Menit)</label>
                        <input type="number" name="durasi" required min="1" placeholder="misal: 120"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs text-slate-800 placeholder-slate-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5">Status</label>
                        <div class="relative">
                            <select name="status_paket" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs appearance-none bg-white pr-10 text-slate-700">
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="akan_datang">Akan Datang</option>
                                <option value="berlangsung">Berlangsung</option>
                                <option value="selesai">Selesai</option>
                            </select>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button type="button" @click="showAddModal = false"
                            class="px-5 py-2 border border-slate-300 bg-white text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-medium rounded-lg text-sm shadow-xs transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
