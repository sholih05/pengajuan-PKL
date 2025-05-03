<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThnAkademik extends Model
{
    use HasFactory;

    protected $table = 'thn_akademik';
    protected $primaryKey = 'id_ta';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'tahun_akademik', 'mulai', 'selesai', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function penempatan()
    {
        return $this->hasMany(Penempatan::class, 'id_ta', 'id_ta')->where('penempatan.is_active', true);
    }

    public function quesioner()
    {
        return $this->hasMany(Quesioner::class, 'id_ta', 'id_ta')->where('quesioner.is_active', true);
    }

    public function prgObsvr()
    {
        return $this->hasMany(PrgObsvr::class, 'id_ta', 'id_ta')->where('prg_obsvr.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
