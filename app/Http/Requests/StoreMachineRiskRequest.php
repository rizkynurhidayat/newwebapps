<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hazard_code' => ['nullable', 'string', 'max:50', 'unique:machine_risk_assessments,hazard_code'],
            'hazard_name' => ['required', 'string', 'max:255'],
            'machine_area' => ['required', 'string', 'max:255'],
            'risk_description' => ['required', 'string'],
            'likelihood' => ['required', 'integer', 'min:1', 'max:5'],
            'severity' => ['required', 'integer', 'min:1', 'max:5'],
            'control_measures' => ['nullable', 'string'],
            'pic' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:active,controlled,closed'],
        ];
    }
}
