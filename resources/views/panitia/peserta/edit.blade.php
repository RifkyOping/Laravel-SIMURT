<x-app-layout>
    <x-slot name="header">
        Edit Data Peserta
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Perbarui Data Peserta</h1>
                    <p class="text-xs text-slate-500">Edit informasi identitas dan status akun peserta</p>
                </div>
                <a href="{{ route('panitia.peserta.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('panitia.peserta.update', $user->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Identitas (NIK)</label>
                    <input type="text" name="no_identitas" value="{{ old('no_identitas', $user->peserta?->no_identitas) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Asal Sekolah / Instansi</label>
                    <input type="text" name="instansi" value="{{ old('instansi', $user->peserta?->instansi) }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Akun</label>
                    <select name="status_akun" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <option value="terverifikasi" {{ $user->status_akun === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi (Disetujui)</option>
                        <option value="menunggu" {{ $user->status_akun === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="ditolak" {{ $user->status_akun === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('panitia.peserta.index') }}" class="px-4 py-2.5 border border-slate-200 rounded-xl text-slate-700 text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
