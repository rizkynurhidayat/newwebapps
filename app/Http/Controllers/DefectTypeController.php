<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDefectTypeRequest;
use App\Http\Requests\UpdateDefectTypeRequest;
use App\Models\DefectCategory;
use App\Models\DefectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DefectTypeController extends Controller
{
    public function index(Request $request): View
    {
        $query = DefectType::query()->with(['defectCategory'])->withCount('inspectionDefects');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('defect_category_id', $request->category_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $defectTypes = $query->orderBy('code')->paginate(15)->withQueryString();
        $categories = DefectCategory::orderBy('name')->get();

        return view('admin.defects.index', compact('defectTypes', 'categories'));
    }

    public function create(): View
    {
        $categories = DefectCategory::orderBy('name')->get();

        return view('admin.defects.create', compact('categories'));
    }

    public function store(StoreDefectTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        DefectType::create($data);

        return redirect()->route('admin.defects.index')
            ->with('success', 'Jenis cacat berhasil didaftarkan.');
    }

    public function edit(DefectType $defect): View
    {
        $categories = DefectCategory::orderBy('name')->get();

        return view('admin.defects.edit', compact('defect', 'categories'));
    }

    public function update(UpdateDefectTypeRequest $request, DefectType $defect): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $defect->update($data);

        return redirect()->route('admin.defects.index')
            ->with('success', 'Data jenis cacat berhasil diperbarui.');
    }

    public function destroy(DefectType $defect): RedirectResponse
    {
        if ($defect->inspectionDefects()->exists()) {
            return back()->with('error', 'Jenis cacat tidak dapat dihapus karena sudah memiliki data temuan inspeksi.');
        }

        $defect->delete();

        return redirect()->route('admin.defects.index')
            ->with('success', 'Jenis cacat berhasil dihapus.');
    }
}
