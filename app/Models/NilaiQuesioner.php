<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiQuesioner extends Model
{
    use HasFactory;
    protected $table = 'nilai_quesioner';
    protected $primaryKey = 'id_nilai';
    public $incrementing = true;

    protected $fillable = [
        'nilai', 'tanggal', 'nis', 'id_quesioner', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis')->where('siswa.is_active', true);
    }

    public function quesioner()
    {
        return $this->belongsTo(Quesioner::class, 'id_quesioner', 'id_quesioner')->where('quesioner.is_active', true);
    }

    // Relasi melalui quesioner untuk mendapatkan tahun akademik
    public function thnAkademik()
    {
        return $this->hasOneThrough(
            ThnAkademik::class,
            Quesioner::class,
            'id_quesioner', // Foreign key on Quesioner table
            'id_ta',        // Foreign key on ThnAkademik table
            'id_quesioner', // Local key on NilaiQuesioner table
            'id_ta'         // Local key on Quesioner table
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
