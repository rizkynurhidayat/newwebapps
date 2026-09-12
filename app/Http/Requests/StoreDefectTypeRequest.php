<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDefectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'defect_category_id' => ['required', 'exists:defect_categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:defect_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['required', 'string', 'in:minor,major,critical'],
            'default_5m_category' => ['required', 'string', 'in:man,machine,method,material,measurement,environment'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
