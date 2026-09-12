<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDefectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $defectTypeId = $this->route('defect_type')?->id ?? $this->route('defect_type');

        return [
            'defect_category_id' => ['required', 'exists:defect_categories,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('defect_types', 'code')->ignore($defectTypeId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['required', 'string', 'in:minor,major,critical'],
            'default_5m_category' => ['required', 'string', 'in:man,machine,method,material,measurement,environment'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
