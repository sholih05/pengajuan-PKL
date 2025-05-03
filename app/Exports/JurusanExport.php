<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JurusanExport implements FromCollection, WithHeadings
{
    /**
     * Mendapatkan data dari model Jurusan untuk diexport.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Jurusan::all(['id_jurusan', 'jurusan', 'singkatan', 'is_active']);
    }

    /**
     * Menambahkan heading pada file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['id_jurusan', 'jurusan', 'singkatan', 'is_active'];
    }
}
