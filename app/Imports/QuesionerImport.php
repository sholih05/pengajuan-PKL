<?php
namespace App\Imports;

use App\Models\Quesioner;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuesionerImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $id_ta;

    // Menerima id_ta dari controller
    public function __construct($id_ta)
    {
        $this->id_ta = $id_ta;
    }

    /**
     * Mengambil data dari file Excel dan menyimpan ke dalam koleksi.
     * Ini akan digunakan untuk batch insert.
     *
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        // Menyiapkan array untuk menyimpan data batch insert
        $data = [];

        foreach ($rows as $row) {
            $data[] = [
                'soal' => $row['soal'],
                'id_ta' => $this->id_ta, // Menambahkan id_ta yang diterima dari form
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif jika tidak ada nilai
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(), // ID pengguna yang mengupload
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(), // ID pengguna yang mengupdate
            ];
        }

        // Melakukan insert dalam batch menggunakan transaksi untuk memastikan integritas data
        DB::transaction(function () use ($data) {
            Quesioner::insert($data); // Batch insert
        });
    }

    /**
     * Menentukan ukuran batch insert
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 500; // Misalnya 500 record per batch
    }

    /**
     * Mengatur ukuran chunk saat membaca file Excel
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // Menggunakan chunk sebesar 500 baris
    }
}

