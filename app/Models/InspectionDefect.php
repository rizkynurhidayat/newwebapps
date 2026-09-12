<?php

namespace App\Models;

use App\Enums\IshikawaCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionDefect extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_inspection_id',
        'defect_type_id',
        'defect_qty',
        'root_cause_category',
        'root_cause_notes',
        'photo_path',
    ];

    protected $casts = [
        'defect_qty' => 'integer',
        'root_cause_category' => IshikawaCategory::class,
    ];

    public function qualityInspection(): BelongsTo
    {
        return $this->belongsTo(QualityInspection::class);
    }

    public function defectType(): BelongsTo
    {
        return $this->belongsTo(DefectType::class);
    }
}
