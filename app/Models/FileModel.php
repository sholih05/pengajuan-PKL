<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    use HasFactory;

    protected $table = 'uploaded_files'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key
    protected $fillable = ['file_name', 'file_path', 'uploaded_at']; // Kolom yang dapat diisi
    public $timestamps = false; // Tidak menggunakan kolom created_at dan updated_at
}
