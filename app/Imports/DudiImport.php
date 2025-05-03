<?php

namespace App\Imports;

use App\Models\Dudi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class DudiImport implements ToModel, WithHeadingRow
{
    /**
     * Mengonversi baris Excel menjadi model Dudi.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Melakukan updateOrCreate data Dudi berdasarkan id_dudi
        return Dudi::updateOrCreate(
            ['id_dudi' => $row['id_dudi']], // Mencari berdasarkan id_dudi
            [
                'nama' => $row['nama'],
                'alamat' => $row['alamat'],
                'no_kontak' => $row['no_kontak'],
                'longitude' => $row['longitude'],
                'latitude' => $row['latitude'],
                'radius' => $row['radius'],
                'nama_pimpinan' => $row['nama_pimpinan'],
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(), // ID pengguna yang mengupdate
            ]
        );
    }

}
