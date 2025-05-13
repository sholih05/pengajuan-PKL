<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplatePenilaianItem extends Model
{
    protected $fillable = [
        'template_id',
        'indikator',
        'is_main',
        'parent_id',
        'urutan',
        'is_nilai',
        'deskripsi'
    ];
    
    /**
     * Relasi dengan template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(TemplatePenilaian::class, 'template_id');
    }
    
    /**
     * Relasi dengan indikator utama (parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TemplatePenilaianItem::class, 'parent_id');
    }
    
    /**
     * Relasi dengan sub-indikator (children)
     */
    public function children(): HasMany
    {
        return $this->hasMany(TemplatePenilaianItem::class, 'parent_id')
            ->orderBy('urutan');
    }
}