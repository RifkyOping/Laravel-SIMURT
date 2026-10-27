# SIMURT Design Tokens and Component Recipes

Use these canonical Tailwind CSS + Blade snippets whenever building or refactoring views in SIMURT.

## 1. Design Tokens Summary

| Token Category | Canonical Utility Classes | Forbidden / Legacy Variants |
| :--- | :--- | :--- |
| Primary Brand Background | `bg-[#1e385b] hover:bg-[#152843] text-white` | `bg-blue-600`, `bg-gray-800`, `bg-slate-900`, `hover:bg-[#152740]`, `hover:bg-[#162a45]` |
| Primary Focus Ring | `focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]` | `focus:ring-2 focus:ring-blue-500`, `focus:ring-indigo-500`, `focus:ring-slate-400` |
| Button Border Radius | `rounded-lg` (all buttons, inputs, tabs) | `rounded`, `rounded-md`, `rounded-xl`, `rounded-full` (on buttons/tabs) |
| Card / Table Container | `bg-white rounded-xl border border-slate-200 shadow-xs` | `rounded-2xl`, `border-slate-700`, `shadow-md` |
| Table Header Row | `bg-[#f0f4f8] text-slate-700 text-xs font-semibold border-b border-slate-200` | `bg-slate-50`, `bg-slate-50/70`, `bg-slate-100 uppercase` |
| Success Badge | `bg-emerald-50 text-emerald-700 border border-emerald-200` | `bg-[#d1fae5] text-[#065f46]`, `bg-[#dcfce7] text-[#15803d]` |
| Warning Badge | `bg-amber-50 text-amber-700 border border-amber-200` | `bg-[#fef3c7] text-[#92400e]`, `bg-[#fef3c7] text-[#b45309]` |
| Danger Badge | `bg-rose-50 text-rose-700 border border-rose-200` | `bg-[#fee2e2] text-[#991b1b]`, `bg-[#fee2e2] text-[#dc2626]`, `bg-red-100` |
| Info Badge | `bg-blue-50 text-blue-700 border border-blue-200` | `bg-[#e8f0fe] text-[#1a73e8]` |
| Neutral Badge | `bg-slate-100 text-slate-600 border border-slate-200` | `bg-[#eef2f6] text-[#64748b]`, `bg-[#f1f5f9] text-[#64748b]` |

## 2. Canonical Component Snippets

### A. Buttons

```html
<!-- 1. Primary CTA Button (Tambah, Simpan, Masuk, Export Utama) -->
<button type="button"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    <span>Tambah Data</span>
</button>

<!-- 2. Secondary / Outline Button (Batal, Kembali, Import, Export Sekunder) -->
<button type="button"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-medium transition tactile-btn">
    <span>Batal</span>
</button>

<!-- 3. Danger Solid Button (Konfirmasi Hapus, Diskualifikasi, Selesai Ujian) -->
<button type="submit"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn">
    <span>Konfirmasi Diskualifikasi</span>
</button>
```

### B. Table Row Action Buttons

```html
<div class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
    <!-- Edit / Detail -->
    <a href="#"
       class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn">
        Edit
    </a>

    <!-- Primary Table Action (Setujui / Kelola Soal / Masuk Ujian) -->
    <button type="submit"
            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs font-medium shadow-xs transition tactile-btn">
        Setujui
    </button>

    <!-- Danger Table Action (Hapus / Tolak) -->
    <form method="POST" action="#" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-rose-600 text-xs font-medium transition tactile-btn">
            Hapus
        </button>
    </form>
</div>
```

### C. Proctoring Quick Action Buttons (Pengawas)

```html
<div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
    <button type="button"
            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition tactile-btn">
        Izin
    </button>
    <button type="button"
            class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition tactile-btn">
        Tegur
    </button>
    <button type="button"
            class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition tactile-btn">
        Blokir
    </button>
</div>
```

### D. Status Badges

```html
<!-- Success: Aktif / Terverifikasi / Lulus / Siap -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
    Terverifikasi
</span>

<!-- Warning: Menunggu / Berlangsung / Peringatan -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
    Menunggu
</span>

<!-- Danger: Ditolak / Diskualifikasi / Terputus / Tidak Lulus -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
    Ditolak
</span>

<!-- Info: Akan Datang -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
    Akan Datang
</span>

<!-- Neutral: Selesai / Belum Mulai / Non-Aktif -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
    Selesai
</span>
```

### E. Page Header, KPI Stat Card, and Table Structure

```html
<!-- Page Title & Subtitle -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Judul Halaman</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Deskripsi singkat fungsi halaman.</p>
    </div>
</div>

<!-- KPI Stat Card -->
<div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 shadow-xs card-lift">
    <span class="text-xs sm:text-sm font-medium text-slate-600 block">Label Statistik</span>
    <span class="text-3xl font-extrabold text-slate-900 tracking-tight block mt-2">128</span>
</div>

<!-- Data Table Container -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-[#f0f4f8] text-slate-700 text-xs font-semibold border-b border-slate-200">
                    <th class="py-3.5 px-4 sm:px-6 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 sm:px-6">Nama</th>
                    <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
                    <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <tr class="table-row-hover hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 sm:px-6 text-center text-slate-500 text-xs">1</td>
                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-900">Contoh Nama</td>
                    <td class="py-3.5 px-4 sm:px-6 text-center">...</td>
                    <td class="py-3.5 px-4 sm:px-6 text-center">...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### F. Form Controls (Inputs, Selects, Textareas)

```html
<div>
    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Label Input</label>
    <input type="text"
           class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] transition" />
</div>
```
