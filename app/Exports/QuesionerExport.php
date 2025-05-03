<?php

namespace App\Exports;

use App\Models\Quesioner;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuesionerExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mengambil data soal beserta tahun akademik
        return Quesioner::active()
            ->with('tahunAkademik') // Menyertakan relasi tahun akademik
            ->get()
            ->map(function ($quesioner) {
                // Mengambil soal dan tahun akademik
                return [
                    'soal' => $quesioner->soal,
                    'tahun_akademik' => $quesioner->tahunAkademik ? $quesioner->tahunAkademik->tahun_akademik : null,
                ];
            });
    }

    /**
     * Mendefinisikan kolom header yang akan ditampilkan di Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Soal',
            'Tahun Akademik',
        ];
    }
}
