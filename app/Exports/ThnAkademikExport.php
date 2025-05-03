<?php

namespace App\Exports;

use App\Models\ThnAkademik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ThnAkademikExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ThnAkademik::active()->get(['tahun_akademik', 'mulai', 'selesai']);
    }
    public function headings(): array
    {
        return ['tahun_akademik', 'mulai', 'selesai'];
    }
}
