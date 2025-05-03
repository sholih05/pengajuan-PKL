<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    // Nama tabel
    protected $table = 'presensi';

    // Primary key
    protected $primaryKey = 'id_presensi';

    // Tipe primary key
    public $incrementing = true;

    // Apakah timestamps diaktifkan?
    public $timestamps = true;

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'tanggal',
        'masuk',
        'pulang',
        'kegiatan',
        'foto_masuk',
        'foto_pulang',
        'is_acc_instruktur',
        'is_acc_guru',
        'catatan',
        'created_by',
        'updated_by',
        'is_active',
        'id_penempatan',
    ];

    // Kolom dengan nilai default
    protected $attributes = [
        'is_active' => 1,
    ];

    // Relasi ke tabel Penempatan
    public function penempatan()
    {
        return $this->belongsTo(Penempatan::class, 'id_penempatan', 'id_penempatan');
    }

    // Relasi ke tabel Siswa melalui Penempatan
    public function siswa()
    {
        return $this->hasOneThrough(Siswa::class, Penempatan::class, 'id_penempatan', 'nis', 'id_penempatan', 'nis');
    }

    // Relasi ke tabel Guru melalui Penempatan
    public function guru()
    {
        return $this->hasOneThrough(Guru::class, Penempatan::class, 'id_penempatan', 'id_guru', 'id_penempatan', 'id_guru');
    }

    // Relasi ke tabel Instruktur melalui Penempatan
    public function instruktur()
    {
        return $this->hasOneThrough(Instruktur::class, Penempatan::class, 'id_penempatan', 'id_instruktur', 'id_penempatan', 'id_instruktur');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
