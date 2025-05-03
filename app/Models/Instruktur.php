<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruktur extends Model
{
    use HasFactory;
    protected $table = 'instruktur';
    protected $primaryKey = 'id_instruktur';
    public $incrementing = false;
    protected $keyType = 'string';
    // public $timestamps = false;

    protected $fillable = [
        'id_instruktur','nama', 'gender', 'no_kontak', 'email', 'alamat', 'id_dudi', 'id_user', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function dudi()
    {
        return $this->belongsTo(Dudi::class, 'id_dudi', 'id_dudi')->where('dudi.is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id')->where('user.is_active', true);
    }

    public function catatan()
    {
        return $this->hasMany(Catatan::class, 'id_instruktur', 'id_instruktur')->where('catatan.is_active', true);
    }

    public function nilaiQuesioner()
    {
        return $this->hasMany(NilaiQuesioner::class, 'id_instruktur', 'id_instruktur')->where('nilai_quisioner.is_active', true);
    }

    public function penempatan()
    {
        return $this->hasMany(Penempatan::class, 'id_instruktur', 'id_instruktur')->where('penempatan.is_active', true);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_instruktur', 'id_instruktur')->where('penilaian.is_active', true);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_instruktur', 'id_instruktur')->where('presensi.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
