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
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 3px 0;
            font-size: 13pt;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 25%;
            font-weight: normal;
        }
        .info-table .separator {
            width: 5%;
            text-align: center;
        }
        .info-table .value {
            width: 70%;
            border-bottom: 1px dotted #000;
            min-height: 20px;
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
            font-weight: bold;
        }
        .nilai-table .indikator-child {
            padding-left: 20px;
            font-size: 11pt;
        }
        .nilai-table .indikator-grandchild {
            padding-left: 40px;
            font-size: 10pt;
        }
        .nilai-cell {
            text-align: center;
            width: 80px;
        }
        .ya-tidak {
            text-align: center;
            width: 100px;
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
            background-color: #f0f0f0;
        }
        .deskripsi-cell {
            font-size: 10pt;
            line-height: 1.3;
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
        .interval-table {
            width: 350px;
            font-size: 11pt;
            margin-bottom: 20px;
        }
        .catatan-section {
            background-color: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
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
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: LEMBAR OBSERVASI -->
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
            <td class="label">NIS</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->nis }}</td>
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
            <td class="value">{{ $projectpkl->projectpkl ?? 'N/A' }}</td>
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
                <th width="45%">Tujuan Pembelajaran/Indikator</th>
                <th width="15%">Ketercapaian Ya/Tidak</th>
                <th width="35%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @php $mainNo = 1; @endphp
            @foreach($mainIndicators as $mainIndicator)
                @php
                    // Get main indicator assessment
                    $mainPenilaian = $mainIndicatorPenilaian->where('id_prg_obsvr', $mainIndicator->id)->first();
                    $mainScore = $mainPenilaian ? $mainPenilaian->nilai_instruktur : 0;

                    // Prepare arrays to track achieved and needs improvement sub-indicators
                    $achievedSubIndicators = [];
                    $needsImprovementSubIndicators = [];
                    $totalSubRows = 0;

                    // Count total rows for this main indicator (including sub and sub-sub)
                    if($mainIndicator->children && $mainIndicator->children->count() > 0) {
                        foreach($mainIndicator->children as $child) {
                            $totalSubRows++; // Count sub indicator
                            if($child->level3Children && $child->level3Children->count() > 0) {
                                $totalSubRows += $child->level3Children->count(); // Count sub-sub indicators
                            }
                        }
                    }

                    // Process children to categorize them
                    if($mainIndicator->children && $mainIndicator->children->count() > 0) {
                        $subNo = 1;
                        foreach($mainIndicator->children as $child) {
                            $subNumber = $mainNo . '.' . $subNo;

                            // Check if this sub indicator is achieved
                            $isAchieved = false;
                            if($child->level3Children && $child->level3Children->count() > 0) {
                                // If has level 3 children, check if all are achieved
                                $allLevel3Achieved = true;
                                foreach($child->level3Children as $level3Child) {
                                    if($level3Child->is_nilai === null || $level3Child->is_nilai === 0 || $level3Child->is_nilai === '0') {
                                        $allLevel3Achieved = false;
                                        break;
                                    }
                                }
                                $isAchieved = $allLevel3Achieved;
                            } else {
                                // Direct assessment with strict comparison
                                $isAchieved = $child->is_nilai === 1 || $child->is_nilai === '1';
                            }

                            if($isAchieved) {
                                $achievedSubIndicators[] = $subNumber;
                            } else {
                                $needsImprovementSubIndicators[] = $subNumber;
                            }
                            $subNo++;
                        }
                    }

                    $totalRows = $totalSubRows + 2; // +2 for main row and nilai row
                @endphp

                {{-- Main Indicator Row --}}
                <tr class="indikator-utama">
                    <td><strong>{{ $mainNo }}.</strong></td>
                    <td><strong>{{ $mainIndicator->indikator }}</strong></td>
                    <td class="nilai-cell">

                    </td>
                    <td class="deskripsi-cell" rowspan="{{ $totalRows }}">
                        @php
                            $indicatorName = strtolower(preg_replace('/^Menerapkan\s+/i', '', $mainIndicator->indikator));
                            $achievedText = !empty($achievedSubIndicators) ? implode(', ', $achievedSubIndicators) : 'belum ada yang tercapai';
                            $improvementText = !empty($needsImprovementSubIndicators) ? implode(', ', $needsImprovementSubIndicators) : 'semua sudah tercapai';
                        @endphp
                        <p style="margin: 5px 0; font-size: 10pt;">
                            <strong>Peserta didik sudah mampu menerapkan {{ $indicatorName }} sesuai harapan dalam:</strong>
                        </p>
                        <p style="margin: 5px 0; font-size: 10pt;">
                             {{ $achievedText }} (Y)
                        </p>
                        <p style="margin: 5px 0; font-size: 10pt;">
                            <strong>Perlu ditingkatkan:</strong> {{ $improvementText }} (T)
                        </p>

                    </td>
                </tr>

                {{-- Sub Indicators --}}
                @if($mainIndicator->children && $mainIndicator->children->count() > 0)
                    @php $subIndex = 1; @endphp
                    @foreach($mainIndicator->children as $child)
                        <tr>
                            <td></td>
                            <td class="indikator-child">{{ $mainNo }}.{{ $subIndex }} {{ $child->indikator }}</td>
                            <td class="ya-tidak">
                                @if(!$child->level3Children || $child->level3Children->count() === 0)
                                    @php
                                        // Only show checkbox for sub indicators if they don't have level 3 children
                                        $subAchieved = $child->is_nilai === 1 || $child->is_nilai === '1';
                                    @endphp
                                    
                                    <div class="circle-container">
                                        <div class="{{ $subAchieved ? 'circle selected' : '' }}">
                                            Ya
                                        </div>
                                        /
                                        <div class="{{ !$subAchieved ? 'circle selected' : '' }}">
                                            Tidak
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        {{-- Level 3 Sub-Sub Indicators --}}
                        @if($child->level3Children && $child->level3Children->count() > 0)
                            @foreach($child->level3Children as $level3Child)
                                <tr>
                                    <td></td>
                                    <td class="indikator-grandchild">- {{ $level3Child->indikator }}</td>
                                    <td class="ya-tidak">
                                        <div class="circle-container">
                                            <div class="{{ $level3Child->is_nilai === 1 || $level3Child->is_nilai === '1' ? 'circle selected' : '' }}">
                                                Ya
                                            </div>
                                            /
                                            <div class="{{ $level3Child->is_nilai === 0 || $level3Child->is_nilai === '0' ? 'circle selected' : '' }}">
                                                Tidak
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @php $subIndex++; @endphp
                    @endforeach
                @endif

                {{-- Nilai Row --}}
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f0f0f0;">NILAI</td>
                    <td class="nilai-box">
                        {{ $mainScore > 0 ? number_format($mainScore, 0) : '-' }}
                    </td>
                </tr>
                @php $mainNo++; @endphp
            @endforeach

            {{-- Catatan Section --}}
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold; background-color: #e0e0e0;">CATATAN PENILAIAN</td>
            </tr>
            <tr>
                <td colspan="4" class="catatan-section">
                    <p style="margin: 5px 0;">{{ $catatanText ?: 'Tidak ada catatan khusus.' }}</p>

                </td>
            </tr>
        </tbody>
    </table>

    <h4 style="margin-top: 20px; margin-bottom: 10px;">INTERVAL NILAI</h4>
    <table class="nilai-table interval-table">
        <thead>
            <tr>
                <th style="text-align: center;">NILAI</th>
                <th style="text-align: center;">PREDIKAT</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
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
        <div class="ttd-item">
        </div>
        <div class="ttd-item">
            <p>Slawi, {{ date('d F Y') }}</p>
            <p>Pembimbing Industri</p>
            <div class="ttd-space"></div>
            <p>{{ $penempatan->instruktur->nama ?? '____________________________' }}</p>
        </div>
    </div>

    <!-- Page break before the second form -->
    <div style="page-break-before: always;"></div>

    <!-- HALAMAN 2: DAFTAR NILAI -->
    <div class="second-form">
        <div class="header" style="margin-bottom: 20px;">
            <h2>DAFTAR NILAI PRAKTIK KERJA LAPANGAN</h2>
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
                <td class="label">NISN</td>
                <td class="separator">:</td>
                <td class="value">{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="separator">:</td>
                <td class="value">{{ $siswa->kelas ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Program Keahlian</td>
                <td class="separator">:</td>
                <td class="value">{{ $siswa->jurusan->jurusan ?? 'N/A' }}</td>
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
                    @php
                        $mainPenilaian = $mainIndicatorPenilaian->where('id_prg_obsvr', $mainIndicator->id)->first();
                        $score = $mainPenilaian ? $mainPenilaian->nilai_instruktur : 0;

                        // Generate description based on sub-indicators
                        $achievedSubs = [];
                        $needsImprovementSubs = [];

                        if($mainIndicator->children && $mainIndicator->children->count() > 0) {
                            $subNo = 1;
                            foreach($mainIndicator->children as $child) {
                                $subNumber = ($index + 1) . '.' . $subNo;

                                $isAchieved = false;
                                if($child->level3Children && $child->level3Children->count() > 0) {
                                    $allLevel3Achieved = true;
                                    $level3Values = [];

                                    foreach($child->level3Children as $level3Child) {
                                        // Store the value for debugging
                                        $level3Values[] = [
                                            'indikator' => $level3Child->indikator,
                                            'is_nilai' => $level3Child->is_nilai
                                        ];

                                        // Strict comparison for is_nilai
                                        if($level3Child->is_nilai === null || $level3Child->is_nilai === 0 || $level3Child->is_nilai === '0') {
                                            $allLevel3Achieved = false;
                                            break;
                                        }
                                    }

                                    // Debug information
                                    Log::info('Level 3 Children for ' . $child->indikator, [
                                        'values' => $level3Values,
                                        'allLevel3Achieved' => $allLevel3Achieved
                                    ]);

                                    $isAchieved = $allLevel3Achieved;
                                } else {
                                    // Direct assessment with strict comparison
                                    $isAchieved = $child->is_nilai === 1 || $child->is_nilai === '1';

                                    // Debug information
                                    Log::info('Direct Level 2 Assessment for ' . $child->indikator, [
                                        'is_nilai' => $child->is_nilai,
                                        'subAchieved' => $isAchieved
                                    ]);
                                }

                                if($isAchieved) {
                                    $achievedSubs[] = $subNumber;
                                } else {
                                    $needsImprovementSubs[] = $subNumber;
                                }
                                $subNo++;
                            }
                        }

                        $description = "Tercapai: " . (!empty($achievedSubs) ? implode(', ', $achievedSubs) : 'belum ada') . " (Y). ";
                        $description .= "Perlu ditingkatkan: " . (!empty($needsImprovementSubs) ? implode(', ', $needsImprovementSubs) : 'tidak ada') . " (T).";
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}.</td>
                        <td>{{ $mainIndicator->indikator }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $score > 0 ? number_format($score, 0) : '-' }}</td>
                        <td style="font-size: 10pt;">{{ $description }}</td>
                    </tr>
                @endforeach



                {{-- Catatan Row --}}
                <tr>
                    <td colspan="4" style="font-weight: bold; background-color: #e0e0e0;">Catatan</td>
                </tr>
                <tr>
                    <td colspan="4" style="height: 80px; vertical-align: top; padding: 10px;">
                        {{ $catatanText ?: 'Peserta didik menunjukkan kemampuan yang baik dalam melaksanakan praktik kerja lapangan sesuai dengan kompetensi keahliannya.' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="font-style: italic; font-size: 11pt; margin-bottom: 20px;">
            <strong>Ket:</strong> Pada kolom deskripsi hanya memindahkan apa yang ada dalam kolom deskripsi pada lembar observasi
        </p>

        <table class="nilai-table" style="width: 50%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th colspan="2" style="background-color: #f0f0f0;">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%;">Sakit</td>
                    <td style="width: 50%;">: {{ $presensi->where('keterangan', 'Sakit')->sum('jumlah') }} Hari</td>
                </tr>
                <tr>
                    <td>Ijin</td>
                    <td>: {{ $presensi->where('keterangan', 'Izin')->sum('jumlah') }} Hari</td>
                </tr>
                <tr>
                    <td>Alpa</td>
                    <td>: {{ $presensi->where('keterangan', 'Alpha')->sum('jumlah') }} Hari</td>
                </tr>
            </tbody>
        </table>

        <div class="ttd" style="margin-top: 50px;">
            <div class="ttd-item">

            </div>
            <div class="ttd-item">
                <p>Slawi, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <p>Pembimbing Industri</p>
                <div class="ttd-space"></div>
                <p>{{ $penempatan->instruktur->nama ?? '____________________________' }}</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto print when page loads
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>