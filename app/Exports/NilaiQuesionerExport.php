<?php

namespace App\Exports;

use App\Models\NilaiQuesioner;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NilaiQuesionerExport implements FromCollection, WithHeadings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Mengambil data Catatan berdasarkan id tertentu.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data Catatan berdasarkan id jika ada
        $query = NilaiQuesioner::active()->with([
            'siswa', // Relasi siswa
            'quesioner', // Relasi quesioner
            'thnAkademik'
        ]);

        // Jika id diberikan, filter berdasarkan id
        if (!empty($this->id)) {
            $query->where('nis', $this->id);
        }

        // Ambil data Catatan
        $data = $query->get()->map(function ($catatan) {
            return [
                'tanggal' => $catatan->tanggal,
                'tahun_akademik' => $catatan->thnAkademik->tahun_akademik,
                'quesioner' => $catatan->quesioner->soal,
                'nilai'=> $catatan->nilai == '1' ? 'Ya' : ($catatan->nilai == '0' ? 'Tidak' : ''),
                'nis' => $catatan->nis,
                'nama_siswa' => $catatan->siswa->nama,
            ];
        });

        return $data;
    }

    /**
     * Menambahkan heading pada file Excel yang diexport.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Tahun Akademik',
            'Quesioner',
            'Jawaban',
            'NIS',
            'Nama',
        ];
    }
}
