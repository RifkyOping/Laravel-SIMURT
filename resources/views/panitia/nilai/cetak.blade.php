<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Ujian — SIMURT</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=4">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11pt; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 6px; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 text-slate-900 font-sans">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <!-- Action Buttons -->
        <div class="no-print flex justify-end gap-2 mb-6">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow hover:bg-blue-700">
                🖨️ Cetak Dokumen / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Tutup
            </button>
        </div>

        <!-- Header Laporan -->
        <div class="text-center pb-6 border-b-2 border-slate-900 mb-6 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wider">PANITIA SELEKSI UJIAN BERBASIS KOMPUTER</h1>
            <h2 class="text-base font-semibold">REKAPITULASI NILAI AKHIR UJIAN PESERTA</h2>
            <p class="text-xs text-slate-500">
                Ujian: {{ $jadwal ? $jadwal->nama_ujian : 'Seluruh Jadwal Ujian' }} — Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
            </p>
        </div>

        <!-- Tabel Nilai -->
        <table class="w-full text-left text-xs mb-8">
            <thead>
                <tr class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700">
                    <th class="p-2 text-center">No</th>
                    <th class="p-2">Nama Peserta</th>
                    <th class="p-2">NIK / Identitas</th>
                    <th class="p-2">Nama Ujian</th>
                    <th class="p-2 text-center">Benar</th>
                    <th class="p-2 text-center">Salah</th>
                    <th class="p-2 text-center">Nilai Akhir</th>
                    <th class="p-2 text-center">Status Kelulusan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($nilais as $index => $n)
                    <tr>
                        <td class="p-2 text-center font-mono">{{ $index + 1 }}</td>
                        <td class="p-2 font-bold">{{ $n->peserta->user->name }}</td>
                        <td class="p-2 font-mono text-slate-600">{{ $n->peserta->no_identitas }}</td>
                        <td class="p-2 text-slate-600">{{ $n->jadwal->nama_ujian }}</td>
                        <td class="p-2 text-center font-semibold text-emerald-700">{{ $n->jumlah_benar }}</td>
                        <td class="p-2 text-center font-semibold text-rose-700">{{ $n->jumlah_salah }}</td>
                        <td class="p-2 text-center font-mono font-bold text-sm">{{ $n->nilai_akhir }}</td>
                        <td class="p-2 text-center font-bold">
                            {{ $n->status_kelulusan }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 text-xs pt-8 border-t border-slate-200">
            <div>
                <p class="text-slate-500">Sistem Manajemen Ujian Real-Time (SIMURT)</p>
                <p class="text-slate-400 text-[10px]">Laporan sah hasil seleksi ujian offline LAN.</p>
            </div>
            <div class="text-right space-y-12">
                <p>Panitia Pelaksana,</p>
                <p class="font-bold underline">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>
</body>
</html>
