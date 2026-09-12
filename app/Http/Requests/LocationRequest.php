<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canManageInventory();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $locationId = $this->route('location')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('locations', 'code')->ignore($locationId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'pic_name' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode lokasi/ruangan wajib diisi.',
            'code.unique' => 'Kode lokasi/ruangan ini sudah digunakan.',
            'name.required' => 'Nama lokasi/ruangan wajib diisi.',
        ];
    }
}
