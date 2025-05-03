<?php

namespace App\Imports;

use App\Models\Instruktur;
use App\Models\User;
use App\Models\Dudi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class InstrukturImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * Mengonversi baris Excel menjadi model Instruktur.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cari id_dudi yang valid (misalnya, id_dudi harus ada di tabel Dudi)
        $dudi = Dudi::find($row['id_dudi']);
        if (!$dudi) {
            return null; // Skip jika id_dudi tidak ditemukan
        }

        // Jika id_instruktur sudah ada, update atau buat data baru
        $instruktur = Instruktur::updateOrCreate(
            ['id_instruktur' => $row['id_instruktur']], // Mencari berdasarkan id_instruktur
            [
                'nama' => $row['nama'],
                'gender' => $row['gender'],
                'no_kontak' => $row['no_kontak'],
                'email' => $row['email'],
                'alamat' => $row['alamat'],
                'id_dudi' => $row['id_dudi'],
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]
        );

        // Jika instruktur baru dibuat, buatkan user baru
        $userData = User::where('username', $row['id_instruktur'])->first();
        if (!$userData) {
            // Membuat user baru menggunakan id_instruktur sebagai username
            $user = User::create([
                'username' => $row['id_instruktur'], // Gunakan id_instruktur sebagai username
                'password' => Hash::make($row['id_instruktur']), // Gunakan id_instruktur sebagai password yang di-hash
                'role' => '4', // Role 4, atau sesuaikan dengan kebutuhan Anda
            ]);

            // Hubungkan user dengan instruktur yang baru dibuat
            $instruktur->update([
                'id_user' => $user->id,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);
        }

        return $instruktur;
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
