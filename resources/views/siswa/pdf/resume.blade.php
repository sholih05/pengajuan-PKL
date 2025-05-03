<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PDF</title>
    <style>
        /* Reset margin dan padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            /* Menurunkan jarak antar baris */
            padding: 10px;
            /* Mengurangi padding */
            background-color: #fff;
            font-size: 12px;
            /* Mengurangi ukuran font secara keseluruhan */
            margin: 1cm;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            /* Mengurangi margin bawah */
        }

        .header h2 {
            font-size: 18px;
            /* Mengurangi ukuran font */
            margin-bottom: 5px;
            /* Mengurangi margin bawah */
        }

        .header p {
            font-size: 12px;
            /* Mengurangi ukuran font */
            color: #777;
        }

        .content {
            margin-bottom: 10px;
            /* Mengurangi margin bawah */
        }

        .content h2 {
            font-size: 14px;
            /* Mengurangi ukuran font */
            margin-bottom: 8px;
            /* Mengurangi margin bawah */
        }

        .content p {
            font-size: 12px;
            /* Mengurangi ukuran font */
            margin-bottom: 6px;
            /* Mengurangi jarak antar paragraf */
            color: #555;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* Mengurangi margin bawah */
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 5px;
            /* Mengurangi padding */
            text-align: left;
            font-size: 10px;
            /* Mengurangi ukuran font di dalam tabel */
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: normal;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            /* Mengurangi margin atas */
            font-size: 12px;
            /* Mengurangi ukuran font */
            color: #777;
        }

        .footer p {
            margin: 5px 0;
            /* Mengurangi jarak antar elemen */
        }

        /* Menghapus bullet dan angka pada daftar */
        ol,
        ul {
            padding-left: 0;
        }

        /* Menambahkan indentasi pada item utama */
        ol {
            margin-left: 20px;
        }

        /* Memberikan sedikit indentasi pada sublist */
        ul {
            margin-left: 20px;
        }

        /* Mengatur posisi titik dua agar tetap rapi */
        .label {
            display: inline-block;
            width: 250px;
            /* Atur lebar label */
        }

        .value {
            display: inline-block;
        }

        /* Menyesuaikan layout untuk halaman kecil (Mobile/Tablet) */
        @media (max-width: 600px) {
            body {
                font-size: 10px;
                /* Mengurangi ukuran font untuk layar kecil */
            }

            .header h2 {
                font-size: 16px;
                /* Mengurangi ukuran font untuk header */
            }

            .table th,
            .table td {
                font-size: 10px;
                /* Mengurangi ukuran font di tabel */
            }

            .footer p {
                font-size: 10px;
                /* Mengurangi ukuran font footer */
            }
        }
    </style>

</head>

<body>

    <div>
        <!-- Header -->
        <div class="header">
            <h2>IDENTITAS INSTANSI/DUNIA INDUSTRI DAN</h2>
            <h2>IDENTITAS SISWA PESERTA PKL</h2>
        </div>

        <br>
        <br>
        <div class="content">
            <ol>
                <li>
                    <b>IDENTITAS INSTANSI/DUNIA INDUSTRI</b>
                    <ul style="list-style: none;">
                        <li><span class="label">a. Nama Dunia Usaha/Industri/Instansi</span>&nbsp; : &nbsp;&nbsp; {{ $penempatan->dudi->nama }}</li>
                        <li><span class="label">b. Alamat</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->dudi->alamat }}</li>
                        <li><span class="label">c. Nomor Telepon / Faximile</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->dudi->no_kontak }}</li>
                        <li><span class="label">d. Nama Pimpinan/Direktur</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->dudi->nama_pimpinan }}</li>
                        <li><span class="label">e. Nama Pembimbing/Instruktur</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->instruktur->nama }}</li>
                    </ul>
                </li>
                <br>
                <li>
                    <b>IDENTITAS SISWA PESERTA PKL</b>
                    <ul style="list-style: none;">
                        <li><span class="label">a. Nama Lengkap</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->nama }} ( {{ $penempatan->siswa->gender }} )</li>
                        <li><span class="label">b. Nomor Induk Siswa (NIS)</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->nis }}</li>
                        <li><span class="label">c. Tempat Tanggal Lahir</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($penempatan->siswa->tanggal_lahir)->format('d M Y') }}</li>
                        <li><span class="label">d. Golongan Darah</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->golongan_darah }}</li>
                        <li><span class="label">e. Sekolah</span>&nbsp; : &nbsp;&nbsp; SMK Negeri 1 Slawi</li>
                        <li><span class="label">f. Alamat Sekolah</span>&nbsp; : &nbsp;&nbsp; Jl. H. Agus
                            Salim - Kab. Tegal</li>
                        <li><span class="label">g. Nomor Telepon / Faximile</span>&nbsp; : &nbsp;&nbsp; (0283)
                            491366 Fax. 491336</li>
                        <li><span class="label">h. Nama Orang Tua/Wali</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->nama_wali }}</li>
                        <li><span class="label">i. Alamat Orang Tua/Wali</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->alamat_wali }}</li>
                        <li><span class="label">j. No. Telepon Orang Tua/Wali</span>&nbsp; : &nbsp;&nbsp;
                            {{ $penempatan->siswa->no_kontak_wali }}</li>
                    </ul>
                </li>
            </ol>
            <br>
            <br>
            <div style="text-align: center;">
                @if ($penempatan->siswa->foto)
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/'.$penempatan->siswa->foto))) }}"
                alt="Profile" width="250" id="profileImage">
                @endif
            </div>
        </div>


        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ $date }}</p>
        </div>
    </div>

</body>

</html>
