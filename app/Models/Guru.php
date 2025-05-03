<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = false;
    protected $keyType = 'string';
    // public $timestamps = false;

    protected $fillable = [
        'id_guru', 'nama', 'gender', 'no_kontak', 'email', 'alamat', 'id_user', 'id_jurusan', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan')->where('jurusan.is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id')->where('is_active', true);
    }

    public function penempatan()
    {
        return $this->hasMany(Penempatan::class, 'id_guru', 'id_guru')->where('penempatan.is_active', true);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_guru', 'id_guru')->where('penilaian.is_active', true);
    }

    public function prgObsvr()
    {
        return $this->hasMany(PrgObsvr::class, 'id_guru', 'id_guru')->where('prg_obsvr.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
