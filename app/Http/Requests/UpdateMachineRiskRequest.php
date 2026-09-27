<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMachineRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $riskId = $this->route('risk')?->id ?? $this->route('risk');

        return [
            'hazard_code' => ['required', 'string', 'max:50', Rule::unique('machine_risk_assessments', 'hazard_code')->ignore($riskId)],
            'hazard_name' => ['required', 'string', 'max:255'],
            'machine_area' => ['required', 'string', 'max:255'],
            'risk_description' => ['required', 'string'],
            'likelihood' => ['required', 'integer', 'min:1', 'max:5'],
            'severity' => ['required', 'integer', 'min:1', 'max:5'],
            'control_measures' => ['nullable', 'string'],
            'pic' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:active,controlled,closed'],
        ];
    }
}
