<?php

namespace App\Http\Controllers;

use App\Enums\InspectionStage;
use App\Http\Requests\StoreQualityInspectionRequest;
use App\Models\DefectType;
use App\Models\ProductionBatch;
use App\Models\QualityInspection;
use App\Services\ProductionService;
use App\Services\SixSigmaCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualityInspectionController extends Controller
{
    public function __construct(
        public ProductionService $productionService,
        public SixSigmaCalculatorService $sixSigmaCalculator
    ) {}

    public function index(Request $request): View
    {
        $query = QualityInspection::query()
            ->with(['productionBatch.product', 'productionBatch.productionLine', 'inspector'])
            ->withCount('inspectionDefects');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inspection_number', 'like', "%{$search}%")
                    ->orWhereHas('productionBatch', fn ($b) => $b->where('batch_number', 'like', "%{$search}%"))
                    ->orWhereHas('productionBatch.product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('stage')) {
            $query->where('inspection_stage', $request->stage);
        }

        if ($request->filled('result')) {
            $query->where('result_status', $request->result);
        }

        $inspections = $query->latest('inspection_time')->paginate(10)->withQueryString();

        return view('admin.inspections.index', compact('inspections'));
    }

    public function create(Request $request): View
    {
        $batches = ProductionBatch::with(['product', 'productionLine'])
            ->whereIn('status', ['in_production', 'draft'])
            ->latest('production_date')
            ->get();

        $selectedBatch = null;
        if ($request->filled('batch_id')) {
            $selectedBatch = ProductionBatch::with('product')->find($request->batch_id);
        }

        $defectTypes = DefectType::active()->with('defectCategory')->orderBy('code')->get();
        $stages = InspectionStage::cases();
        $nextInspectionNumber = $this->productionService->generateInspectionNumber();

        return view('admin.inspections.create', compact(
            'batches',
            'selectedBatch',
            'defectTypes',
            'stages',
            'nextInspectionNumber'
        ));
    }

    public function store(StoreQualityInspectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['inspector_id'] = auth()->id();

        $inspection = $this->productionService->recordInspection($data);

        return redirect()->route('admin.inspections.show', $inspection)
            ->with('success', "Pemeriksaan {$inspection->inspection_number} berhasil dicatat. Tingkat Kualitas: {$inspection->sigma_level} Sigma.");
    }

    public function show(QualityInspection $inspection): View
    {
        $inspection->load([
            'productionBatch.product',
            'productionBatch.productionLine',
            'productionBatch.supervisor',
            'inspector',
            'inspectionDefects.defectType.defectCategory',
        ]);

        return view('admin.inspections.show', compact('inspection'));
    }

    /**
     * Real-time calculation endpoint for the inspection form.
     */
    public function calculatePreview(Request $request): JsonResponse
    {
        $sampleSize = (int) $request->input('sample_size', 100);
        $defectiveUnits = (int) $request->input('defective_units', 0);
        $passedQty = max(0, $sampleSize - $defectiveUnits);
        $totalDefects = (int) $request->input('total_defects', $defectiveUnits);
        $opportunities = (int) $request->input('opportunities_per_unit', 5);

        $metrics = $this->sixSigmaCalculator->calculateMetrics(
            $sampleSize,
            $passedQty,
            $totalDefects,
            $opportunities
        );

        return response()->json($metrics);
    }
}
