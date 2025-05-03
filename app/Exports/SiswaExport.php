<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaExport implements FromCollection, WithHeadings
{
    /**
     * Mengambil data dari model Siswa dan mengembalikannya sebagai koleksi.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Siswa::active()
            ->get([
                'nis',
                'nisn',
                'nama',
                'tempat_lahir',
                'tanggal_lahir',
                'golongan_darah',
                'gender',
                'foto',
                'no_kontak',
                'email',
                'alamat',
                'id_jurusan',
                'nama_wali',
                'alamat_wali',
                'no_kontak_wali',
                'status_bekerja',
            ])
            ->map(function($siswa) {
                // Generate URL asset for foto if exists
                $siswa->foto = $siswa->foto ? asset('storage/' . $siswa->foto) : null;
                return $siswa;
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
            'NIS',
            'NISN',
            'Nama',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Golongan Darah',
            'Gender',
            'Foto',
            'No Kontak',
            'Email',
            'Alamat',
            'ID Jurusan',
            'Nama Wali',
            'Alamat Wali',
            'No Kontak Wali',
            'Status Bekerja',
        ];
    }
}
