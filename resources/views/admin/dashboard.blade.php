<x-app-layout>
    <x-slot name="header">
        Sistem Administration Panel
    </x-slot>

    <div class="space-y-6">
        <!-- Subheader / Title Section -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Sistem Administration Panel</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola user, berikan hak akses panitia/pengawas, dan audit sistem log keamanan.</p>
        </div>

        <!-- 4 Stat Cards in 1 Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Pengguna -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-lift cursor-default">
                <div class="text-sm font-semibold text-slate-700">Total Pengguna</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $totalPengguna }}</div>
            </div>

            <!-- Peserta -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-lift cursor-default">
                <div class="text-sm font-semibold text-slate-700">Peserta</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $totalPeserta }}</div>
            </div>

            <!-- Panitia -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-lift cursor-default">
                <div class="text-sm font-semibold text-slate-700">Panitia</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $totalPanitia }}</div>
            </div>

            <!-- Pengawas -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-lift cursor-default">
                <div class="text-sm font-semibold text-slate-700">Pengawas</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $totalPengawas }}</div>
            </div>
        </div>

        <!-- Tabel 1: Daftar Pengguna Terbaru -->
        <div class="pt-2">
            <h2 class="text-base font-bold text-slate-900 mb-3">Daftar Pengguna Terbaru</h2>
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-none">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#f0f4f8] text-slate-700 text-xs font-semibold">
                                <th class="py-3.5 px-6">Nama</th>
                                <th class="py-3.5 px-6">Email</th>
                                <th class="py-3.5 px-6 text-center">Role</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($penggunaTerbaru as $u)
                                <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-6 font-medium text-slate-900">{{ $u->name }}</td>
                                    <td class="py-3.5 px-6 text-slate-500 font-normal">{{ $u->email }}</td>
                                    <td class="py-3.5 px-6 text-center">
                                        <span class="inline-block px-3 py-0.5 rounded border border-slate-300 bg-white text-slate-600 text-xs font-normal">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-700 font-normal">
                                        {{ ucfirst($u->status_akun) }}
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.users.edit', $u->id) }}" 
                                               class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 tactile-btn">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline"
                                                  data-confirm-title="Hapus Pengguna"
                                                  data-confirm-message="Apakah Anda yakin ingin menghapus pengguna &quot;{{ $u->name }}&quot;? Data dan hak akses akun ini akan dihapus secara permanen.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 border border-rose-300 rounded-lg text-xs font-medium text-rose-600 bg-white hover:bg-rose-50 tactile-btn cursor-pointer">
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
                                    <td colspan="5" class="py-6 text-center text-xs text-slate-400">Belum ada data pengguna terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel 2: Audit Trail & Log Aktivitas Terbaru -->
        <div class="pt-2">
            <h2 class="text-base font-bold text-slate-900 mb-3">Audit Trail & Log Aktivitas Terbaru</h2>
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-none">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#f0f4f8] text-slate-700 text-xs font-semibold">
                                <th class="py-3.5 px-6">Waktu</th>
                                <th class="py-3.5 px-6">Pengguna</th>
                                <th class="py-3.5 px-6">Aktivitas</th>
                                <th class="py-3.5 px-6">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($auditLogTerbaru as $log)
                                @php
                                    $waktuFormatted = $log->waktu 
                                        ? str_replace(['Oct', 'May', 'Aug', 'Dec'], ['Okt', 'Mei', 'Agt', 'Des'], $log->waktu->format('d M Y, H:i:s')) 
                                        : '-';
                                    $namaPengguna = ($log->user && $log->user->isAdmin()) ? 'Admin' : ($log->user ? $log->user->name : 'Sistem');
                                @endphp
                                <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-6 text-slate-500 text-xs whitespace-nowrap">{{ $waktuFormatted }}</td>
                                    <td class="py-3.5 px-6 font-bold text-slate-900 text-sm whitespace-nowrap">{{ $namaPengguna }}</td>
                                    <td class="py-3.5 px-6 text-slate-700 text-sm whitespace-nowrap">{{ $log->jenis_aktivitas }}</td>
                                    <td class="py-3.5 px-6 text-slate-600 text-xs">{{ $log->keterangan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-xs text-slate-400">Belum ada catatan log aktivitas keamanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
