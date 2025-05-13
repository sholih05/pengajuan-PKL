<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Penilaian PKL - {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .info-table th {
            width: 30%;
            text-align: left;
            background-color: #f2f2f2;
        }
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .nilai-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .nilai-table .indikator-utama {
            background-color: #e6e6e6;
            font-weight: bold;
        }
        .nilai-table .indikator-child {
            padding-left: 20px;
        }
        .nilai-akhir {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .catatan {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .ttd {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .ttd-item {
            width: 45%;
            text-align: center;
        }
        .ttd-space {
            height: 80px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            @page {
                margin: 2cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LEMBAR PENILAIAN PRAKTIK KERJA LAPANGAN (PKL)</h2>
        <h3>TAHUN AKADEMIK {{ $siswa->tahunAkademik->tahun_akademik ?? 'N/A' }}</h3>
    </div>
    
    <h3>A. DATA SISWA</h3>
    <table class="info-table">
        <tr>
            <th>NIS</th>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <th>Nama Siswa</th>
            <td>{{ $siswa->nama }}</td>
        </tr>
        <tr>
            <th>Kelas</th>
            <td>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Jurusan</th>
            <td>{{ $siswa->jurusan->nama_jurusan ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Guru Pembimbing</th>
            <td>{{ $siswa->guru->nama ?? 'N/A' }}</td>
        </tr>
    </table>
    
    <h3>B. HASIL PENILAIAN</h3>
    <div class="nilai-akhir">
        <h3 style="margin: 0;">Nilai Akhir: {{ number_format($nilaiAkhir, 2) }}</h3>
        <p style="margin: 5px 0 0 0;">Keterangan: <strong>{{ getNilaiKeterangan($nilaiAkhir) }}</strong></p>
    </div>
    
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Indikator Penilaian</th>
                <th width="15%">Nilai</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($indikatorUtama as $indikator)
                <tr class="indikator-utama">
                    <td>{{ $no++ }}</td>
                    <td>{{ $indikator->indikator }}</td>
                    <td style="text-align: center;">
                        @php
                            $nilai = $penilaian->where('id_prg_obsvr', $indikator->id)->first();
                        @endphp
                        
                        @if($indikator->is_nilai == '1' && $nilai)
                            {{ $nilai->nilai }}
                        @else
                            Tidak Dinilai
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($indikator->is_nilai == '1' && $nilai)
                            {{ getNilaiKeterangan($nilai->nilai) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                
                @if($indikator->children->isNotEmpty())
                    @foreach($indikator->children as $child)
                        <tr>
                            <td></td>
                            <td class="indikator-child">{{ $child->indikator }}</td>
                            <td style="text-align: center;">
                                @php
                                    $nilai = $penilaian->where('id_prg_obsvr', $child->id)->first();
                                @endphp
                                
                                @if($child->is_nilai == '1' && $nilai)
                                    {{ $nilai->nilai }}
                                @else
                                    Tidak Dinilai
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($child->is_nilai == '1' && $nilai)
                                    {{ getNilaiKeterangan($nilai->nilai) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
    
    <h3>C. CATATAN PENILAIAN</h3>
    <div class="catatan">
        <p>{{ $catatanText ?: 'Tidak ada catatan' }}</p>
    </div>
    
    <div class="ttd">
        <div class="ttd-item">
            <p>Mengetahui,</p>
            <p>Kepala Jurusan {{ $siswa->jurusan->nama_jurusan ?? '' }}</p>
            <div class="ttd-space"></div>
            <p>____________________________</p>
            <p>NIP.</p>
        </div>
        <div class="ttd-item">
            <p>{{ date('d F Y') }}</p>
            <p>Guru Pembimbing</p>
            <div class="ttd-space"></div>
            <p>{{ $siswa->guru->nama ?? '____________________________' }}</p>
            <p>NIP. {{ $siswa->guru->nip ?? '' }}</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

@php
function getNilaiKeterangan($nilai) {
    if ($nilai >= 90) {
        return 'Sangat Baik';
    } elseif ($nilai >= 80) {
        return 'Baik';
    } elseif ($nilai >= 70) {
        return 'Cukup';
    } elseif ($nilai >= 60) {
        return 'Kurang';
    } else {
        return 'Sangat Kurang';
    }
}
@endphp