<?php

namespace App\Services;

use App\Enums\RiskLevel;
use App\Models\MachineRiskAssessment;
use Illuminate\Support\Collection;

class RiskAssessmentService
{
    /**
     * Get aggregate KPI metrics for Risk Assessment Dashboard.
     *
     * @return array{
     *     total_hazards: int,
     *     total_risks: int,
     *     avg_score: float,
     *     distribution: array<string, int>,
     *     highest_risk: ?MachineRiskAssessment
     * }
     */
    public function getDashboardSummary(): array
    {
        $all = MachineRiskAssessment::all();

        $totalHazards = $all->count();
        $totalRisks = $all->count();
        $avgScore = $totalRisks > 0 ? round($all->avg('risk_score'), 1) : 0.0;

        $distribution = [
            'low' => $all->where('risk_level', RiskLevel::Low)->count(),
            'medium' => $all->where('risk_level', RiskLevel::Medium)->count(),
            'high' => $all->where('risk_level', RiskLevel::High)->count(),
            'extreme' => $all->where('risk_level', RiskLevel::Extreme)->count(),
        ];

        $highestRisk = $all->sortByDesc('risk_score')->first();

        return [
            'total_hazards' => $totalHazards,
            'total_risks' => $totalRisks,
            'avg_score' => $avgScore,
            'distribution' => $distribution,
            'highest_risk' => $highestRisk,
        ];
    }

    /**
     * Build interactive 5x5 Risk Assessment Matrix (Likelihood 1-5 vs Severity 1-5).
     *
     * @return array<int, array<int, array{score: int, level: RiskLevel, items: Collection}>>
     */
    public function getRiskMatrix5x5(): array
    {
        $all = MachineRiskAssessment::all();
        $matrix = [];

        for ($l = 5; $l >= 1; $l--) {
            $matrix[$l] = [];
            for ($s = 1; $s <= 5; $s++) {
                $score = $l * $s;
                $level = RiskLevel::fromScore($score);
                $items = $all->filter(fn ($item) => $item->likelihood === $l && $item->severity === $s)->values();

                $matrix[$l][$s] = [
                    'score' => $score,
                    'level' => $level,
                    'items' => $items,
                    'count' => $items->count(),
                ];
            }
        }

        return $matrix;
    }

    /**
     * Generate next sequential hazard code (e.g. BHY-001).
     */
    public function generateHazardCode(): string
    {
        $lastId = MachineRiskAssessment::withTrashed()->max('id') ?? 0;

        return 'BHY-'.str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);
    }
}
