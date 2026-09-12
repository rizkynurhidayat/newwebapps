<?php

namespace App\Http\Controllers;

use App\Enums\BatchStatus;
use App\Enums\ProductionShift;
use App\Http\Requests\StoreProductionBatchRequest;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Services\ProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionBatchController extends Controller
{
    public function __construct(
        public ProductionService $productionService
    ) {}

    public function index(Request $request): View
    {
        $query = ProductionBatch::query()->with(['product', 'productionLine', 'supervisor'])
            ->withCount('qualityInspections');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%")->orWhere('part_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('line_id')) {
            $query->where('production_line_id', $request->line_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('production_date', $request->date);
        }

        $batches = $query->latest('production_date')->paginate(10)->withQueryString();
        $lines = ProductionLine::orderBy('line_code')->get();

        return view('admin.batches.index', compact('batches', 'lines'));
    }

    public function create(): View
    {
        $products = Product::active()->orderBy('name')->get();
        $lines = ProductionLine::operational()->orderBy('line_code')->get();
        $shifts = ProductionShift::cases();
        $nextBatchNumber = $this->productionService->generateBatchNumber();

        return view('admin.batches.create', compact('products', 'lines', 'shifts', 'nextBatchNumber'));
    }

    public function store(StoreProductionBatchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['batch_number'] = $this->productionService->generateBatchNumber();
        $data['supervisor_id'] = auth()->id();
        $data['status'] = BatchStatus::InProduction;
        $data['actual_qty'] = 0;

        $batch = ProductionBatch::create($data);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', "Batch produksi {$batch->batch_number} berhasil dibuat.");
    }

    public function show(ProductionBatch $batch): View
    {
        $batch->load([
            'product',
            'productionLine',
            'supervisor',
            'qualityInspections.inspector',
            'qualityInspections.inspectionDefects.defectType',
        ]);

        return view('admin.batches.show', compact('batch'));
    }

    public function edit(ProductionBatch $batch): View
    {
        $products = Product::active()->orderBy('name')->get();
        $lines = ProductionLine::orderBy('line_code')->get();
        $shifts = ProductionShift::cases();
        $statuses = BatchStatus::cases();

        return view('admin.batches.edit', compact('batch', 'products', 'lines', 'shifts', 'statuses'));
    }

    public function update(Request $request, ProductionBatch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'production_line_id' => ['required', 'exists:production_lines,id'],
            'production_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:Shift 1,Shift 2,Shift 3'],
            'target_qty' => ['required', 'integer', 'min:1'],
            'actual_qty' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:draft,in_production,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $batch->update($validated);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Data batch produksi berhasil diperbarui.');
    }

    public function destroy(ProductionBatch $batch): RedirectResponse
    {
        if ($batch->qualityInspections()->exists()) {
            return back()->with('error', 'Batch tidak dapat dihapus karena sudah memiliki hasil inspeksi QC.');
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch produksi berhasil dihapus.');
    }
}
