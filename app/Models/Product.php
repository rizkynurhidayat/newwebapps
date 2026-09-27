<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_number',
        'name',
        'description',
        'raw_material',
        'unit',
        'defect_opportunities_per_unit',
        'standard_cycle_time',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'defect_opportunities_per_unit' => 'integer',
        'standard_cycle_time' => 'decimal:2',
    ];

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }

    public function qualityInspections(): HasManyThrough
    {
        return $this->hasManyThrough(QualityInspection::class, ProductionBatch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
