<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'part_number' => ['required', 'string', 'max:50', Rule::unique('products', 'part_number')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'raw_material' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'defect_opportunities_per_unit' => ['required', 'integer', 'min:1', 'max:100'],
            'standard_cycle_time' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
