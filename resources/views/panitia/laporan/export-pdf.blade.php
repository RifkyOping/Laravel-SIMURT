<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Eksekutif Ujian — SIMURT</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=4">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 10.5pt; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #e2e8f0; padding: 8px 10px; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 text-slate-900 font-sans">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 print-shadow-none">
        <!-- Action Buttons -->
        <div class="no-print flex justify-end gap-2 mb-6">
            <button onclick="window.print()" class="px-4 py-2 bg-[#1e385b] text-white rounded-xl text-xs font-bold shadow-xs hover:bg-[#152740] transition">
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Tutup
            </button>
        </div>

        <!-- Header Laporan -->
        <div class="text-center pb-6 border-b-2 border-slate-900 mb-6 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">SISTEM MANAJEMEN UJIAN (SIMURT)</h1>
            <h2 class="text-base font-semibold text-slate-700">Laporan Eksekutif Pelaksanaan Ujian</h2>
            <p class="text-xs text-slate-500">
                Periode: <span class="font-bold text-slate-700">{{ ucfirst(str_replace('_', ' ', $periode)) }}</span> | Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WITA
            </p>
        </div>

        <!-- 4 Kartu Ringkasan -->
        <div class="grid grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-6 text-center">
            <div>
                <span class="text-slate-500 block mb-1">Total Ujian:</span>
                <span class="font-bold text-xl text-slate-900">{{ $totalUjian }}</span>
            </div>
            <div>
                <span class="text-slate-500 block mb-1">Total Peserta:</span>
                <span class="font-bold text-xl text-slate-900">{{ number_format($totalPesertaTerdaftar) }}</span>
            </div>
            <div>
                <span class="text-slate-500 block mb-1">Rata-rata Kelulusan:</span>
                <span class="font-bold text-xl text-emerald-600">{{ $rataRataKelulusan }}%</span>
            </div>
            <div>
                <span class="text-slate-500 block mb-1">Total Pelanggaran:</span>
                <span class="font-bold text-xl text-rose-600">{{ $totalPelanggaran }}</span>
            </div>
        </div>

        <!-- Tren Tingkat Kelulusan -->
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase text-slate-700 mb-3 tracking-wider">Tren Tingkat Kelulusan Per Ujian (%)</h3>
            <div class="space-y-2.5">
                @foreach($trenKelulusan as $item)
                    <div class="flex items-center text-xs">
                        <span class="font-bold text-slate-800 w-72 flex-shrink-0">{{ $item['nama'] }}</span>
                        <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden mx-4 border border-slate-200">
                            <div class="h-full bg-[#1e385b] rounded-full" style="width: {{ $item['persen'] }}%;"></div>
                        </div>
                        <span class="font-bold text-slate-800 w-10 text-right">{{ $item['persen'] }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tabel Rekapitulasi Ujian -->
        <h3 class="text-xs font-bold uppercase text-slate-700 mb-3 tracking-wider">Rekapitulasi Pelaksanaan Ujian</h3>
        <table class="w-full text-left text-xs mb-8">
            <thead>
                <tr class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700 border-b border-slate-300">
                    <th class="p-2.5">Nama Ujian</th>
                    <th class="p-2.5">Tanggal</th>
                    <th class="p-2.5">Peserta</th>
                    <th class="p-2.5 text-center">Rata-rata Nilai</th>
                    <th class="p-2.5 text-center">Kelulusan (%)</th>
                    <th class="p-2.5 text-center">Pelanggaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($rekapRows as $row)
                    <tr>
                        <td class="p-2.5 font-bold text-slate-900">{{ $row['nama_ujian'] }}</td>
                        <td class="p-2.5 text-slate-600">{{ $row['tanggal'] }}</td>
                        <td class="p-2.5 text-slate-600">{{ $row['peserta_str'] }}</td>
                        <td class="p-2.5 text-center font-mono font-bold">{{ $row['rata_nilai'] }}</td>
                        <td class="p-2.5 text-center font-bold text-emerald-600">{{ $row['kelulusan_persen'] }}%</td>
                        <td class="p-2.5 text-center font-bold {{ $row['total_pelanggaran'] > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                            {{ $row['total_pelanggaran'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signature -->
        <div class="grid grid-cols-2 text-xs pt-8 border-t border-slate-200">
            <div>
                <p class="font-semibold text-slate-700">SIMURT — Sistem Manajemen Ujian</p>
                <p class="text-slate-400 text-[10px]">Autentikasi & Integritas Terverifikasi</p>
            </div>
            <div class="text-right space-y-12">
                <p class="text-slate-600">Panitia Pelaksana Ujian,</p>
                <p class="font-bold underline text-slate-900">{{ Auth::user()->name ?? 'Budi Santoso' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
