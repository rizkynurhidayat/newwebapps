<?php

namespace App\Services;

use App\Enums\BatchStatus;
use App\Enums\InspectionResult;
use App\Models\CapaAction;
use App\Models\ProductionBatch;
use App\Models\QualityInspection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function __construct(
        public SixSigmaCalculatorService $sixSigmaCalculator
    ) {}

    /**
     * Generate next sequential Batch Number (format: LOT-YYYYMM-XXXX).
     */
    public function generateBatchNumber(): string
    {
        $prefix = 'LOT-'.now()->format('Ym').'-';
        $latest = ProductionBatch::where('batch_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('batch_number');

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d{4})$/', $latest, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next sequential Inspection Number (format: QC-YYYYMM-XXXX).
     */
    public function generateInspectionNumber(): string
    {
        $prefix = 'QC-'.now()->format('Ym').'-';
        $latest = QualityInspection::where('inspection_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('inspection_number');

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d{4})$/', $latest, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next sequential CAPA Number (format: CAPA-YYYYMM-XXXX).
     */
    public function generateCapaNumber(): string
    {
        $prefix = 'CAPA-'.now()->format('Ym').'-';
        $latest = CapaAction::where('capa_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('capa_number');

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d{4})$/', $latest, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Atomically record a Quality Inspection with its defect items and calculate Six Sigma metrics.
     *
     * @param  array{
     *     production_batch_id: int,
     *     inspector_id: int,
     *     inspection_time: string|Carbon,
     *     inspection_stage: string,
     *     sample_size_inspected: int,
     *     defective_units_qty: int,
     *     notes?: string|null,
     *     defects?: array<int, array{defect_type_id: int, defect_qty: int, root_cause_category: string, root_cause_notes?: string|null, photo_path?: string|null}>
     * }  $data
     */
    public function recordInspection(array $data): QualityInspection
    {
        return DB::transaction(function () use ($data) {
            $batch = ProductionBatch::with('product')->findOrFail($data['production_batch_id']);
            $product = $batch->product;

            $sampleSize = (int) $data['sample_size_inspected'];
            $defectiveUnits = (int) ($data['defective_units_qty'] ?? 0);
            $passedQty = max(0, $sampleSize - $defectiveUnits);

            // Calculate total defects from itemized defects list
            $defectsList = $data['defects'] ?? [];
            $totalDefectsCount = 0;
            foreach ($defectsList as $defect) {
                $totalDefectsCount += (int) ($defect['defect_qty'] ?? 0);
            }

            // If no defect details were passed but defective units > 0, assume at least 1 defect per defective unit
            if ($totalDefectsCount === 0 && $defectiveUnits > 0) {
                $totalDefectsCount = $defectiveUnits;
            }

            // Calculate Six Sigma metrics
            $opportunities = $product->defect_opportunities_per_unit ?? 5;
            $metrics = $this->sixSigmaCalculator->calculateMetrics(
                $sampleSize,
                $passedQty,
                $totalDefectsCount,
                $opportunities
            );

            // Determine Inspection Result
            $defectRate = $sampleSize > 0 ? ($defectiveUnits / $sampleSize) : 0;
            $resultStatus = match (true) {
                $defectiveUnits === 0 => InspectionResult::Passed,
                $defectRate <= 0.05 => InspectionResult::Conditional,
                default => InspectionResult::Rejected,
            };

            $inspection = QualityInspection::create([
                'inspection_number' => $this->generateInspectionNumber(),
                'production_batch_id' => $batch->id,
                'inspector_id' => $data['inspector_id'],
                'inspection_time' => $data['inspection_time'] ?? now(),
                'inspection_stage' => $data['inspection_stage'] ?? 'in_process',
                'sample_size_inspected' => $sampleSize,
                'passed_qty' => $passedQty,
                'defective_units_qty' => $defectiveUnits,
                'total_defects_count' => $totalDefectsCount,
                'dpu' => $metrics['dpu'],
                'dpo' => $metrics['dpo'],
                'dpmo' => $metrics['dpmo'],
                'sigma_level' => $metrics['sigma_level'],
                'yield_percentage' => $metrics['yield'],
                'result_status' => $resultStatus,
                'notes' => $data['notes'] ?? null,
            ]);

            // Save line-item defects
            foreach ($defectsList as $defect) {
                if (! empty($defect['defect_type_id']) && ! empty($defect['defect_qty'])) {
                    $inspection->inspectionDefects()->create([
                        'defect_type_id' => $defect['defect_type_id'],
                        'defect_qty' => (int) $defect['defect_qty'],
                        'root_cause_category' => $defect['root_cause_category'] ?? 'machine',
                        'root_cause_notes' => $defect['root_cause_notes'] ?? null,
                        'photo_path' => $defect['photo_path'] ?? null,
                    ]);
                }
            }

            // Update batch actual quantity and status if completed
            if (! empty($data['complete_batch'])) {
                $batch->update([
                    'actual_qty' => $passedQty,
                    'status' => BatchStatus::Completed,
                ]);
            }

            return $inspection->load(['inspectionDefects.defectType', 'productionBatch.product']);
        });
    }
}
