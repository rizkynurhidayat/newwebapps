<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualityInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'production_batch_id' => ['required', 'exists:production_batches,id'],
            'inspection_time' => ['required', 'date'],
            'inspection_stage' => ['required', 'string', 'in:incoming,in_process,final_qa'],
            'sample_size_inspected' => ['required', 'integer', 'min:1'],
            'defective_units_qty' => ['required', 'integer', 'min:0', 'lte:sample_size_inspected'],
            'notes' => ['nullable', 'string'],
            'complete_batch' => ['nullable', 'boolean'],
            'defects' => ['nullable', 'array'],
            'defects.*.defect_type_id' => ['required_with:defects', 'exists:defect_types,id'],
            'defects.*.defect_qty' => ['required_with:defects', 'integer', 'min:1'],
            'defects.*.root_cause_category' => ['required_with:defects', 'string', 'in:man,machine,method,material,measurement,environment'],
            'defects.*.root_cause_notes' => ['nullable', 'string'],
        ];
    }
}
