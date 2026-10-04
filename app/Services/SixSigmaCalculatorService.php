<?php

namespace App\Services;

use App\Enums\IshikawaCategory;
use Illuminate\Support\Collection;

class SixSigmaCalculatorService
{
    /**
     * Calculate core Six Sigma metrics: DPU, DPO, DPMO, Process Yield %, and Sigma Level (with standard 1.5 sigma shift).
     *
     * @return array{dpu: float, dpo: float, dpmo: float, yield: float, sigma_level: float}
     */
    public function calculateMetrics(int $sampleSize, int $passedQty, int $defectsCount, int $opportunitiesPerUnit): array
    {
        $n = max(1, $sampleSize);
        $d = max(0, $defectsCount);
        $o = max(1, $opportunitiesPerUnit);
        $passed = max(0, min($sampleSize, $passedQty));

        $dpu = round($d / $n, 4);
        $totalOpportunities = $n * $o;
        $dpo = round($d / $totalOpportunities, 6);
        $dpmo = round($dpo * 1_000_000, 2);
        $yield = round(($passed / $n) * 100, 2);

        $sigmaLevel = $this->calculateSigmaLevelFromDpo($dpo);

        return [
            'dpu' => $dpu,
            'dpo' => $dpo,
            'dpmo' => $dpmo,
            'yield' => $yield,
            'sigma_level' => $sigmaLevel,
        ];
    }

    /**
     * Compute Sigma Level from DPO using standard inverse normal distribution with 1.5 sigma shift.
     */
    public function calculateSigmaLevelFromDpo(float $dpo): float
    {
        if ($dpo <= 0.0) {
            return 6.00;
        }

        if ($dpo >= 0.999999) {
            return 0.00;
        }

        $probabilityDefectFree = 1.0 - $dpo;
        $z = $this->inverseNormalCdf($probabilityDefectFree);
        $sigmaWithShift = $z + 1.50;

        return round(max(0.00, min(6.00, $sigmaWithShift)), 2);
    }

    /**
     * High-accuracy rational approximation for Inverse Normal Cumulative Distribution Function (NormSInv).
     * Based on Peter J. Acklam's algorithm.
     */
    public function inverseNormalCdf(float $p): float
    {
        if ($p <= 0.0) {
            return -6.0;
        }
        if ($p >= 1.0) {
            return 6.0;
        }

        // Coefficients in rational approximations
        $a = [
            -3.969683028665376e+01,
            2.209460984245205e+02,
            -2.759285104469687e+02,
            1.383577518672690e+02,
            -3.066479806614716e+01,
            2.506628277459239e+00,
        ];

        $b = [
            -5.447609879822406e+01,
            1.615858368580409e+02,
            -1.556989798598866e+02,
            6.680131188771972e+01,
            -1.328068155288572e+01,
        ];

        $c = [
            -7.784894002430293e-03,
            -3.223964580411365e-01,
            -2.400758277161838e+00,
            -2.549732539343734e+00,
            4.374664141464968e+00,
            2.938163982698783e+00,
        ];

        $d = [
            7.784695709041462e-03,
            3.224671290700398e-01,
            2.445134137142996e+00,
            3.754408661907416e+00,
        ];

        $pLow = 0.02425;
        $pHigh = 1.0 - $pLow;

        if ($p < $pLow) {
            // Rational approximation for lower region
            $q = sqrt(-2.0 * log($p));

            return (((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5] /
                (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1.0);
        }

        if ($p <= $pHigh) {
            // Rational approximation for central region
            $q = $p - 0.5;
            $r = $q * $q;

            return ((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $q /
                ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + 1.0);
        }

        // Rational approximation for upper region
        $q = sqrt(-2.0 * log(1.0 - $p));

        return -((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) /
            (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1.0);
    }

    /**
     * Compute Pareto 80/20 distribution from a collection of defect items.
     *
     * @return array{items: array, total_defects: int, vital_few_count: int}
     */
    public function calculatePareto(Collection $defects): array
    {
        $grouped = $defects->groupBy('defect_type_id')->map(function ($group) {
            $first = $group->first();
            $defectType = $first->defectType ?? null;

            return [
                'defect_type_id' => $first->defect_type_id,
                'defect_code' => $defectType?->code ?? 'DEF-'.str_pad((string) $first->defect_type_id, 3, '0', STR_PAD_LEFT),
                'defect_name' => $defectType?->name ?? 'Unknown Defect',
                'severity' => $defectType?->severity?->value ?? 'minor',
                'count' => $group->sum('defect_qty'),
            ];
        })->sortByDesc('count')->values();

        $totalDefects = $grouped->sum('count');

        $cumulative = 0;
        $items = [];
        $vitalFewCount = 0;

        foreach ($grouped as $item) {
            $count = $item['count'];
            $percentage = $totalDefects > 0 ? round(($count / $totalDefects) * 100, 1) : 0;
            $cumulative += $percentage;
            $isVitalFew = $cumulative <= 80.0 || (count($items) === 0 && $totalDefects > 0);

            if ($isVitalFew) {
                $vitalFewCount++;
            }

            $items[] = array_merge($item, [
                'percentage' => $percentage,
                'cumulative_percentage' => min(100.0, round($cumulative, 1)),
                'is_vital_few' => $isVitalFew,
            ]);
        }

        return [
            'items' => $items,
            'total_defects' => $totalDefects,
            'vital_few_count' => $vitalFewCount,
        ];
    }

    /**
     * Compute Statistical Process Control (p-Chart) for defect proportion over chronological batches.
     *
     * @return array{ucl: float, cl: float, lcl: float, points: array}
     */
    public function calculateSpcControlChart(Collection $inspections): array
    {
        if ($inspections->isEmpty()) {
            return [
                'ucl' => 0.0,
                'cl' => 0.0,
                'lcl' => 0.0,
                'points' => [],
            ];
        }

        $totalInspected = $inspections->sum('sample_size_inspected');
        $totalDefectiveUnits = $inspections->sum('defective_units_qty');
        $k = $inspections->count();

        // Average fraction nonconforming (p_bar)
        $pBar = $totalInspected > 0 ? ($totalDefectiveUnits / $totalInspected) : 0.0;
        $nBar = $k > 0 ? ($totalInspected / $k) : 1.0;

        $sigmaP = ($nBar > 0 && $pBar > 0) ? sqrt(($pBar * (1.0 - $pBar)) / $nBar) : 0.0;

        $ucl = round(min(1.0, $pBar + (3.0 * $sigmaP)), 4);
        $cl = round($pBar, 4);
        $lcl = round(max(0.0, $pBar - (3.0 * $sigmaP)), 4);

        $points = $inspections->map(function ($inspection) use ($ucl, $lcl) {
            $sampleSize = max(1, $inspection->sample_size_inspected);
            $p = round($inspection->defective_units_qty / $sampleSize, 4);
            $isOutOfControl = $p > $ucl || $p < $lcl;

            return [
                'id' => $inspection->id,
                'label' => $inspection->inspection_number,
                'batch_number' => $inspection->productionBatch?->batch_number ?? '-',
                'date' => $inspection->inspection_time?->format('d/m H:i') ?? '-',
                'sample_size' => $sampleSize,
                'defective_qty' => $inspection->defective_units_qty,
                'p' => $p,
                'out_of_control' => $isOutOfControl,
            ];
        })->values()->toArray();

        return [
            'ucl' => $ucl,
            'cl' => $cl,
            'lcl' => $lcl,
            'points' => $points,
        ];
    }

    /**
     * Compute Ishikawa (Fishbone 5M+1E) defect frequencies.
     *
     * @return array<string, array{label: string, count: int, percentage: float, items: array}>
     */
    public function calculateIshikawaMatrix(Collection $inspectionDefects): array
    {
        $categories = IshikawaCategory::cases();
        $total = $inspectionDefects->sum('defect_qty');
        $matrix = [];

        foreach ($categories as $category) {
            $filtered = $inspectionDefects->filter(fn ($item) => $item->root_cause_category === $category);
            $count = $filtered->sum('defect_qty');
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0.0;

            $matrix[$category->value] = [
                'label' => $category->label(),
                'icon' => $category->icon(),
                'badge_color' => $category->badgeColor(),
                'count' => $count,
                'percentage' => $percentage,
                'sample_notes' => $filtered->pluck('root_cause_notes')->filter()->unique()->take(4)->values()->toArray(),
            ];
        }

        return $matrix;
    }

    /**
     * Generate automated Six Sigma conclusions per Flowchart Step 9 & 10 (Sigma Level, Dominant Defect, Control Status, Action).
     *
     * @param  array{items: array, total_defects: int, vital_few_count: int}  $pareto
     * @param  array{ucl: float, cl: float, lcl: float, points: array}  $spc
     * @return array{
     *     sigma_eval: array{level: float, category: string, badge_color: string, description: string},
     *     dominant_defect: ?array{code: string, name: string, percentage: float, count: int, severity: string},
     *     control_status: array{is_in_control: bool, label: string, badge_color: string, out_of_control_count: int, note: string},
     *     recommendations: array<string>
     * }
     */
    public function generateConclusion(float $avgSigma, array $pareto, array $spc): array
    {
        // 1. Sigma Level Evaluation
        $sigmaEval = match (true) {
            $avgSigma >= 6.0 => [
                'level' => $avgSigma,
                'category' => 'Kelas Dunia (World Class)',
                'badge_color' => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold',
                'description' => 'Proses sangat presisi dengan probabilitas cacat sangat minim (<= 3.4 DPMO).',
            ],
            $avgSigma >= 4.0 => [
                'level' => $avgSigma,
                'category' => 'Standar Industri Baik (Competitive)',
                'badge_color' => 'bg-blue-100 text-blue-950 border border-blue-300 font-bold',
                'description' => 'Kualitas memenuhi standar industri komponen otomotif. Pertahankan kestabilan.',
            ],
            $avgSigma >= 3.0 => [
                'level' => $avgSigma,
                'category' => 'Cukup / Perlu Pengendalian (Acceptable)',
                'badge_color' => 'bg-amber-100 text-amber-950 border border-amber-300 font-bold',
                'description' => 'Tingkat cacat masih berada dalam batas toleransi namun rentan terhadap fluktuasi proses.',
            ],
            default => [
                'level' => $avgSigma,
                'category' => 'Kritis / Butuh Perbaikan Segera (Critical)',
                'badge_color' => 'bg-rose-100 text-rose-950 border border-rose-300 font-bold',
                'description' => 'Tingkat kegagalan tinggi (< 3.0 Sigma). Tindakan korektif darurat wajib diprioritaskan.',
            ],
        };

        // 2. Dominant Defect from Pareto Vital Few
        $topItem = $pareto['items'][0] ?? null;
        $dominantDefect = $topItem ? [
            'code' => $topItem['defect_code'],
            'name' => $topItem['defect_name'],
            'percentage' => $topItem['percentage'],
            'count' => $topItem['count'],
            'severity' => $topItem['severity'],
        ] : null;

        // 3. SPC Process Control Status
        $outOfControlPoints = collect($spc['points'] ?? [])->filter(fn ($p) => ! empty($p['out_of_control']));
        $outCount = $outOfControlPoints->count();
        $isInControl = $outCount === 0;

        $controlStatus = [
            'is_in_control' => $isInControl,
            'label' => $isInControl ? 'Terkendali (In Control)' : 'Di Luar Kendali (Out of Control)',
            'badge_color' => $isInControl ? 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold' : 'bg-rose-100 text-rose-950 border border-rose-300 font-bold',
            'out_of_control_count' => $outCount,
            'note' => $isInControl
                ? 'Seluruh variasi proporsi cacat per batch berada di dalam batas kendali Upper Control Limit (UCL) dan Lower Control Limit (LCL).'
                : "Terdeteksi {$outCount} lot produksi dengan proporsi cacat melampaui batas kendali (UCL/LCL). Wajib dilakukan Tindakan Korektif (CAPA).",
        ];

        // 4. Actionable Recommendations
        $recommendations = [];
        if ($dominantDefect) {
            $recommendations[] = "Prioritaskan tindakan perbaikan pada cacat dominan '{$dominantDefect['name']}' yang menyumbang {$dominantDefect['percentage']}% dari seluruh kegagalan mutu.";
        }
        if (! $isInControl) {
            $recommendations[] = "Segera buat tiket CAPA untuk {$outCount} lot produksi yang statusnya Out of Control untuk menginvestigasi akar masalah 5M+1E pada mesin stamping press.";
        } else {
            $recommendations[] = 'Pertahankan konsistensi parameter operasional mesin stamping press dan lakukan audit CTQ berkala.';
        }

        return [
            'sigma_eval' => $sigmaEval,
            'dominant_defect' => $dominantDefect,
            'control_status' => $controlStatus,
            'recommendations' => $recommendations,
        ];
    }
}
