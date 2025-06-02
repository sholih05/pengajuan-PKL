<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Penilaian extends Model
{
    use HasFactory;
    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
       'nilai_instruktur', 'waktu_instruktur', 'id','id_instruktur', 'nis', 'id_prg_obsvr', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis')->where('siswa.is_active', true);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru')->where('guru.is_active', true);
    }

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur');
    }

    public function prgObsvr()
    {
        return $this->belongsTo(PrgObsvr::class, 'id_prg_obsvr', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function hitungNilaiAkhir($id_siswa)
    {
        try {
            // Ambil semua penilaian siswa yang aktif
            $penilaian = self::with('prgObsvr')
                ->where('nis', $id_siswa)
                ->where('is_active', 1)
                ->get();

            if ($penilaian->isEmpty()) {
                return 0;
            }

            $totalNilai = 0;
            $jumlahIndikator = 0;

            // Hitung total nilai dan jumlah indikator yang dinilai
            foreach ($penilaian as $p) {
                // Pastikan indikator ini adalah indikator yang dinilai
                if ($p->prgObsvr && $p->prgObsvr->is_nilai == '1') {
                    $totalNilai += $p->nilai;
                    $jumlahIndikator++;
                }
            }

            // Jika tidak ada indikator yang dinilai, kembalikan 0
            if ($jumlahIndikator == 0) {
                return 0;
            }

            // Hitung rata-rata nilai
            $nilaiAkhir = $totalNilai / $jumlahIndikator;

            // Bulatkan ke 2 desimal
            return round($nilaiAkhir, 2);
        } catch (\Exception $e) {
            Log::error('Error in Penilaian::hitungNilaiAkhir: ' . $e->getMessage());
            return 0;
        }
    }
}