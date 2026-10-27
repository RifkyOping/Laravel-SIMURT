<x-app-layout>
    <x-slot name="header">
        Log Aktivitas Sistem
    </x-slot>

    <div class="space-y-6">
        <!-- Subheader / Title Section -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Log Aktivitas Sistem</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau seluruh aktivitas pengguna dalam sistem.</p>
        </div>

        <!-- Filter Controls Row (Tanggal, Pengguna, Jenis Aktivitas) -->
        <div class="flex flex-wrap items-center gap-3 pt-1">
            <!-- Filter 1: Tanggal with Calendar Icon & Interactive Picker -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" 
                        class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-xs sm:text-sm font-medium text-slate-700 flex items-center gap-2 hover:bg-slate-50 transition shadow-none tactile-btn">
                    <!-- Calendar Icon SVG -->
                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2" />
                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    <span>
                        @if(request('tanggal_mulai') && request('tanggal_selesai'))
                            Tanggal: {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d M Y') }}
                        @elseif(request('tanggal_mulai'))
                            Tanggal: Sejak {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }}
                        @else
                            Tanggal: 10 Okt 2024 - 15 Okt 2024
                        @endif
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Date Dropdown Popover -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-spring duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                     class="absolute left-0 mt-2 z-30 bg-white border border-slate-200 rounded-xl p-4 shadow-xl w-72 space-y-3" 
                     style="display: none;">
                    <form method="GET" action="{{ route('admin.aktivitas.index') }}" class="space-y-3 text-xs">
                        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                        <input type="hidden" name="jenis_aktivitas" value="{{ request('jenis_aktivitas') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div>
                            <label class="block text-slate-700 font-medium mb-1">Mulai Tanggal</label>
                            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai', '2024-10-10') }}" 
                                   class="w-full text-xs rounded-lg border border-slate-200 px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-medium mb-1">Sampai Tanggal</label>
                            <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai', '2024-10-15') }}" 
                                   class="w-full text-xs rounded-lg border border-slate-200 px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <a href="{{ route('admin.aktivitas.index') }}" class="text-xs text-rose-600 hover:underline">Reset</a>
                            <button type="submit" class="px-3.5 py-1.5 bg-[#1e385b] text-white rounded-lg font-medium tactile-btn">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter 2 & 3 Form -->
            <form method="GET" action="{{ route('admin.aktivitas.index') }}" id="filterForm" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">

                <!-- Filter 2: Pengguna Dropdown -->
                <div class="relative">
                    <select name="user_id" onchange="this.form.submit()"
                            class="bg-white border border-slate-200 rounded-lg pl-4 pr-9 py-2 text-xs sm:text-sm font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer appearance-none focus:outline-none focus:ring-1 focus:ring-slate-400 shadow-none">
                        <option value="">Pengguna: Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                Pengguna: {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter 3: Jenis Aktivitas Dropdown -->
                <div class="relative">
                    <select name="jenis_aktivitas" onchange="this.form.submit()"
                            class="bg-white border border-slate-200 rounded-lg pl-4 pr-9 py-2 text-xs sm:text-sm font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer appearance-none focus:outline-none focus:ring-1 focus:ring-slate-400 shadow-none">
                        <option value="">Jenis: Semua Aktivitas</option>
                        @foreach($jenisList as $j)
                            <option value="{{ $j }}" {{ request('jenis_aktivitas') == $j ? 'selected' : '' }}>
                                Jenis: {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                @if(request('user_id') || request('jenis_aktivitas') || request('tanggal_mulai') || request('tanggal_selesai') || request('search'))
                    <a href="{{ route('admin.aktivitas.index') }}" class="text-xs text-rose-600 hover:text-rose-800 underline font-medium ml-1">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Log Aktivitas -->
        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-none">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#f0f4f8] text-slate-700 text-xs sm:text-sm font-semibold">
                            <th class="py-3.5 px-4 w-12 text-slate-700">No</th>
                            <th class="py-3.5 px-4 text-slate-700">Waktu</th>
                            <th class="py-3.5 px-4 text-slate-700">Pengguna</th>
                            <th class="py-3.5 px-4 text-slate-700">Role</th>
                            <th class="py-3.5 px-4 text-slate-700">Aktivitas</th>
                            <th class="py-3.5 px-4 text-slate-700">Detail</th>
                            <th class="py-3.5 px-4 text-slate-700">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($logs as $index => $log)
                            @php
                                $role = strtolower($log->user?->role ?? 'system');
                                $roleClass = match($role) {
                                    'panitia' => 'bg-purple-100 text-purple-700',
                                    'pengawas' => 'bg-amber-100 text-amber-800',
                                    'peserta' => 'bg-blue-100 text-blue-700',
                                    'admin' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };

                                $waktuFormatted = $log->waktu 
                                    ? str_replace(['Oct', 'May', 'Aug', 'Dec'], ['Okt', 'Mei', 'Agt', 'Des'], $log->waktu->format('d M, H:i:s')) 
                                    : '-';

                                $namaUser = $log->user ? $log->user->name : 'Sistem';
                                if ($log->user && $log->user->isAdmin()) {
                                    $namaUser = 'Admin';
                                } elseif ($log->user && str_contains($log->user->name, 'Bambang Hermawan')) {
                                    $namaUser = 'Bambang H.';
                                }
                            @endphp
                            <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 text-slate-500 text-sm">
                                    {{ $logs->firstItem() + $index }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-sm whitespace-nowrap">
                                    {{ $waktuFormatted }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 text-sm whitespace-nowrap">
                                    {{ $namaUser }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold {{ $roleClass }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-800 text-sm whitespace-nowrap">
                                    {{ $log->jenis_aktivitas }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-sm max-w-xs truncate" title="{{ $log->keterangan }}">
                                    {{ $log->keterangan }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-sm font-normal whitespace-nowrap">
                                    {{ $log->alamat_ip ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                    Tidak ada catatan log aktivitas ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Bar -->
            <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs sm:text-sm text-slate-600 bg-white gap-3">
                <div>
                    Menampilkan {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} entries
                </div>
                <div>
                    {{ $logs->links('vendor.pagination.mockup') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
