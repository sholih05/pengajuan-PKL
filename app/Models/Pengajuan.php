<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surat_pkl'; 

    protected $fillable = [
        'nim',
        'jurusan',
        'perusahaan_tujuan',
        'tanggal_pengajuan',
        'status',
    ];

    // protected $table = 'pengajuan_surat'; 

    // protected $fillable = [
    //     'anggota_1',
    //     'anggota_2',
    //     'anggota_3',
    //     'anggota_4',
    //     'jurusan',
    //     'perusahaan_tujuan',
    //     'tanggal_pengajuan',
    // ];
}

