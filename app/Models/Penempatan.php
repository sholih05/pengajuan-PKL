<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penempatan extends Model
{
    use HasFactory;
    protected $table = 'penempatan';
    protected $primaryKey = 'id_penempatan';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'nis',
        'id_ta',
        'id_guru',
        'id_instruktur',
        'projectpkl',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
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
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur')->where('instruktur.is_active', true);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(ThnAkademik::class, 'id_ta', 'id_ta')->where('thn_akademik.is_active', true);
    }

    public function dudi()
    {
        return $this->hasOneThrough(Dudi::class, Instruktur::class, 'id_instruktur', 'id_dudi', 'id_instruktur', 'id_dudi');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
