---
name: simurt-frontend-consistency
description: "Standardize and enforce UI/UX consistency across Laravel Blade views and Tailwind CSS components in the SIMURT project. Trigger whenever creating, editing, reviewing, or refactoring Blade templates, buttons, color palettes, status badges, tables, forms, modals, or layouts, or when the user asks to fix inconsistent UI elements, button shapes, or colors."
license: MIT
metadata:
  author: simurt
---

# SIMURT Frontend Consistency

Maintain a unified, professional visual language across all roles (Admin, Panitia, Pengawas, Peserta, and Auth) in the SIMURT Laravel Blade and Tailwind CSS application. This skill eliminates visual drift in button shapes, brand colors, status badges, form controls, and table layouts.

## When to Use

- Creating or updating any Blade template under `resources/views/` or reusable component under `resources/views/components/`
- Fixing inconsistent button colors (such as `bg-blue-600`, `bg-gray-800`, or `bg-slate-900` vs `#1e385b`) or button border radii (`rounded` vs `rounded-md` vs `rounded-lg` vs `rounded-xl`)
- Standardizing table row actions (Edit, Hapus, Detail, Setujui, Tolak, Izin, Tegur, Blokir) across Admin, Panitia, Pengawas, and Peserta pages
- Refactoring status badges, KPI cards, filter toolbars, form inputs, or modals to match the SIMURT design system
- Replacing emoji characters in UI templates with clean inline SVG icons

## Reference Files

| Concern | File |
| --- | --- |
| Canonical Tailwind tokens and HTML/Blade snippets for buttons, badges, tables, cards, and inputs | [`references/component-recipes.md`](references/component-recipes.md) |
| File-by-file UI inconsistency audit and refactoring checklist for SIMURT | [`rules/audit-checklist.md`](rules/audit-checklist.md) |

## Steps

### 1. Inspect Target Views and Reference Recipes

Before editing any Blade file, read `references/component-recipes.md` to load the canonical HTML and Tailwind class combinations, and check `rules/audit-checklist.md` for known inconsistencies in the target view.

### 2. Apply the Canonical Color Palette

Use a single source of truth for brand and semantic colors so pages across different roles look like the same application:

- **Primary Brand Navy**: Use `bg-[#1e385b]` with `hover:bg-[#152843]` and `text-white`. Never use `bg-blue-600`, `bg-indigo-600`, `bg-gray-800`, or `bg-slate-900` for primary actions, and do not mix `#152740` or `#162a45` hover variants.
- **Neutral Surfaces and Borders**: Use `bg-white` for cards, tables, and modals, `bg-[#f8fafc]` for guest layouts, and `border-slate-200` for container borders. Use `bg-[#f0f4f8] text-slate-700` for all table headers (`<thead><tr>`).
- **Typography Hierarchy**: Use `text-slate-900 font-bold tracking-tight` for headings, `text-slate-700` for primary body text, and `text-slate-500` for subtitles, timestamps, and helper text. Avoid mixing `gray-*` utilities from default Laravel Breeze templates.
- **Semantic Status Colors**: Use Tailwind palette utilities consistently instead of arbitrary hex codes (`#d1fae5`, `#dcfce7`, `#fef3c7`, `#fee2e2`, `#e8f0fe`):
  - **Success (Aktif, Terverifikasi, Lulus, Siap, Izin)**: `bg-emerald-50 text-emerald-700 border border-emerald-200` for badges; `bg-emerald-600 hover:bg-emerald-700 text-white` for solid action buttons.
  - **Warning (Menunggu, Berlangsung, Peringatan, Tegur)**: `bg-amber-50 text-amber-700 border border-amber-200` for badges and banners; `bg-amber-500 hover:bg-amber-600 text-white` for solid action buttons.
  - **Danger (Ditolak, Diskualifikasi, Terputus, Hapus, Blokir)**: `bg-rose-50 text-rose-700 border border-rose-200` for badges; `border border-rose-300 text-rose-600 bg-white hover:bg-rose-50` for outline table actions; `bg-rose-600 hover:bg-rose-700 text-white` for destructive confirmations. Never mix `red-*` and `rose-*`.
  - **Info (Akan Datang, Konfirmasi)**: `bg-blue-50 text-blue-700 border border-blue-200`.
  - **Neutral (Selesai, Belum Mulai, Non-Aktif)**: `bg-slate-100 text-slate-600 border border-slate-200`.

### 3. Standardize Button Shapes, Sizes, and Micro-Interactions

Every interactive button must use `rounded-lg` and include the `.tactile-btn` class (defined in `resources/css/app.css`) for consistent press feedback:

- **Primary Button (Tambah, Simpan, Masuk, Download/Export Utama)**:
  `inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn`
- **Secondary Outline Button (Batal, Kembali, Import, Filter Popover)**:
  `inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-medium transition tactile-btn`
- **Table Row Action - Neutral (Edit, Detail)**:
  `inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition tactile-btn`
- **Table Row Action - Danger (Hapus, Tolak)**:
  `inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-rose-600 text-xs font-medium transition tactile-btn`
- **Table Row Action - Primary (Setujui, Kelola Soal, Masuk Ujian)**:
  `inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1e385b] hover:bg-[#152843] text-white text-xs font-medium shadow-xs transition tactile-btn`
- **Filter Tab Pills**:
  `pill-tab px-5 py-2 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap` combined with `bg-[#1e385b] text-white shadow-xs` when active or `bg-white border border-slate-200 text-slate-700 hover:bg-slate-50` when inactive.

### 4. Unify Cards, Tables, Forms, and Modals

- **Cards and Table Containers**: Use `bg-white rounded-xl border border-slate-200 shadow-xs`. Add `card-lift` to KPI stat cards.
- **Form Inputs, Selects, and Textareas**: Use `rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b] transition`. Replace any `rounded-xl border-slate-300 focus:ring-2 focus:ring-blue-500` found on edit forms.
- **Destructive Actions**: Always require user confirmation (either via `onsubmit="return confirm('...');"` or an Alpine.js confirmation modal) for Hapus, Tolak, and Blokir/Diskualifikasi actions.
- **Inline SVG Icons Only**: Never use emoji characters in headings, alerts, empty states, or buttons. Use clean `stroke="currentColor"` SVG icons (`w-4 h-4` inside buttons/inputs, `w-5 h-5` inside alerts/navigation).

### 5. Verify Visual Consistency and Build Assets

Run verification checks after modifying Blade or CSS files:

1. Grep the modified views to confirm no stray legacy classes remain:
   `grep -rnE "bg-blue-600|focus:ring-blue-500|border-slate-700|bg-\[#152740\]|bg-\[#162a45\]" resources/views/`
2. Ensure Blade components in `resources/views/components/` (`primary-button.blade.php`, `secondary-button.blade.php`, `danger-button.blade.php`, `text-input.blade.php`) use the SIMURT palette instead of default Breeze `gray-*` / `indigo-*` classes.
3. Run `npm run build` so Vite compiles any newly referenced Tailwind utility classes into `public/build/`, then run relevant Pest feature tests (`php artisan test --compact`) to verify no view rendering or text assertions were broken.

## Gotchas

- **Edit pages vs Index modals**: Edit pages (`admin/users/edit.blade.php`, `panitia/peserta/edit.blade.php`, `panitia/paket/edit.blade.php`, `panitia/jadwal/edit.blade.php`) previously used `bg-blue-600 rounded-xl` and `focus:ring-blue-500`, whereas index modals used `bg-[#1e385b] rounded-lg`. Always align edit pages with the `#1e385b` and `rounded-lg` standard.
- **Plain-text table actions**: `panitia/peserta/index.blade.php` and `panitia/pengawas/index.blade.php` used plain text links (`hover:underline`) for Edit and Hapus while other tables used bordered buttons. Always use the bordered `rounded-lg` button style for table actions.
- **Harsh dark borders**: Several Panitia and Peserta views used `border-slate-700` on cards, secondary buttons, and inactive filter tabs. Replace `border-slate-700` with `border-slate-200` (for containers/secondary buttons) or `border-slate-300` (for small table action buttons).
- **Preserve test selectors and text**: Feature tests in `tests/Feature/` assert specific Indonesian labels (such as button text, status labels, and pagination text). Only update CSS classes and SVG icons unless the user explicitly asks to change copy.
