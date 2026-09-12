<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ItemLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        // Jika staf/admin, mereka bisa memilih user peminjam. Jika pegawai biasa, otomatis diri sendiri.
        if (auth()->user()?->canManageInventory()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang yang ingin dipinjam wajib dipilih.',
            'due_date.after_or_equal' => 'Tanggal estimasi kembali harus sama dengan atau setelah tanggal pinjam.',
            'quantity.min' => 'Jumlah barang minimal 1.',
        ];
    }
}
