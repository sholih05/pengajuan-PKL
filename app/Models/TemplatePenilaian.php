<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplatePenilaian extends Model
{
    protected $table = 'template_penilaian';

    protected $fillable = [
        'nama_template',
        'jurusan_id',
        'poin_pembelajaran',
        'tujuan_pembelajaran',
        'deskripsi',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_active'
    ];

    /**
     * Relasi dengan jurusan
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id_jurusan');
    }

    /**
     * Relasi dengan item template (indikator)
     */
    public function items(): HasMany
    {
        return $this->hasMany(TemplatePenilaianItem::class, 'template_id');
    }

    /**
     * Relasi dengan indikator utama saja
     */
    public function mainItems(): HasMany
    {
        return $this->hasMany(TemplatePenilaianItem::class, 'template_id')
            ->where('is_main', true)
            ->orderBy('urutan');
    }

    /**
     * Relasi dengan user yang membuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by' , 'id');
    }

    /**
     * Relasi dengan user yang mengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}