<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMachineRiskRequest;
use App\Http\Requests\UpdateMachineRiskRequest;
use App\Models\MachineRiskAssessment;
use App\Services\RiskAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MachineRiskController extends Controller
{
    public function __construct(
        protected RiskAssessmentService $riskService
    ) {}

    public function index(Request $request): View
    {
        $summary = $this->riskService->getDashboardSummary();
        $matrix = $this->riskService->getRiskMatrix5x5();

        $query = MachineRiskAssessment::query();

        if ($request->filled('level')) {
            $query->where('risk_level', $request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hazard_name', 'like', "%{$search}%")
                    ->orWhere('hazard_code', 'like', "%{$search}%")
                    ->orWhere('machine_area', 'like', "%{$search}%")
                    ->orWhere('risk_description', 'like', "%{$search}%");
            });
        }

        $risks = $query->orderByDesc('risk_score')->paginate(10)->withQueryString();

        return view('admin.risks.index', compact('summary', 'matrix', 'risks'));
    }

    public function create(): View
    {
        $generatedCode = $this->riskService->generateHazardCode();

        return view('admin.risks.create', compact('generatedCode'));
    }

    public function store(StoreMachineRiskRequest $request): RedirectResponse
    {
        $risk = MachineRiskAssessment::create($request->validated());

        return redirect()->route('admin.risks.index')
            ->with('success', "Identifikasi bahaya [{$risk->hazard_code}] {$risk->hazard_name} berhasil disimpan.");
    }

    public function edit(MachineRiskAssessment $risk): View
    {
        return view('admin.risks.edit', compact('risk'));
    }

    public function update(UpdateMachineRiskRequest $request, MachineRiskAssessment $risk): RedirectResponse
    {
        $risk->update($request->validated());

        return redirect()->route('admin.risks.index')
            ->with('success', "Data analisa bahaya & risiko [{$risk->hazard_code}] berhasil diperbarui.");
    }

    public function destroy(MachineRiskAssessment $risk): RedirectResponse
    {
        $code = $risk->hazard_code;
        $risk->delete();

        return redirect()->route('admin.risks.index')
            ->with('success', "Analisa risiko [{$code}] berhasil dihapus.");
    }
}
