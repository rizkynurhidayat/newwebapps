<?php

namespace App\Http\Controllers;

use App\Enums\BatchStatus;
use App\Enums\CapaStatus;
use App\Models\CapaAction;
use App\Models\InspectionDefect;
use App\Models\ProductionBatch;
use App\Models\QualityInspection;
use App\Services\SixSigmaCalculatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManufacturingDashboardController extends Controller
{
    public function __construct(
        public SixSigmaCalculatorService $sixSigmaCalculator
    ) {}

    public function index(Request $request): View
    {
        // 1. Core KPIs
        $inspections = QualityInspection::with(['productionBatch.product', 'productionBatch.productionLine'])
            ->latest('inspection_time')
            ->get();

        $totalBatches = ProductionBatch::count();
        $inProductionBatches = ProductionBatch::where('status', BatchStatus::InProduction)->count();
        $totalOutput = (int) ProductionBatch::sum('actual_qty');

        $avgSigmaLevel = $inspections->isNotEmpty() ? round($inspections->avg('sigma_level'), 2) : 0.0;
        $avgYield = $inspections->isNotEmpty() ? round($inspections->avg('yield_percentage'), 2) : 0.0;
        $avgDpmo = $inspections->isNotEmpty() ? round($inspections->avg('dpmo'), 0) : 0;
        $openCapaCount = CapaAction::whereIn('status', [CapaStatus::Open, CapaStatus::InProgress])->count();

        // 2. Pareto 80/20 Calculation (from all defects)
        $allDefects = InspectionDefect::with('defectType.defectCategory')->get();
        $paretoData = $this->sixSigmaCalculator->calculatePareto($allDefects);

        // 3. SPC Control Chart (p-chart from past 15 inspections)
        $spcInspections = QualityInspection::with('productionBatch')
            ->orderBy('inspection_time', 'asc')
            ->take(20)
            ->get();
        $spcData = $this->sixSigmaCalculator->calculateSpcControlChart($spcInspections);

        // 4. Ishikawa 5M+1E Summary
        $ishikawaMatrix = $this->sixSigmaCalculator->calculateIshikawaMatrix($allDefects);

        // 5. Recent Inspections
        $recentInspections = QualityInspection::with(['productionBatch.product', 'inspector'])
            ->latest('inspection_time')
            ->take(6)
            ->get();

        // 6. Active / Recent Batches
        $recentBatches = ProductionBatch::with(['product', 'productionLine', 'supervisor'])
            ->latest('production_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBatches',
            'inProductionBatches',
            'totalOutput',
            'avgSigmaLevel',
            'avgYield',
            'avgDpmo',
            'openCapaCount',
            'paretoData',
            'spcData',
            'ishikawaMatrix',
            'recentInspections',
            'recentBatches'
        ));
    }
}
