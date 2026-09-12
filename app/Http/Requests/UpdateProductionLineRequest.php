<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lineId = $this->route('production_line')?->id ?? $this->route('production_line');

        return [
            'line_code' => ['required', 'string', 'max:50', Rule::unique('production_lines', 'line_code')->ignore($lineId)],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:operational,maintenance,inactive'],
        ];
    }
}
