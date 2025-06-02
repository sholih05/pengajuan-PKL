<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDetail extends Model
{
    use HasFactory;

    protected $table = 'pengajuansurat_detail';
    protected $fillable = ['id_surat', 'nim', 'jurusan'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_surat', 'id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nim', 'nis');
    }

    
}
