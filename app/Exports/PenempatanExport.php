<?php

namespace App\Exports;

use App\Models\Penempatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenempatanExport implements FromCollection, WithHeadings, WithChunkReading
{
    protected $id_ta;

    public function __construct($id_ta)
    {
        $this->id_ta = $id_ta;
    }
    /**
     * Mengambil data dari model Penempatan dan mengembalikannya sebagai koleksi.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mengambil data penempatan yang aktif, beserta relasi yang dibutuhkan
        return Penempatan::active()->where('id_ta', $this->id_ta)
            ->with([
                'siswa',  // Relasi Siswa
                'guru',   // Relasi Guru
                'instruktur', // Relasi Instruktur
                'tahunAkademik', // Relasi Tahun Akademik
                'dudi' // Relasi DUDI
            ])
            ->get()
            ->map(function ($penempatan) {
                return [
                    'tahun_akademik' => $penempatan->tahunAkademik->tahun_akademik,
                    'nis' => $penempatan->nis,
                    'nama_siswa' => $penempatan->siswa ? $penempatan->siswa->nama : null,  // Nama Siswa
                    'id_guru' => $penempatan->id_guru,  // ID Guru
                    'nama_guru' => $penempatan->guru ? $penempatan->guru->nama : null,  // Nama Guru
                    'id_instruktur' => $penempatan->id_instruktur,  // ID Instruktur
                    'nama_instruktur' => $penempatan->instruktur ? $penempatan->instruktur->nama : null,  // Nama Instruktur
                    'nama_dudi' => $penempatan->dudi ? $penempatan->dudi->nama : null,  // Nama Dudi
                ];
            });
    }

    /**
     * Menambahkan heading pada file Excel yang diexport.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tahun Akademik',
            'NIS',
            'Nama Siswa',
            'ID Guru',
            'Nama Guru',
            'ID Instruktur',
            'Nama Instruktur',
            'Nama DUDI'
        ];
    }

    /**
     * Menentukan ukuran chunk saat membaca data dari file Excel.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // Menggunakan chunk sebesar 500 baris
    }
}
