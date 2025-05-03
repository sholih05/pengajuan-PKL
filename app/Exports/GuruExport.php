<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuruExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Mengambil data dari model Guru.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mengambil data guru yang aktif
        return Guru::active()->get();
    }

    /**
     * Menentukan header kolom pada file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Guru', 'Nama', 'Gender', 'No Kontak', 'Email', 'Alamat', 'ID jurusan', 'Jurusan',
        ];
    }

    /**
     * Menentukan bagaimana setiap baris data dipetakan ke dalam file Excel.
     *
     * @param mixed $guru
     * @return array
     */
    public function map($guru): array
    {
        return [
            $guru->id_guru,
            $guru->nama,
            $guru->gender,
            $guru->no_kontak,
            $guru->email,
            $guru->alamat,
            $guru->jurusan->id_jurusan ?? '-', // Ambil nama jurusan yang terkait dengan guru
            $guru->jurusan->jurusan ?? '-', // Ambil nama jurusan yang terkait dengan guru
        ];
    }
}
