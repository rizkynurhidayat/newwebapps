<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_code',
        'name',
        'location',
        'description',
        'status',
    ];

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }

    public function scopeOperational($query)
    {
        return $query->where('status', 'operational');
    }
}
