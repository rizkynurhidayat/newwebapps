<?php

namespace App\Models;

use App\Enums\BatchStatus;
use App\Enums\ProductionShift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_number',
        'product_id',
        'production_line_id',
        'supervisor_id',
        'production_date',
        'shift',
        'target_qty',
        'actual_qty',
        'status',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
        'shift' => ProductionShift::class,
        'status' => BatchStatus::class,
        'target_qty' => 'integer',
        'actual_qty' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(ProductionLine::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function qualityInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function latestInspection()
    {
        return $this->hasOne(QualityInspection::class)->latestOfMany();
    }
}
