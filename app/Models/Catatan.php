<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catatan extends Model
{
    use HasFactory;
    protected $table = 'catatan';
    protected $primaryKey = 'id_catatan';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'tanggal', 'catatan', 'kategori', 'id_instruktur', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur')->where('is_active', true);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
