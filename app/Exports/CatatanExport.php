<?php

namespace App\Exports;

use App\Models\Catatan;
use App\Models\ThnAkademik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatatanExport implements FromCollection, WithHeadings
{
    protected $id;
    protected $id_ta;

    public function __construct($id,$id_ta)
    {
        $this->id = $id;
        $this->id_ta = $id_ta;
    }

    /**
     * Mengambil data Catatan berdasarkan id tertentu.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $ta = ThnAkademik::find($this->id_ta);
        // Ambil data Catatan berdasarkan id jika ada
        $query = Catatan::active()->with([
            'instruktur', // Relasi Instruktur
        ])->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);

        // Jika id diberikan, filter berdasarkan id
        if (!empty($this->id)) {
            $query->where('id_instruktur', $this->id);
        }

        // Ambil data Catatan
        $data = $query->get()->map(function ($catatan) {
            return [
                'tanggal' => $catatan->tanggal,
                'catatan' => $catatan->catatan,
                'kategori'=> $catatan->kategori == 'K' ? 'Kendala' : ($catatan->kategori == 'S' ? 'Saran' : ''),
                'id_instruktur' => $catatan->id_instruktur,
                'nama_instruktur' => $catatan->instruktur->nama,
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
            'Catatan',
            'Kategori',
            'ID Instruktur',
            'Nama Instruktur',
        ];
    }
}
