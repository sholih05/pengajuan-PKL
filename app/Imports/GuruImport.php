<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * Mengonversi baris Excel menjadi model guru.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // Jika id_guru sudah ada, update atau buat data baru
        $guru = Guru::updateOrCreate(
            ['id_guru' => $row['id_guru']], // Mencari berdasarkan id_guru
            [
                'nama' => $row['nama'],
                'gender' => $row['gender'],
                'no_kontak' => $row['no_kontak'],
                'email' => $row['email'],
                'alamat' => $row['alamat'],
                'id_jurusan' => $row['id_jurusan'],
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]
        );

        // Jika guru baru dibuat, buatkan user baru
        $userData = User::where('username', $row['id_guru'])->first();
        if (!$userData) {
            // Membuat user baru menggunakan id_guru sebagai username
            $user = User::create([
                'username' => $row['id_guru'], // Gunakan id_guru sebagai username
                'password' => Hash::make($row['id_guru']), // Gunakan id_guru sebagai password yang di-hash
                'role' => '3', // Role 3, atau sesuaikan dengan kebutuhan Anda
            ]);

            // Hubungkan user dengan guru yang baru dibuat
            $guru->update([
                'id_user' => $user->id,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);
        }

        return $guru;
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
