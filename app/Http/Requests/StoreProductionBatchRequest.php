<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'production_line_id' => ['required', 'exists:production_lines,id'],
            'production_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:Shift 1,Shift 2,Shift 3'],
            'target_qty' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
