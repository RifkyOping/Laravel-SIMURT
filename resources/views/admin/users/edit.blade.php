<x-app-layout>
    <x-slot name="header">
        Edit Akun Pengguna
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Perbarui Data Pengguna</h1>
                    <p class="text-xs text-slate-500">Edit informasi profil, peran, dan status akun</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Role Akun</label>
                        <select name="role" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                            <option value="peserta" {{ $user->role === 'peserta' ? 'selected' : '' }}>Peserta</option>
                            <option value="panitia" {{ $user->role === 'panitia' ? 'selected' : '' }}>Panitia</option>
                            <option value="pengawas" {{ $user->role === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Akun</label>
                        <select name="status_akun" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                            <option value="aktif" {{ $user->status_akun === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="terverifikasi" {{ $user->status_akun === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="menunggu" {{ $user->status_akun === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="ditolak" {{ $user->status_akun === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="nonaktif" {{ $user->status_akun === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[11px] text-slate-500 mb-2">Kosongkan jika tidak ingin mengubah password:</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter"
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
