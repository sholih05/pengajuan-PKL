<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ketersediaan extends Model
{
    use HasFactory;
    protected $table = 'ketersediaan';
    protected $primaryKey = 'id_ketersediaan';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'tanggal', 'id_jurusan', 'id_dudi', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function dudi()
    {
        return $this->belongsTo(Dudi::class, 'id_dudi', 'id_dudi')->where('dudi.is_active', true);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan')->where('jurusan.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
