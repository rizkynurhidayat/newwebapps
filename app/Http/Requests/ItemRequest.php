<?php

namespace App\Http\Requests;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ItemRequest extends FormRequest
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
        $itemId = $this->route('item')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')->ignore($itemId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'barcode')->ignore($itemId),
            ],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'unit' => ['required', 'string', 'max:30'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'is_consumable' => ['boolean'],
            'condition' => ['required', new Enum(ItemCondition::class)],
            'status' => ['required', new Enum(ItemStatus::class)],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode barang wajib diisi.',
            'code.unique' => 'Kode barang ini sudah terdaftar di sistem.',
            'name.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'location_id.required' => 'Lokasi/Ruangan penyimpanan barang wajib dipilih.',
            'unit.required' => 'Satuan barang wajib diisi.',
            'stock.required' => 'Jumlah stok barang wajib diisi.',
            'image.max' => 'Ukuran foto maksimal adalah 2MB.',
        ];
    }
}
