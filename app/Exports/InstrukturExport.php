<?php
namespace App\Exports;

use App\Models\Instruktur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InstrukturExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * Mendapatkan collection data dari Instruktur dengan nama_dudi.
    *
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil data instruktur beserta nama dudi
        return Instruktur::active()
            ->with('dudi') // Melakukan eager loading relasi dudi
            ->get(['id_instruktur', 'nama', 'gender', 'no_kontak', 'email', 'alamat', 'id_dudi']);
    }

    /**
     * Menambahkan header kolom untuk Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Instruktur',
            'Nama Instruktur',
            'Gender',
            'No Kontak',
            'Email',
            'Alamat',
            'ID Dudi',
            'Nama Dudi', // Header baru untuk nama_dudi
        ];
    }

    /**
     * Memetakan data dari model ke format yang akan diexport ke Excel.
     *
     * @param \App\Models\Instruktur $instruktur
     * @return array
     */
    public function map($instruktur): array
    {
        return [
            $instruktur->id_instruktur,
            $instruktur->nama,
            $instruktur->gender,
            $instruktur->no_kontak,
            $instruktur->email,
            $instruktur->alamat,
            $instruktur->id_dudi,
            $instruktur->dudi ? $instruktur->dudi->nama : null, // Menambahkan nama_dudi dari relasi dudi
        ];
    }
}
