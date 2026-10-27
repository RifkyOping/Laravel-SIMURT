<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Ujian — {{ $jadwal->nama_ujian }}</title>
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
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Tutup
            </button>
        </div>

        <div class="text-center pb-6 border-b-2 border-slate-900 mb-6 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wider">LAPORAN RESMI PELAKSANAAN UJIAN SELEKSI</h1>
            <h2 class="text-base font-semibold">{{ $jadwal->nama_ujian }}</h2>
            <p class="text-xs text-slate-500">
                Tanggal: {{ $jadwal->tanggal->translatedFormat('d F Y') }} | Durasi: {{ $jadwal->durasi }} Menit | Waktu: {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
            </p>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-6 text-center">
            <div>
                <span class="text-slate-500 block">Total Peserta:</span>
                <span class="font-bold text-base">{{ $totalPeserta }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Kehadiran:</span>
                <span class="font-bold text-base">{{ $totalHadir }} ({{ $hadirPersen }}%)</span>
            </div>
            <div>
                <span class="text-slate-500 block">Rata-rata Nilai:</span>
                <span class="font-bold text-base text-blue-600">{{ $rataRataNilai }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Pelanggaran:</span>
                <span class="font-bold text-base text-rose-600">{{ $totalPelanggaran }}</span>
            </div>
        </div>

        <!-- Daftar Nilai Peserta -->
        <h3 class="text-sm font-bold mb-2">Hasil Evaluasi Peserta:</h3>
        <table class="w-full text-left text-xs mb-8">
            <thead>
                <tr class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700">
                    <th class="p-2 text-center">No</th>
                    <th class="p-2">Nama Peserta</th>
                    <th class="p-2">NIK</th>
                    <th class="p-2 text-center">Benar</th>
                    <th class="p-2 text-center">Salah</th>
                    <th class="p-2 text-center">Nilai Akhir</th>
                    <th class="p-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($jadwal->nilaiUjians as $idx => $nl)
                    <tr>
                        <td class="p-2 text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="p-2 font-bold">{{ $nl->peserta->user->name }}</td>
                        <td class="p-2 font-mono text-slate-600">{{ $nl->peserta->no_identitas }}</td>
                        <td class="p-2 text-center font-semibold text-emerald-700">{{ $nl->jumlah_benar }}</td>
                        <td class="p-2 text-center font-semibold text-rose-700">{{ $nl->jumlah_salah }}</td>
                        <td class="p-2 text-center font-mono font-bold">{{ $nl->nilai_akhir }}</td>
                        <td class="p-2 text-center font-bold">{{ $nl->status_kelulusan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Log Pelanggaran jika ada -->
        @if($jadwal->pelanggarans->isNotEmpty())
            <h3 class="text-sm font-bold mb-2 text-rose-800">Catatan Pelanggaran & Diskualifikasi:</h3>
            <table class="w-full text-left text-xs mb-8">
                <thead>
                    <tr class="bg-rose-50 text-rose-900 uppercase text-[10px] font-bold">
                        <th class="p-2">Waktu</th>
                        <th class="p-2">Nama Peserta</th>
                        <th class="p-2">Jenis Pelanggaran</th>
                        <th class="p-2">Keterangan</th>
                        <th class="p-2">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($jadwal->pelanggarans as $plg)
                        <tr>
                            <td class="p-2 font-mono">{{ $plg->waktu->format('H:i:s') }}</td>
                            <td class="p-2 font-bold">{{ $plg->peserta->user->name }}</td>
                            <td class="p-2 font-bold text-rose-700">{{ $plg->jenis_pelanggaran }}</td>
                            <td class="p-2 text-slate-600">{{ $plg->keterangan }}</td>
                            <td class="p-2 font-semibold">{{ $plg->tindakan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="grid grid-cols-2 text-xs pt-8 border-t border-slate-200">
            <div>
                <p class="text-slate-500">SIMURT — Sistem Manajemen Ujian</p>
                <p class="text-slate-400 text-[10px]">Autentikasi & Verifikasi Offline Terjamin</p>
            </div>
            <div class="text-right space-y-12">
                <p>Panitia Pelaksana Ujian,</p>
                <p class="font-bold underline">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>
</body>
</html>
