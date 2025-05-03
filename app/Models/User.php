<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'username', 'password', 'role', 'created_at', 'updated_at', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    protected $hidden = [
        'password',
    ];

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id_user', 'id')->where('siswa.is_active', true);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_user', 'id')->where('guru.is_active', true);
    }

    public function instruktur()
    {
        return $this->hasOne(Instruktur::class, 'id_user', 'id')->where('instruktur.is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
