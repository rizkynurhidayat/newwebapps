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
        'inspection_year',
        'inspection_month',
        'inspection_week',
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
        'inspection_year' => 'integer',
        'inspection_month' => 'integer',
        'inspection_week' => 'integer',
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

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $month = $this->inspection_month ?? $this->inspection_time?->month;
        $week = $this->inspection_week ?? ($this->inspection_time ? min(4, (int) ceil($this->inspection_time->day / 7)) : 1);
        $year = $this->inspection_year ?? ($this->inspection_time ? $this->inspection_time->year : now()->year);
        $monthName = $months[$month] ?? '';

        return "Bulan {$monthName} {$year} (Minggu ke-{$week})";
    }

    public function getPeriodShortLabelAttribute(): string
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $month = $this->inspection_month ?? $this->inspection_time?->month;
        $week = $this->inspection_week ?? ($this->inspection_time ? min(4, (int) ceil($this->inspection_time->day / 7)) : 1);
        $year = $this->inspection_year ?? ($this->inspection_time ? $this->inspection_time->year : now()->year);
        $monthName = $months[$month] ?? '';

        return "{$monthName} {$year} (M{$week})";
    }

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
