<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionLineRequest;
use App\Http\Requests\UpdateProductionLineRequest;
use App\Models\ProductionLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionLineController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProductionLine::query()->withCount('productionBatches');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('line_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lines = $query->orderBy('line_code')->paginate(10)->withQueryString();

        return view('admin.lines.index', compact('lines'));
    }

    public function create(): View
    {
        return view('admin.lines.create');
    }

    public function store(StoreProductionLineRequest $request): RedirectResponse
    {
        ProductionLine::create($request->validated());

        return redirect()->route('admin.lines.index')
            ->with('success', 'Lini produksi berhasil ditambahkan.');
    }

    public function edit(ProductionLine $line): View
    {
        return view('admin.lines.edit', compact('line'));
    }

    public function update(UpdateProductionLineRequest $request, ProductionLine $line): RedirectResponse
    {
        $line->update($request->validated());

        return redirect()->route('admin.lines.index')
            ->with('success', 'Lini produksi berhasil diperbarui.');
    }

    public function destroy(ProductionLine $line): RedirectResponse
    {
        if ($line->productionBatches()->exists()) {
            return back()->with('error', 'Lini produksi tidak dapat dihapus karena sudah memiliki riwayat batch produksi.');
        }

        $line->delete();

        return redirect()->route('admin.lines.index')
            ->with('success', 'Lini produksi berhasil dihapus.');
    }
}
