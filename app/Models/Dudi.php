<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
    use HasFactory;
    protected $table = 'dudi';
    protected $primaryKey = 'id_dudi';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'nama', 'alamat', 'no_kontak', 'longitude', 'latitude','radius', 'nama_pimpinan', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function instruktur()
    {
        return $this->hasMany(Instruktur::class, 'id_dudi', 'id_dudi')->where('instruktur.is_active', true);
    }

    public function ketersediaan()
    {
        return $this->hasMany(Ketersediaan::class, 'id_dudi', 'id_dudi')->where('ketersediaan.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
