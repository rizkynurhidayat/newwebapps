<?php

namespace App\Http\Requests;

use App\Enums\StockLogType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StockTransactionRequest extends FormRequest
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
        return [
            'item_id' => ['required', 'exists:items,id'],
            'type' => ['required', new Enum(StockLogType::class)],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'type.required' => 'Jenis transaksi stok wajib dipilih.',
            'quantity.required' => 'Jumlah kuantitas wajib diisi.',
            'quantity.min' => 'Jumlah kuantitas minimal 1.',
            'notes.required' => 'Catatan/keterangan transaksi wajib diisi.',
        ];
    }
}
