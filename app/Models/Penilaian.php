<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;
    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'nilai_guru_pembimbing', 'nilai_instruktur', 'waktu_guru_pembimbing', 'waktu_instruktur', 'id', 'id_guru', 'id_instruktur', 'nis', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
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
        return $this->belongsTo(PrgObsvr::class, 'id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
