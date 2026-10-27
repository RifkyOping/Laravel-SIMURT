<x-app-layout>
    <x-slot name="header">
        Edit Data Pengawas
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <div class="bg-white rounded-xl border border-slate-200/90 p-6 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Perbarui Pengawas</h1>
                    <p class="text-xs text-slate-500">Edit akun pengawas ujian</p>
                </div>
                <a href="{{ route('panitia.pengawas.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('panitia.pengawas.update', $user->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Identitas (NIK)</label>
                    <input type="text" name="no_identitas" value="{{ old('no_identitas', $user->peserta?->no_identitas) }}" placeholder="16 digit NIK"
                           class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs font-mono" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Akun</label>
                    <select name="status_akun" class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs">
                        <option value="aktif" {{ in_array($user->status_akun, ['aktif', 'terverifikasi']) ? 'selected' : '' }}>Aktif / Terverifikasi</option>
                        <option value="menunggu" {{ $user->status_akun === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="nonaktif" {{ in_array($user->status_akun, ['nonaktif', 'ditolak']) ? 'selected' : '' }}>Non-Aktif / Ditolak</option>
                    </select>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-2">Kosongkan password jika tidak ingin mengubah:</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter"
                                   class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                   class="w-full text-sm px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] shadow-xs" />
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2.5">
                    <a href="{{ route('panitia.pengawas.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-[#1e385b] hover:bg-[#152740] text-white font-semibold text-sm rounded-lg shadow-xs transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
