<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat PKL</title>
    <style>
    /* A4 page settings with reduced margins */
    @page {
        size: A4;
        margin: 1.2cm; /* Minimized margins for optimal space */
    }

    body {
        font-family: "Times New Roman", Times, serif;
        margin: 0;
        padding: 0;
        line-height: 1.4; /* Compact line spacing */
        background-color: #fff;
    }

    .content {
        max-width: 19cm; /* Adjusted to A4 printable area */
        margin: 0 auto;
        padding: 0.5cm; /* Minimized internal padding */
    }

    /* Header styles */
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 5px;
        border-bottom: 2px solid black;
    }

    .header img {
        max-width: 70px; /* Smaller logos for space efficiency */
        height: auto;
    }

    .header-center {
        text-align: center;
        flex: 1;
        padding: 0 10px;
        display: flex;
        align-items: center;
        margin : 0.01px;
        justify-content: space-between;
        padding-bottom: 7px;
        border-bottom: 1px solid black;
    }

    .header-center h1 {
        font-size: 14px; /* Reduced font size */
        margin: 0;
    }

    .header-center h2 {
        font-size: 12px; /* Smaller heading for better fit */
        margin: 3px 0;
    }

    .header-center p {
        font-size: 10px;
        margin: 2px 0;
    }

    /* Letter header */
    .letter-header {
        margin-top: 10px;
        font-size: 11px; /* Slightly smaller text */
    }

    .letter-header table {
        width: 100%;
    }

    /* Table styles */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 10px; /* Compact text for tables */
    }

    .table th, .table td {
        border: 1px solid black;
        padding: 4px; /* Smaller padding for more rows */
        text-align: center;
    }

    /* Signature */
    .signature {
        margin-top: 20px; /* Compact spacing */
        text-align: right;
        font-size: 11px;
    }

    .signature .name {
        margin-top: 30px;
        font-weight: bold;
        text-decoration: underline;
    }

    /* Footer */
    .footer-section {
        margin-top: 10px;
        font-size: 10px;
    }

    .footer-section table {
        width: 100%;
        border-top: 2px solid black;
        padding-top: 5px;
    }

    .footer-section img {
        max-width: 70px; /* Smaller logos */
        height: auto;
    }

    /* Ensure everything stays on one page */
    @media print {
        body {
            margin: 0;
        }
        .header, .content, .footer-section {
            page-break-inside: avoid;
        }
    }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
    <img src="{{ public_path('assets/img/SMK_KARBAK.png') }}" alt="School Logo" style="position: absolute; top: 0; left: 0;">
        <div class="header-center">
            <h1>YAYASAN PENDIDIKAN EKONOMI</h1>
            <h2>SMK KARYA BHAKTI BREBES</h2>
            <p>Jl. Taman Siswa No. 1 Telp. (0283) 671284, 673241 Fax. (0283) 67184 Brebes 52212</p>
            <p>Website: <a href="http://www.smk-karyabhakti.fk" target="_blank">www.smk-karyabhakti.fk</a> 
                Email: <a href="mailto:karyabhakti.brebes@gmail.com">karyabhakti.brebes@gmail.com</a></p>
            <p><strong>Terakreditasi: A</strong></p>
        </div>
    <img src="{{ public_path('assets/img/Pictures/logo delta.png') }}" alt="Delta PAS Logo" style="position: absolute; top: 0; right: 0; height: 70 px; width: 95px;">
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Letter Header -->
        <div class="letter-header">
            <table>
                <tr>
                    <td style="width: 70%; vertical-align: top;">
                        <p>Nomor: 143/I.03.5.SMK-KB/M 2025</p>
                        <p>Lampiran: -</p>
                        <p>Hal: <strong>Permohonan Ijin Praktik Kerja Lapangan (PKL)</strong></p>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: top;">
                        <p>Brebes, {{ \Carbon\Carbon::now()->locale('id_ID')->translatedFormat('d F Y') }}</p>
                    </td>
                </tr>
            </table>
            Kepada Yth.<br>
                {{ $surat->kepada_yth }}
                <br>Di tempat
            </p>
        </div>

        <p>
            Dalam rangka menumbuhkembangkan karakter dan budaya kerja yang profesional pada Peserta Didik,
            meningkatkan kompetensi Peserta Didik sesuai kurikulum dan kebutuhan dunia kerja,
            dan menyiapkan kemandirian Peserta Didik untuk bekerja dan/atau berwirausaha,
            maka pada tahun pelajaran 2025/2026, SMK Karya Bhakti Brebes akan melaksanakan program
            Praktik Kerja Lapangan (PKL) bagi Peserta Didik kelas XI pada Industri dan Dunia Kerja
            (IDUKA) yang dianggap relevan.
        </p>

        <p>
            Sehubungan hal tersebut, maka kami mohon bantuan Bapak/Ibu/Saudara selaku
            Pimpinan/Kepala/Manager untuk berkenan menerima Peserta Didik kami guna melaksanakan PKL
            di institusi yang Bapak/Ibu/Saudara pimpin dengan waktu pelaksanaan mulai dari tanggal
            <strong>{{$surat->tanggal_mulai}} sampai dengan {{$surat->tanggal_selesai}}</strong>.
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Kompetensi Keahlian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nama }}</td>
                    <td>{{ $student->jurusan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p>Demikian permohonan ini kami sampaikan, atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.</p>

        <!-- Signature -->
        <div class="signature">
            <p>Brebes, Mei 2023</p>
            <p>Kepala Sekolah,</p>
            <div class="name">
                ADHI NUR ARIFIANTO, SH
            </div>
            <p>NIY. 5257</p>
        </div>
    </div>

    <!-- Footer Section -->
    <div style="position: fixed; bottom: 0; width: 100%" class="footer-section">
        <table>
            <tr>
                <td>
                <img src="{{ public_path('assets/img/Pictures/kan.png') }}" alt="KAN Logo">
                </td>
                <td style="text-align: center;">
                    <strong>Kompetensi Keahlian:</strong><br>
                    Teknik Komputer dan Jaringan, Rekayasa Perangkat Lunak, Multimedia,<br>
                    Akuntansi Keuangan dan Lembaga, Otomatisasi dan Tata Kelola Perkantoran,<br>
                    Bisnis Daring dan Pemasaran
                </td>
                <td>
                <img src="{{ public_path('assets/img/iaf.jpg') }}" alt="IAF Logo">
                </td>
            </tr>
        </table>
    </div>
    
</body>
</html>
