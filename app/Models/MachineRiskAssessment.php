<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineRiskAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hazard_code',
        'hazard_name',
        'machine_area',
        'risk_description',
        'likelihood',
        'severity',
        'risk_score',
        'risk_level',
        'control_measures',
        'pic',
        'status',
    ];

    protected $casts = [
        'likelihood' => 'integer',
        'severity' => 'integer',
        'risk_score' => 'integer',
        'risk_level' => RiskLevel::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (MachineRiskAssessment $assessment) {
            // Auto generate hazard code if empty
            if (empty($assessment->hazard_code)) {
                $lastId = static::max('id') ?? 0;
                $assessment->hazard_code = 'BHY-'.str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);
            }

            // Auto calculate risk score = likelihood * severity
            $likelihood = max(1, min(5, (int) $assessment->likelihood));
            $severity = max(1, min(5, (int) $assessment->severity));
            $assessment->likelihood = $likelihood;
            $assessment->severity = $severity;
            $assessment->risk_score = $likelihood * $severity;

            // Auto determine risk level category
            $assessment->risk_level = RiskLevel::fromScore($assessment->risk_score);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
