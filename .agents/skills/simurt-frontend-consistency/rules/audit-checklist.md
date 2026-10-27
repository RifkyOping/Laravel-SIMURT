# SIMURT File-by-File UI Inconsistency Audit Checklist

Use this checklist when auditing or refactoring views in `resources/views/` to ensure complete UI consistency across all roles.

## 1. Reusable Blade Components (`resources/views/components/`)

- [ ] `primary-button.blade.php`: Replace `bg-gray-800 rounded-md uppercase tracking-widest focus:ring-indigo-500` with `bg-[#1e385b] hover:bg-[#152843] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn`.
- [ ] `secondary-button.blade.php`: Replace `border-gray-300 rounded-md uppercase tracking-widest` with `border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-xs sm:text-sm font-medium transition tactile-btn`.
- [ ] `danger-button.blade.php`: Replace `bg-red-600 rounded-md uppercase tracking-widest` with `bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn`.
- [ ] `text-input.blade.php`: Replace `border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md` with `rounded-lg border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] transition`.
- [ ] `input-label.blade.php`: Replace `text-sm text-gray-700` with `text-xs font-semibold text-slate-700 mb-1.5`.

## 2. Admin Views (`resources/views/admin/`)

- [ ] `admin/dashboard.blade.php`: Change table action buttons (`Edit`, `Hapus`) from `rounded` to `rounded-lg` (`px-3 py-1.5`).
- [ ] `admin/users/index.blade.php`: Change table action buttons (`Edit`, `Hapus`) from `rounded` to `rounded-lg` (`px-3 py-1.5`), and align input focus rings to `focus:ring-[#1e385b] focus:border-[#1e385b]`.
- [ ] `admin/users/edit.blade.php`:
  - Replace card `rounded-2xl` with `rounded-xl border-slate-200`.
  - Replace input/select `rounded-xl border-slate-300 focus:ring-2 focus:ring-blue-500` with `rounded-lg border-slate-200 focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]`.
  - Replace submit button `bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md shadow-blue-500/20` with `bg-[#1e385b] hover:bg-[#152843] rounded-lg shadow-xs tactile-btn`, and cancel button `rounded-xl` with `rounded-lg`.

## 3. Panitia Views (`resources/views/panitia/`)

- [ ] `panitia/dashboard.blade.php`:
  - Standardize table headers from `bg-slate-50/70` to `bg-[#f0f4f8] text-slate-700 text-xs font-semibold`.
  - Change `Setujui` button from `rounded-md hover:bg-[#152740]` to `rounded-lg hover:bg-[#152843] tactile-btn`.
  - Change `Edit` button from `rounded-md border-slate-700` to `rounded-lg border-slate-300 bg-white hover:bg-slate-50 tactile-btn`.
  - Change `Tolak` and `Hapus` buttons from `rounded-md border-rose-500` to `rounded-lg border-rose-300 bg-white hover:bg-rose-50 text-rose-600 tactile-btn`.
- [ ] `panitia/peserta/index.blade.php` & `panitia/pengawas/index.blade.php`:
  - Replace plain-text `Edit` and `Hapus` links (`hover:underline`) with bordered `rounded-lg` table action buttons (`border-slate-300` for Edit, `border-rose-300 text-rose-600` for Hapus).
  - Standardize table headers to `bg-[#f0f4f8]` and primary button hover to `hover:bg-[#152843] tactile-btn`.
- [ ] `panitia/peserta/edit.blade.php`, `panitia/paket/edit.blade.php`, `panitia/jadwal/edit.blade.php`:
  - Replace `bg-blue-600 hover:bg-blue-700 rounded-xl` submit buttons with `bg-[#1e385b] hover:bg-[#152843] rounded-lg tactile-btn`.
  - Replace `rounded-xl border-slate-300 focus:ring-2 focus:ring-blue-500` on inputs/selects/textareas with `rounded-lg border-slate-200 focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]`.
- [ ] `panitia/paket/index.blade.php`:
  - Replace custom hex status badges (`#e8f0fe`, `#fef3c7`, `#f1f5f9`) with canonical `blue-50`, `amber-50`, and `slate-100` badges.
  - Change `Kelola Soal` button from `rounded-md` to `rounded-lg tactile-btn`.
- [ ] `panitia/soal/index.blade.php`:
  - Change `Import Soal` button border from `border-slate-700` to `border-slate-200`.
- [ ] `panitia/nilai/detail.blade.php`:
  - Change `Tutup / Kembali` button from `rounded-xl bg-slate-900` to `rounded-lg bg-[#1e385b] hover:bg-[#152843] tactile-btn`.
- [ ] `panitia/laporan/detail.blade.php`:
  - Change `Download Laporan (PDF)` button from `rounded-xl hover:bg-[#152740]` to `rounded-lg hover:bg-[#152843] tactile-btn`.
- [ ] `panitia/laporan/cetak.blade.php`, `panitia/laporan/export-pdf.blade.php`, `panitia/nilai/cetak.blade.php`:
  - Change print button from `bg-blue-600 rounded-xl` to `bg-[#1e385b] hover:bg-[#152843] rounded-lg` and replace printer emoji with an inline SVG icon.

## 4. Pengawas Views (`resources/views/pengawas/`)

- [ ] `pengawas/dashboard.blade.php`, `pengawas/monitoring/index.blade.php`, `pengawas/log/index.blade.php`:
  - Standardize table headers from `bg-slate-50` to `bg-[#f0f4f8] text-slate-700`.
  - Change `Izin`, `Tegur`, and `Blokir` buttons from `rounded` with raw hex backgrounds to `rounded-lg` with `bg-emerald-600 hover:bg-emerald-700`, `bg-amber-500 hover:bg-amber-600`, and `bg-rose-600 hover:bg-rose-700` plus `tactile-btn`.
  - Replace raw hex badge classes (`bg-[#fef3c7]`, `bg-[#d1fae5]`, `bg-[#fee2e2]`) in both Blade and Alpine `getStatusBadgeClass()` with canonical `amber-50`, `emerald-50`, and `rose-50` classes.
  - In the Block/Disqualification modal, replace `rounded-2xl` buttons/inputs with `rounded-lg` and replace the `🚫` emoji with an inline SVG icon.

## 5. Peserta & Auth Views (`resources/views/peserta/`, `resources/views/auth/`)

- [ ] `auth/login.blade.php` & `auth/register.blade.php`:
  - Change primary button hover from `hover:bg-[#162a45]` to `hover:bg-[#152843]`, and input focus rings to `focus:ring-[#1e385b] focus:border-[#1e385b]`.
- [ ] `peserta/dashboard.blade.php`:
  - Standardize table header to `bg-[#f0f4f8] text-slate-700`.
  - Replace `rounded-full` status badges and raw hexes (`#dcfce7`, `#eef2f6`) with `rounded-md` canonical badges (`emerald-50`, `slate-100`, `blue-50`).
- [ ] `peserta/jadwal/index.blade.php`:
  - Change filter tab pills from `rounded-full` and inactive `border-slate-700` to `pill-tab rounded-lg` and `border-slate-200 bg-white text-slate-700 hover:bg-slate-50`.
- [ ] `peserta/jadwal/konfirmasi.blade.php`:
  - Change `Mulai Ujian` active state from `bg-blue-600 hover:bg-blue-700 rounded-xl` to `bg-[#1e385b] hover:bg-[#152843] rounded-lg tactile-btn`.
  - Change `Kembali ke Dashboard` from `rounded-xl` to `rounded-lg tactile-btn`.
  - Replace emojis (`📜`, `📡`) with inline SVG icons.
- [ ] `peserta/hasil/index.blade.php` & `peserta/hasil/show.blade.php`:
  - Replace `border-slate-700/80` on the featured result card and `border-slate-700` on the back button with `border-slate-200`.
  - Replace emojis (`📜`, `⏳`, `🔄`) with inline SVG icons.
- [ ] `peserta/ujian/izin.blade.php`:
  - Replace `bg-blue-600` / `bg-amber-600 rounded-xl` buttons with `bg-[#1e385b] hover:bg-[#152843] rounded-lg tactile-btn` and replace emojis (`🔒`, `⏳`, `🔄`) with inline SVG icons.
