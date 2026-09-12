<?php

namespace App\Models;

use App\Enums\DefectSeverity;
use App\Enums\IshikawaCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefectType extends Model
{
    use HasFactory;

    protected $fillable = [
        'defect_category_id',
        'code',
        'name',
        'description',
        'severity',
        'default_5m_category',
        'is_active',
    ];

    protected $casts = [
        'severity' => DefectSeverity::class,
        'default_5m_category' => IshikawaCategory::class,
        'is_active' => 'boolean',
    ];

    public function defectCategory(): BelongsTo
    {
        return $this->belongsTo(DefectCategory::class);
    }

    public function inspectionDefects(): HasMany
    {
        return $this->hasMany(InspectionDefect::class);
    }

    public function capaActions(): HasMany
    {
        return $this->hasMany(CapaAction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
