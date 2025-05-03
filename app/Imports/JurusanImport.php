<?php

namespace App\Imports;

use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JurusanImport implements ToModel, WithHeadingRow
{
    /**
     * Transform each row into a Jurusan model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek apakah id_jurusan sudah ada, jika ada update, jika tidak insert
        return Jurusan::updateOrCreate(
            ['id_jurusan' => $row['id_jurusan']], // Kunci untuk upsert
            [
                'jurusan' => $row['jurusan'],
                'singkatan' => $row['singkatan'],
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]
        );
    }
}
