<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Observasi PKL - {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 3px 0;
            font-size: 13pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
        }
        .info-table .label {
            width: 25%;
            font-weight: normal;
            vertical-align: top;
        }
        .info-table .separator {
            width: 5%;
            text-align: center;
        }
        .info-table .value {
            width: 70%;
            border-bottom: 1px dotted #000;
        }
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .nilai-table th {
            text-align: center;
            font-weight: bold;
        }
        .nilai-table .indikator-utama {
            font-weight: normal;
        }
        .nilai-table .indikator-child {
            padding-left: 20px;
        }
        .nilai-cell {
            text-align: center;
        }
        .ya-tidak {
            text-align: center;
        }
        .circle-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }
        .circle {
            display: inline-block;
            width: 30px;
            height: 20px;
            border: 1px solid #000;
            border-radius: 50%;
            text-align: center;
            position: relative;
        }
        .circle.selected {
            background-color: #ddd;
        }
        .nilai-box {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
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
        .score-fraction {
            position: absolute;
            left: -30px;
            top: 50%;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            @page {
                margin: 1cm;
                size: 210mm 330mm; /* F4 paper size */
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LEMBAR OBSERVASI PRAKTIK KERJA LAPANGAN</h2>
        <h2>SMK NEGERI 1 SLAWI</h2>
        <h2>TAHUN PELAJARAN {{ $tahunAkademik->tahun_akademik ?? date('Y') . '/' . (date('Y') + 1) }}</h2>
    </div>
    
    <table class="info-table">
        <tr>
            <td class="label">Nama Peserta Didik</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->nama }}</td>
        </tr>
        <tr>
            <td class="label">Tempat PKL</td>
            <td class="separator">:</td>
            <td class="value">{{ $penempatan->dudi->nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Guru Pembimbing</td>
            <td class="separator">:</td>
            <td class="value">{{ $penempatan->guru->nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Projek PKL</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->projek_pkl ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Kompetensi Keahlian</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->jurusan->jurusan ?? 'N/A' }}</td>
        </tr>
    </table>
    
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="50%">Tujuan Pembelajaran/Indikator</th>
                <th width="20%">Ketercapaian Ya/Tidak</th>
                <th width="25%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @php $mainNo = 1; @endphp
            @foreach($mainIndicators as $mainIndicator)
                @php 
                    // Prepare arrays to track achieved and needs improvement sub-indicators
                    $achievedSubIndicators = [];
                    $needsImprovementSubIndicators = [];
                    
                    // Process children to categorize them
                    
                    if($mainIndicator->children && $mainIndicator->children->count() > 0) {
                        $subNo = 1;
                        foreach($mainIndicator->children as $index => $child) {
                            $subNumber = $mainNo . '.' . ($subNo);
                            if($child->prgObsvr && $child->prgObsvr->is_nilai == 1) {
                                $achievedSubIndicators[] = $subNumber;
                            } else {
                                $needsImprovementSubIndicators[] = $subNumber;
                            }
                            $subNo++;
                        }
                    }
                @endphp
                
                <tr class="indikator-utama">
                    <td>{{ $mainNo }}.</td>
                    <td>{{ $mainIndicator->indikator }}</td>
                    <td></td>
                    <td style="" rowspan="{{ $mainIndicator->children->count() + 2 }}">
                        <p style="margin: 5px;">Peserta didik sudah mampu menerapkan {{ strtolower(preg_replace('/^Menerapkan\s+/i', '', $mainIndicator->indikator)) }} sesuai harapan dalam</p>
                        <p style="margin: 5px;">{{ implode(', ', $achievedSubIndicators) ?: '................................' }} (Y)</p>
                        <p style="margin: 5px;">namun masih perlu ditingkatkan dalam hal {{ implode(', ', $needsImprovementSubIndicators) ?: '.................... ' }}(T)</p>
                    </td>
                </tr>
                
                @if($mainIndicator->children && $mainIndicator->children->count() > 0)
                    @php $subIndex = 1; @endphp
                    @foreach($mainIndicator->children as $child)
                        <tr>
                            <td></td>
                            <td class="indikator-child">{{ $mainNo }}.{{ $subIndex }} {{ $child->indikator }}</td>
                            <td class="ya-tidak">
                                <div class="circle-container">
                                    <div class="{{ $child->prgObsvr && $child->prgObsvr->is_nilai == 1 ? 'circle selected' : '' }}">
                                        Ya
                                    </div>
                                    /
                                    <div class="{{ $child->prgObsvr && $child->prgObsvr->is_nilai == 0 ? 'circle selected' : '' }}">
                                        Tidak
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @php $subIndex++; @endphp
                    @endforeach
                @endif
                
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold;">NILAI</td>
                    <td colspan="1" class="nilai-box">
                        <div style="position: relative;">
                            @if($mainIndicator->nilai)
                                {{ number_format($mainIndicator->nilai->nilai_instruktur, 0) }}
                            @else
                                -
                            @endif
                        </div>
                    </td>
                </tr>
                @php $mainNo++; @endphp
            @endforeach
                <tr>
                    <td colspan="4" style="text-align: center; font-weight: bold;">CATATAN PENILAIAN</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <p>{{ $catatanText ?? 'Tidak ada catatan' }}</p>
                    </td>
                </tr>
        </tbody>
    </table>

    <h4 style="margin-top: 20px; margin-bottom: 0px;">INTERVAL NILAI</h4>
<table class="nilai-table" style="width: 350px; font-size: 12px; border-collapse: collapse; border-spacing: 0;">
    <thead>
        <tr>
            <th style="text-align: center;">NILAI</th>
            <th style="text-align: center;">PREDIKAT</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody style="border-spacing: 0.5px;">
        <tr>
            <td style="text-align: center;">86 - 100</td>
            <td style="text-align: center;">A</td>
            <td>Sangat Baik</td>
        </tr>
        <tr>
            <td style="text-align: center;">71 - 85</td>
            <td style="text-align: center;">B</td>
            <td>Baik</td>
        </tr>
        <tr>
            <td style="text-align: center;">56 - 70</td>
            <td style="text-align: center;">C</td>
            <td>Cukup Baik</td>
        </tr>
        <tr>
            <td style="text-align: center;">0 - 55</td>
            <td style="text-align: center;">D</td>
            <td>Perlu Perbaikan</td>
        </tr>
    </tbody>
</table>

    
    <div class="ttd">
        <div class="">

        </div>
        <div class="ttd-item">
            <p>Slawi, {{ date('d F Y') }}</p>
            <p>Instruktur PKL</p>
            <div class="ttd-space"></div>
            <p>{{ $siswa->guru->nama ?? '____________________________' }}</p>
        </div>
    </div>
    
    <!-- Page break before the second form -->
    <div style="page-break-before: always;"></div>
    
    <!-- Second form based on the uploaded image -->
    <div class="second-form">
        <div class="header" style="margin-bottom: 20px;">
            <h2>DAFTAR NILAI PRAKTIK KERJA LAPANGAN</h2>
            <h2>SMK NEGERI 1 SLAWI</h2>
            <h2>TAHUN PELAJARAN {{ $tahunAkademik->tahun_akademik ?? date('Y') . '/' . (date('Y') + 1) }}</h2>
        </div>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 30%;">Nama Peserta Didik</td>
                <td style="width: 5%;">:</td>
                <td style="width: 65%; border-bottom: 1px dotted #000;">{{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td>NISN</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $siswa->kelas ?? '' }}</td>
            </tr>
            <tr>
                <td>Program Keahlian</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $siswa->jurusan->jurusan ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Tempat PKL</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $penempatan->dudi->nama ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Tanggal PKL</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">
                    Mulai : {{ $penempatan->tanggal_mulai ?? '........................' }} <br>
                    Selesai : {{ $penempatan->tanggal_selesai ?? '........................' }}
                </td>
            </tr>
            <tr>
                <td>Nama Guru Pembimbing</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $penempatan->guru->nama ?? 'N/A' }}</td>
            </tr>
        </table>
        
        <table class="nilai-table" style="width: 100%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 45%;">Tujuan Pembelajaran</th>
                    <th style="width: 10%;">Skor</th>
                    <th style="width: 40%;">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mainIndicators as $index => $mainIndicator)
                    <tr>
                        <td>{{ $loop->iteration }}.</td>
                        <td>{{ $mainIndicator->indikator }}</td>
                        <td style="text-align: center;">{{ $mainIndicator->nilai->nilai_instruktur ?? '' }}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="font-weight: bold;">Catatan</td>
                </tr>
                <tr>
                    <td colspan="4" style="height: 100px; vertical-align: top; padding: 10px;">{{ $catatanText ?? '' }}</td>
                </tr>
            </tbody>
        </table>
        
        <p style="font-style: italic; font-size: 11pt; margin-bottom: 20px;">
            Ket : Pada kolom deskripsi hanya memindahkan apa yang ada dalam kolom deskripsi pada lembar observasi
        </p>
        
        <table class="nilai-table" style="width: 50%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th colspan="2">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%;">Sakit</td>
                    <td style="width: 50%;">: ...................... Hari</td>
                </tr>
                <tr>
                    <td>Ijin</td>
                    <td>: ...................... Hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>: ...................... Hari</td>
                </tr>
            </tbody>
        </table>
        
        <div class="ttd" style="margin-top: 50px;">
            <div class=""></div>
            <div class="ttd-item">
                <p>........................., {{ date('Y') }}</p>
                <p>Guru Pembimbing</p>
                <div class="ttd-space"></div>
                <p>{{ $penempatan->guru->nama ?? '____________________________' }}</p>
                <p>NIP. {{ $penempatan->guru->id_guru ?? '' }}</p>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
