<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'golongan_darah',
        'gender',
        'foto',
        'no_kontak',
        'email',
        'alamat',
        'kelas',
        'id_jurusan',
        'id_user',
        'nama_wali',
        'alamat_wali',
        'no_kontak_wali',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    // Relationships
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan')->where('jurusan.is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id')->where('is_active', true);
    }


    public function penempatan()
    {
        return $this->hasMany(Penempatan::class, 'nis', 'nis')->where('penempatan.is_active', true);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'nis', 'nis')->where('penilaian.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}