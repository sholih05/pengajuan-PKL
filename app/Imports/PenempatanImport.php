<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Instruktur;
use App\Models\Penempatan;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenempatanImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $id_ta;

    // Menerima id_ta dari controller
    public function __construct($id_ta)
    {
        $this->id_ta = $id_ta;
    }

    /**
     * Mengonversi baris Excel menjadi model guru.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validasi jika data siswa, guru, instruktur, dan dudi ada
        $siswa = Siswa::where('nis', $row['nis'])->first();
        $guru = Guru::where('id_guru', $row['id_guru'])->first();
        $instruktur = Instruktur::where('id_instruktur', $row['id_instruktur'])->first();

        // Pastikan semua data yang dibutuhkan ada
        if ($siswa && $guru && $instruktur) {
            // Jika nis sudah ada, update atau buat data baru
            return Penempatan::updateOrCreate(
                ['nis' => $row['nis'],
                'id_ta' => $this->id_ta], // Mencari berdasarkan nis
                [
                    'nis' => $row['nis'],
                    'id_ta' => $this->id_ta,
                    'id_guru' => $guru->id_guru,
                    'id_instruktur' => $instruktur->id_instruktur,
                    'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                ]
            );
        }
        // Jika ada data yang tidak valid, kembalikan null
        return null;
    }

    /**
     * Menentukan ukuran chunk saat membaca data dari file Excel.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // Menggunakan chunk sebesar 500 baris
    }
}
