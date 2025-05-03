<?php

namespace App\Exports;

use App\Models\Dudi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DudiExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Dudi::active()->get(['id_dudi', 'nama', 'alamat', 'no_kontak', 'nama_pimpinan', 'longitude', 'latitude','radius',]);
    }

    /**
     * Mendefinisikan kolom header yang akan ditampilkan di Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Dudi',
            'Nama Dudi',
            'Alamat',
            'No. Kontak',
            'Nama Pimpinan',
            'Longitude',
            'Latitude',
            'Radius Absensi'
        ];
    }
}
