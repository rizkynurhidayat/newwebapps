<?php

namespace App\Http\Controllers;

use App\Enums\CapaStatus;
use App\Http\Requests\StoreCapaActionRequest;
use App\Http\Requests\UpdateCapaActionRequest;
use App\Models\CapaAction;
use App\Models\DefectType;
use App\Models\User;
use App\Services\ProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CapaController extends Controller
{
    public function __construct(
        public ProductionService $productionService
    ) {}

    public function index(Request $request): View
    {
        $query = CapaAction::query()->with(['defectType', 'assignedTo']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('capa_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('defectType', fn ($d) => $d->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $capaActions = $query->latest('target_completion_date')->paginate(10)->withQueryString();
        $statuses = CapaStatus::cases();

        return view('admin.capa.index', compact('capaActions', 'statuses'));
    }

    public function create(Request $request): View
    {
        $defectTypes = DefectType::active()->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $nextCapaNumber = $this->productionService->generateCapaNumber();

        $selectedDefectId = $request->defect_type_id;

        return view('admin.capa.create', compact('defectTypes', 'users', 'nextCapaNumber', 'selectedDefectId'));
    }

    public function store(StoreCapaActionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['capa_number'] = $this->productionService->generateCapaNumber();
        $data['status'] = $data['status'] ?? CapaStatus::Open->value;

        $capa = CapaAction::create($data);

        return redirect()->route('admin.capa.show', $capa)
            ->with('success', "Tindakan perbaikan {$capa->capa_number} berhasil didaftarkan.");
    }

    public function show(CapaAction $capa): View
    {
        $capa->load(['defectType.defectCategory', 'assignedTo']);

        return view('admin.capa.show', compact('capa'));
    }

    public function edit(CapaAction $capa): View
    {
        $defectTypes = DefectType::active()->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $statuses = CapaStatus::cases();

        return view('admin.capa.edit', compact('capa', 'defectTypes', 'users', 'statuses'));
    }

    public function update(UpdateCapaActionRequest $request, CapaAction $capa): RedirectResponse
    {
        $capa->update($request->validated());

        return redirect()->route('admin.capa.show', $capa)
            ->with('success', 'Data tindakan perbaikan berhasil diperbarui.');
    }

    public function destroy(CapaAction $capa): RedirectResponse
    {
        $capa->delete();

        return redirect()->route('admin.capa.index')
            ->with('success', 'Tindakan perbaikan berhasil dihapus.');
    }
}
