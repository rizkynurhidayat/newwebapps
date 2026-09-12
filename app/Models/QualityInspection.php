<?php

namespace App\Models;

use App\Enums\InspectionResult;
use App\Enums\InspectionStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inspection_number',
        'production_batch_id',
        'inspector_id',
        'inspection_time',
        'inspection_stage',
        'sample_size_inspected',
        'passed_qty',
        'defective_units_qty',
        'total_defects_count',
        'dpu',
        'dpo',
        'dpmo',
        'sigma_level',
        'yield_percentage',
        'result_status',
        'notes',
    ];

    protected $casts = [
        'inspection_time' => 'datetime',
        'inspection_stage' => InspectionStage::class,
        'result_status' => InspectionResult::class,
        'sample_size_inspected' => 'integer',
        'passed_qty' => 'integer',
        'defective_units_qty' => 'integer',
        'total_defects_count' => 'integer',
        'dpu' => 'decimal:4',
        'dpo' => 'decimal:6',
        'dpmo' => 'decimal:2',
        'sigma_level' => 'decimal:2',
        'yield_percentage' => 'decimal:2',
    ];

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function inspectionDefects(): HasMany
    {
        return $this->hasMany(InspectionDefect::class);
    }
}
