<x-app-layout>
    <x-slot name="header">
        Edit Paket Soal
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Perbarui Paket Soal</h1>
                    <p class="text-xs text-slate-500">Edit informasi paket soal ujian</p>
                </div>
                <a href="{{ route('panitia.paket.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('panitia.paket.update', $paket->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Soal</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Paket</label>
                    <textarea name="deskripsi" rows="3" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="durasi" value="{{ old('durasi', $paket->durasi) }}" min="1" required
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (0-500)</label>
                        <input type="number" step="0.01" name="passing_grade" value="{{ old('passing_grade', $paket->passing_grade) }}" min="0" max="500" required
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Paket</label>
                    <select name="status_paket" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <option value="akan_datang" {{ $paket->status_paket === 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="berlangsung" {{ $paket->status_paket === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ $paket->status_paket === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('panitia.paket.index') }}" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 text-xs font-semibold hover:bg-slate-50">
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
