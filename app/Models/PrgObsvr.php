<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrgObsvr extends Model
{
    use HasFactory;
    protected $table = 'prg_obsvr';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'indikator', 'is_nilai', 'id_ta', 'id_guru', 'id_jurusan', 'id1', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru')->where('guru.is_active', true);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan')->where('jurusan.is_active', true);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(ThnAkademik::class, 'id_ta', 'id_ta')->where('thn_akademik.is_active', true);
    }

    public function parent()
    {
        return $this->belongsTo(PrgObsvr::class, 'id1', 'id')->where('prg_obsvr.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
