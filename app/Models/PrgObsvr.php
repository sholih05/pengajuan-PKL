<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrgObsvr extends Model
{
    use HasFactory;
    protected $table = 'prg_obsvr';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    protected $fillable = [
        'indikator', 'is_nilai', 'id_ta', 'id_guru', 'id_jurusan', 'id1','id2',
        'level', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    const LEVEL_MAIN = 1;
    const LEVEL_SUB = 2;
    const LEVEL_SUB_SUB = 3;


    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru')->where('guru.is_active', true);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan')->where('jurusan.is_active', true);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(ThnAkademik::class, 'id_ta', 'id_ta')->where('thn_akademik.is_active', true);
    }

    /**
     * Relationship dengan parent level 1 (untuk level 2)
     */
    public function parent()
    {
        return $this->belongsTo(PrgObsvr::class, 'id1');
    }

    /**
     * Relationship dengan parent level 2 (untuk level 3)
     */
    public function level2Parent()
    {
        return $this->belongsTo(PrgObsvr::class, 'id2');
    }

    /**
     * Relationship dengan children level 2 (dari level 1)
     */
    public function children()
    {
        return $this->hasMany(PrgObsvr::class, 'id1')
                    ->where('level', self::LEVEL_SUB)
                    ->where('is_active', 1)
                    ->orderBy('id');
    }

    /**
     * Relationship dengan children level 3 (dari level 2)
     */
    public function level3Children()
    {
        return $this->hasMany(PrgObsvr::class, 'id2')
                    ->where('level', self::LEVEL_SUB_SUB)
                    ->where('is_active', 1)
                    ->orderBy('id');
    }

    /**
     * Scope untuk main indicators
     */
    public function scopeMainIndicators($query)
    {
        return $query->where('level', self::LEVEL_MAIN)
                    ->where('is_active', 1)
                    ->orderBy('id');
    }

    /**
     * Scope untuk sub indicators
     */
    public function scopeSubIndicators($query, $parentId)
    {
        return $query->where('level', self::LEVEL_SUB)
                    ->where('id1', $parentId)
                    ->where('is_active', 1)
                    ->orderBy('id');
    }

    /**
     * Scope untuk sub-sub indicators
     */
    public function scopeSubSubIndicators($query, $level2ParentId)
    {
        return $query->where('level', self::LEVEL_SUB_SUB)
                    ->where('id2', $level2ParentId)
                    ->where('is_active', 1)
                    ->orderBy('id');
    }

    /**
     * Get level name
     */
    public function getLevelNameAttribute()
    {
        switch ($this->level) {
            case self::LEVEL_MAIN:
                return 'Main Indicator';
            case self::LEVEL_SUB:
                return 'Sub Indicator';
            case self::LEVEL_SUB_SUB:
                return 'Sub-Sub Indicator';
            default:
                return 'Unknown';
        }
    }

    /**
     * Check if this indicator should be assessed directly
     */
    public function shouldBeAssessed()
    {
        // Level 3 selalu dinilai
        if ($this->level == self::LEVEL_SUB_SUB) {
            return true;
        }
        
        // Level 2 dinilai jika tidak ada level 3
        if ($this->level == self::LEVEL_SUB) {
            return !$this->level3Children()->exists();
        }
        
        // Level 1 tidak pernah dinilai langsung
        return false;
    }
}