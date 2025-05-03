<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quesioner extends Model
{
    use HasFactory;

    protected $table = 'quesioner';
    protected $primaryKey = 'id_quesioner';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'soal', 'id_ta', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(ThnAkademik::class, 'id_ta', 'id_ta')->where('thn_akademik.is_active', true);
    }

    public function nilaiQuesioner()
    {
        return $this->hasMany(NilaiQuesioner::class, 'id_quesioner', 'id_quesioner')->where('nilai_quesioner.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
