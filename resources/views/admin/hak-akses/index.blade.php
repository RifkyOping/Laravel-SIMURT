<x-app-layout>
    <x-slot name="header">
        Manajemen Hak Akses
    </x-slot>

    <div class="space-y-6">
        <!-- Subheader / Title Section -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Hak Akses</h1>
            <p class="text-sm text-slate-500 mt-1">Tentukan hak akses setiap pengguna berdasarkan role.</p>
        </div>

        <!-- Role Tabs / Pills (Peserta, Panitia, Pengawas, Admin) -->
        <div class="flex items-center gap-3 pt-1 overflow-x-auto">
            @php
                $roles = [
                    'peserta' => 'Peserta',
                    'panitia' => 'Panitia',
                    'pengawas' => 'Pengawas',
                    'admin' => 'Admin',
                ];
            @endphp

            @foreach($roles as $roleKey => $roleLabel)
                <a href="{{ route('admin.hak-akses.index', ['role' => $roleKey]) }}"
                   class="pill-tab px-8 py-2.5 rounded-lg font-medium text-sm transition whitespace-nowrap {{ $selectedRole === $roleKey ? 'bg-[#1e385b] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    {{ $roleLabel }}
                </a>
            @endforeach
        </div>

        <!-- Matriks Hak Akses Form -->
        <div class="pt-2">
            <h2 class="text-base font-bold text-slate-900 mb-4">
                Konfigurasi Izin: {{ ucfirst($selectedRole) }}
            </h2>

            <form method="POST" action="{{ route('admin.hak-akses.update') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $selectedRole }}">

                <!-- Table Container with Rounded Border -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#f0f4f8] text-slate-700 text-sm font-semibold border-b border-slate-200">
                                    <th class="py-3.5 px-6 text-left w-2/6">Modul Sistem</th>
                                    <th class="py-3.5 px-4 text-center w-1/6">Lihat</th>
                                    <th class="py-3.5 px-4 text-center w-1/6">Tambah</th>
                                    <th class="py-3.5 px-4 text-center w-1/6">Edit</th>
                                    <th class="py-3.5 px-4 text-center w-1/6">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($moduls as $modul)
                                    @php
                                        $item = $hakAksesMap[$modul] ?? null;
                                    @endphp
                                    <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-6 font-semibold text-slate-800 text-sm">
                                            {{ $modul }}
                                        </td>
                                        
                                        <!-- Lihat -->
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center">
                                                <label class="w-18 sm:w-20 h-8 rounded-lg bg-slate-50/80 border border-slate-100/90 flex items-center justify-center cursor-pointer hover:bg-slate-100/90 hover:border-slate-200 active:scale-95 transition-all duration-150">
                                                    <input type="checkbox" 
                                                           name="permissions[{{ $modul }}][can_view]" 
                                                           value="1"
                                                           {{ $item && $item->can_view ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 border-slate-300 transition-transform active:scale-90 cursor-pointer">
                                                </label>
                                            </div>
                                        </td>
                                        
                                        <!-- Tambah -->
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center">
                                                <label class="w-18 sm:w-20 h-8 rounded-lg bg-slate-50/80 border border-slate-100/90 flex items-center justify-center cursor-pointer hover:bg-slate-100/90 hover:border-slate-200 active:scale-95 transition-all duration-150">
                                                    <input type="checkbox" 
                                                           name="permissions[{{ $modul }}][can_create]" 
                                                           value="1"
                                                           {{ $item && $item->can_create ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 border-slate-300 transition-transform active:scale-90 cursor-pointer">
                                                </label>
                                            </div>
                                        </td>

                                        <!-- Edit -->
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center">
                                                <label class="w-18 sm:w-20 h-8 rounded-lg bg-slate-50/80 border border-slate-100/90 flex items-center justify-center cursor-pointer hover:bg-slate-100/90 hover:border-slate-200 active:scale-95 transition-all duration-150">
                                                    <input type="checkbox" 
                                                           name="permissions[{{ $modul }}][can_update]" 
                                                           value="1"
                                                           {{ $item && $item->can_update ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 border-slate-300 transition-transform active:scale-90 cursor-pointer">
                                                </label>
                                            </div>
                                        </td>

                                        <!-- Hapus -->
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center">
                                                <label class="w-18 sm:w-20 h-8 rounded-lg bg-slate-50/80 border border-slate-100/90 flex items-center justify-center cursor-pointer hover:bg-slate-100/90 hover:border-slate-200 active:scale-95 transition-all duration-150">
                                                    <input type="checkbox" 
                                                           name="permissions[{{ $modul }}][can_delete]" 
                                                           value="1"
                                                           {{ $item && $item->can_delete ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-[#1e385b] focus:ring-[#1e385b] focus:ring-offset-0 border-slate-300 transition-transform active:scale-90 cursor-pointer">
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#1e385b] hover:bg-[#152841] text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2 tactile-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
