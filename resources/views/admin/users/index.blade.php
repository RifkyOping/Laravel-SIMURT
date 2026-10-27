<x-app-layout>
    <x-slot name="header">
        Kelola Pengguna
    </x-slot>

    <div class="space-y-6" x-data="{ showAddModal: false }">
        <!-- Subheader / Title Section -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola seluruh akun pengguna dalam sistem.</p>
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

        <!-- Filter, Pencarian & Tombol Tambah Pengguna -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <!-- Left Filters -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search Input with Magnifier SVG -->
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau email..." 
                           class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400" />
                </div>

                <!-- Role Filter Dropdown -->
                <div class="relative">
                    <select name="role" 
                            onchange="this.form.submit()" 
                            class="appearance-none pl-3 pr-8 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400 cursor-pointer">
                        <option value="Semua" {{ !request('role') || request('role') === 'Semua' ? 'selected' : '' }}>Role: Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="panitia" {{ request('role') === 'panitia' ? 'selected' : '' }}>Panitia</option>
                        <option value="pengawas" {{ request('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="peserta" {{ request('role') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="relative">
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="appearance-none pl-3 pr-8 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400 cursor-pointer">
                        <option value="Semua" {{ !request('status') || request('status') === 'Semua' ? 'selected' : '' }}>Status: Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-aktif" {{ request('status') === 'non-aktif' || request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                @if(request('search') || (request('role') && request('role') !== 'Semua') || (request('status') && request('status') !== 'Semua'))
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-600 hover:text-rose-800 underline font-medium">
                        Reset Filter
                    </a>
                @endif
            </form>

            <!-- Right Action: Tambah Pengguna Button -->
            <button type="button" 
                    @click="showAddModal = true" 
                    class="bg-[#1e385b] hover:bg-[#152843] text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-1.5 shadow-none transition flex-shrink-0 tactile-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pengguna</span>
            </button>
        </div>

        <!-- Tabel Pengguna -->
        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-none">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#f0f4f8] text-slate-700 text-xs font-semibold">
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">Nama</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4 text-center">Role</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Terakhir Login</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($users as $index => $u)
                            @php
                                $roleClass = match($u->role) {
                                    'peserta' => 'bg-blue-100 text-blue-700',
                                    'panitia' => 'bg-purple-100 text-purple-700',
                                    'pengawas' => 'bg-amber-100 text-amber-700',
                                    'admin' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };

                                $isAktif = in_array(strtolower($u->status_akun), ['aktif', 'terverifikasi']);
                                $statusLabel = $isAktif ? 'Aktif' : 'Non-Aktif';
                                $statusClass = $isAktif ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';

                                $loginDate = $u->terakhir_login 
                                    ? str_replace(['Oct', 'May', 'Aug', 'Dec'], ['Okt', 'Mei', 'Agt', 'Des'], $u->terakhir_login->format('d M Y'))
                                    : '-';
                            @endphp
                            <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 text-center text-slate-500 text-xs">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $u->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-normal">
                                    {{ $u->email }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold {{ $roleClass }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-semibold {{ $statusClass }}">
                                        @if($isAktif)
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                            </span>
                                        @else
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        @endif
                                        <span>{{ $statusLabel }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-500 text-xs">
                                    {{ $loginDate }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $u->id) }}" 
                                           class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 tactile-btn">
                                            Edit
                                        </a>
                                        @if(Auth::id() !== $u->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline"
                                                  data-confirm-title="Hapus Akun Pengguna"
                                                  data-confirm-message="Apakah Anda yakin ingin menghapus akun &quot;{{ $u->name }}&quot; ({{ $u->email }})? Data dan hak akses akun ini akan dihapus secara permanen.">
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                    Tidak ada data pengguna ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($users->hasPages())
                <div class="px-6 py-3 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Pengguna dengan Organic Spring Transition -->
        <div x-show="showAddModal" 
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;">
            <div x-show="showAddModal"
                 x-transition:enter="transition ease-spring duration-250"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 @click.away="showAddModal = false" 
                 class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Tambah Akun Pengguna Baru</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 p-1 tactile-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap pengguna"
                               class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition" />
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" required placeholder="user@simurt.id"
                               class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Role Akun</label>
                            <select name="role" required class="w-full text-xs sm:text-sm px-3 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                                <option value="peserta">Peserta</option>
                                <option value="panitia">Panitia</option>
                                <option value="pengawas">Pengawas</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Status Akun</label>
                            <select name="status_akun" required class="w-full text-xs sm:text-sm px-3 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                                <option value="terverifikasi">Terverifikasi</option>
                                <option value="menunggu">Menunggu</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Password</label>
                            <input type="password" name="password" required placeholder="Min 8 karakter"
                                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition" />
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 transition" />
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showAddModal = false" 
                                class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-50 tactile-btn">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 bg-[#1e385b] hover:bg-[#152843] text-white font-medium rounded-lg text-xs tactile-btn">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
