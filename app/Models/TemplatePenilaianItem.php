<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePenilaianItem extends Model
{
    use HasFactory;

    protected $table = 'template_penilaian_items';
    
    protected $fillable = [
        'template_id',
        'indikator',
        'is_main',
        'parent_id',
        'level3_parent_id',
        'level',
        'urutan',
        'is_nilai',
        'is_active'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_nilai' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer'
    ];

    // Constants for levels
    const LEVEL_MAIN = 1;
    const LEVEL_SUB = 2;
    const LEVEL_SUB_SUB = 3;

    /**
     * Relationship dengan template
     */
    public function template()
    {
        return $this->belongsTo(TemplatePenilaian::class, 'template_id');
    }

    /**
     * Relationship dengan parent (untuk level 2)
     */
    public function parent()
    {
        return $this->belongsTo(TemplatePenilaianItem::class, 'parent_id');
    }

    /**
     * Relationship dengan level3 parent (untuk level 3)
     */
    public function level3Parent()
    {
        return $this->belongsTo(TemplatePenilaianItem::class, 'level3_parent_id');
    }

    /**
     * Relationship dengan children (level 2 dari level 1)
     */
    public function children()
    {
        return $this->hasMany(TemplatePenilaianItem::class, 'parent_id')
                    ->where('level', self::LEVEL_SUB)
                    ->orderBy('urutan');
    }

    /**
     * Relationship dengan level3 children (level 3 dari level 2)
     */
    public function level3Children()
    {
        return $this->hasMany(TemplatePenilaianItem::class, 'level3_parent_id')
                    ->where('level', self::LEVEL_SUB_SUB)
                    ->orderBy('urutan');
    }

    /**
     * Scope untuk main indicators
     */
    public function scopeMainIndicators($query)
    {
        return $query->where('level', self::LEVEL_MAIN)
                    ->where('is_active', 1)
                    ->orderBy('urutan');
    }

    /**
     * Scope untuk sub indicators
     */
    public function scopeSubIndicators($query, $parentId)
    {
        return $query->where('level', self::LEVEL_SUB)
                    ->where('parent_id', $parentId)
                    ->where('is_active', 1)
                    ->orderBy('urutan');
    }

    /**
     * Scope untuk sub-sub indicators
     */
    public function scopeSubSubIndicators($query, $level3ParentId)
    {
        return $query->where('level', self::LEVEL_SUB_SUB)
                    ->where('level3_parent_id', $level3ParentId)
                    ->where('is_active', 1)
                    ->orderBy('urutan');
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
     * Check if has level 3 children
     */
    public function hasLevel3Children()
    {
        return $this->level3Children()->exists();
    }

    /**
     * Get all descendants (children and level3 children)
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        
        if ($this->level == self::LEVEL_MAIN) {
            // Get level 2 children
            $level2Children = $this->children()->with('level3Children')->get();
            $descendants = $descendants->merge($level2Children);
            
            // Get level 3 children
            foreach ($level2Children as $level2Child) {
                $descendants = $descendants->merge($level2Child->level3Children);
            }
        } elseif ($this->level == self::LEVEL_SUB) {
            // Get level 3 children
            $descendants = $descendants->merge($this->level3Children);
        }
        
        return $descendants;
    }
}