<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';
    public $incrementing = false;
    protected $keyType = 'string';
    // public $timestamps = false;

    protected $fillable = [
        'id_jurusan','jurusan', 'singkatan', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_jurusan', 'id_jurusan')->where('siswa.is_active', true);
    }

    public function guru()
    {
        return $this->hasMany(Guru::class, 'id_jurusan', 'id_jurusan')->where('guru.is_active', true);
    }

    public function ketersediaan()
    {
        return $this->hasMany(Ketersediaan::class, 'id_jurusan', 'id_jurusan')->where('ketersediaan.is_active', true);
    }

    public function prgObsvr()
    {
        return $this->hasMany(PrgObsvr::class, 'id_jurusan', 'id_jurusan')->where('prg_obsvr.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
