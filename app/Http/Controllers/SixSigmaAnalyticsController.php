<?php

namespace App\Http\Controllers;

use App\Models\InspectionDefect;
use App\Models\Product;
use App\Models\ProductionLine;
use App\Models\QualityInspection;
use App\Services\SixSigmaCalculatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SixSigmaAnalyticsController extends Controller
{
    public function __construct(
        public SixSigmaCalculatorService $sixSigmaCalculator
    ) {}

    public function index(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $lines = ProductionLine::orderBy('line_code')->get();

        // Base Query for Inspections
        $inspectionsQuery = QualityInspection::query()
            ->with(['productionBatch.product', 'productionBatch.productionLine']);

        if ($request->filled('product_id')) {
            $inspectionsQuery->whereHas('productionBatch', fn ($b) => $b->where('product_id', $request->product_id));
        }

        if ($request->filled('line_id')) {
            $inspectionsQuery->whereHas('productionBatch', fn ($b) => $b->where('production_line_id', $request->line_id));
        }

        if ($request->filled('start_date')) {
            $inspectionsQuery->whereDate('inspection_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $inspectionsQuery->whereDate('inspection_time', '<=', $request->end_date);
        }

        $inspections = $inspectionsQuery->orderBy('inspection_time', 'asc')->get();
        $inspectionIds = $inspections->pluck('id');

        // Defects Query
        $defects = InspectionDefect::with(['defectType.defectCategory'])
            ->whereIn('quality_inspection_id', $inspectionIds)
            ->get();

        // 1. Pareto 80/20 Analysis
        $pareto = $this->sixSigmaCalculator->calculatePareto($defects);

        // 2. SPC Control Chart (p-Chart)
        $spc = $this->sixSigmaCalculator->calculateSpcControlChart($inspections);

        // 3. Ishikawa (5M+1E)
        $ishikawa = $this->sixSigmaCalculator->calculateIshikawaMatrix($defects);

        // 4. Overall Sigma Performance
        $totalInspected = $inspections->sum('sample_size_inspected');
        $totalPassed = $inspections->sum('passed_qty');
        $totalDefectiveUnits = $inspections->sum('defective_units_qty');
        $totalDefects = $defects->sum('defect_qty');
        $avgYield = $totalInspected > 0 ? round(($totalPassed / $totalInspected) * 100, 2) : 0.0;
        $avgSigma = $inspections->isNotEmpty() ? round($inspections->avg('sigma_level'), 2) : 0.0;
        $avgDpmo = $inspections->isNotEmpty() ? round($inspections->avg('dpmo'), 0) : 0;

        return view('admin.analytics.index', compact(
            'products',
            'lines',
            'pareto',
            'spc',
            'ishikawa',
            'inspections',
            'totalInspected',
            'totalPassed',
            'totalDefectiveUnits',
            'totalDefects',
            'avgYield',
            'avgSigma',
            'avgDpmo'
        ));
    }
}
