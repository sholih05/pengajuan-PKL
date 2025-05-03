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
            line-height: 1.4; /* Menurunkan jarak antar baris */
            padding: 10px; /* Mengurangi padding */
            background-color: #fff;
            font-size: 12px; /* Mengurangi ukuran font secara keseluruhan */
            margin: 1cm;
        }

        .header {
            text-align: center;
            margin-bottom: 12px; /* Mengurangi margin bawah */
        }

        .header h2 {
            font-size: 18px; /* Mengurangi ukuran font */
            margin-bottom: 5px; /* Mengurangi margin bawah */
        }

        .header p {
            font-size: 12px; /* Mengurangi ukuran font */
            color: #777;
        }

        .content {
            margin-bottom: 12px; /* Mengurangi margin bawah */
        }

        .content h2 {
            font-size: 14px; /* Mengurangi ukuran font */
            margin-bottom: 8px; /* Mengurangi margin bawah */
        }

        .content p {
            font-size: 12px; /* Mengurangi ukuran font */
            margin-bottom: 6px; /* Mengurangi jarak antar paragraf */
            color: #555;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px; /* Mengurangi margin bawah */
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 5px; /* Mengurangi padding */
            text-align: left;
            font-size: 12px; /* Mengurangi ukuran font di dalam tabel */
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: normal;
        }

        .footer {
            text-align: center;
            margin-top: 10px; /* Mengurangi margin atas */
            font-size: 12px; /* Mengurangi ukuran font */
            color: #777;
        }

        .footer p {
            margin: 5px 0; /* Mengurangi jarak antar elemen */
        }

        /* Menyesuaikan layout untuk halaman kecil (Mobile/Tablet) */
        @media (max-width: 600px) {
            body {
                font-size: 10px; /* Mengurangi ukuran font untuk layar kecil */
            }

            .header h2 {
                font-size: 16px; /* Mengurangi ukuran font untuk header */
            }

            .table th, .table td {
                font-size: 10px; /* Mengurangi ukuran font di tabel */
            }

            .footer p {
                font-size: 10px; /* Mengurangi ukuran font footer */
            }
        }
    </style>

</head>
<body>

    <div >
        <!-- Header -->
        <div class="header">
            <h2>DAFTAR PRESENSI SISWA</h2>
            <h2>PESERTA PRAKTIK KERJA LAPANGAN</h2>
        </div>

        <!-- Content -->
        <div class="content">
            <table>
                <tr>
                    <td>NIS</td>
                    <td>&nbsp; : &nbsp;&nbsp;</td>
                    <td> {{ $penempatan->siswa->nis }}</td>
                </tr>
                <tr>
                    <td>NAMA SISWA</td>
                    <td>&nbsp; : &nbsp;&nbsp;</td>
                    <td> {{ $penempatan->siswa->nama }}</td>
                </tr>
                <tr>
                    <td>JURUSAN</td>
                    <td>&nbsp; : &nbsp;&nbsp;</td>
                    <td> {{ $penempatan->siswa->jurusan->jurusan }}</td>
                </tr>
                <tr>
                    <td>INSTRUKTUR</td>
                    <td>&nbsp; : &nbsp;&nbsp;</td>
                    <td> {{ $penempatan->instruktur->nama }}</td>
                </tr>
                <tr>
                    <td>TEMPAT PRAKTIK (DUDI)</td>
                    <td>&nbsp; : &nbsp;&nbsp;</td>
                    <td> {{ $penempatan->dudi->nama }}</td>
                </tr>
            </table>
        </div>

        <!-- Table -->
        <div class="content">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presensi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>
                            @if ($item->foto_masuk)
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/uploads/foto/'.$item->foto_masuk))) }}" class="logo" width="200px"> <br>
                            @endif
                            {{ \Carbon\Carbon::parse($item->masuk)->format('H:i') }}
                        </td>
                        <td>
                            @if ($item->foto_pulang)
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/uploads/foto/'.$item->foto_pulang))) }}" class="logo" width="200px"> <br>
                            @endif
                            {{ \Carbon\Carbon::parse($item->pulang)->format('H:i') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ $date }}</p>
        </div>
    </div>

</body>
</html>
