<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * Mengonversi baris Excel menjadi model siswa.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // Proses update atau create siswa berdasarkan NIS
        $siswa = Siswa::updateOrCreate(
            ['nis' => $row['nis']], // Mencari berdasarkan NIS
            [
                'nisn' => $row['nisn'],
                'nama' => $row['nama'],
                'tempat_lahir' => $row['tempat_lahir'],
                'tanggal_lahir' => Carbon::parse($row['tanggal_lahir'])->format('Y-m-d'),
                'golongan_darah' => $row['golongan_darah'],
                'gender' => $row['gender'],
                'no_kontak' => $row['no_kontak'],
                'email' => $row['email'],
                'alamat' => $row['alamat'],
                'id_jurusan' => $row['id_jurusan'],
                'nama_wali' => $row['nama_wali'],
                'alamat_wali' => $row['alamat_wali'],
                'no_kontak_wali' => $row['no_kontak_wali'],
                'status_bekerja' => isset($row['status_bekerja']) ? $row['status_bekerja'] : 'WFO',
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1, // Default aktif
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]
        );

        // Jika siswa baru dibuat, buatkan user baru
        $userData = User::where('username', $row['nis'])->first();
        if (!$userData) {
            // Membuat user baru menggunakan nis sebagai username
            $user = User::create([
                'username' => $row['nis'], // Gunakan nis sebagai username
                'password' => Hash::make($row['nis']), // Gunakan nis sebagai password yang di-hash
                'role' => '5', // Role 5, atau sesuaikan dengan kebutuhan Anda
            ]);

            // Hubungkan user dengan siswa yang baru dibuat
            $siswa->update([
                'id_user' => $user->id,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);
        }

        return $siswa;
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
