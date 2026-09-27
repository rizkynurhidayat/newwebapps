<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $userId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', new Enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $targetUser = $this->route('user');
            $currentUserId = $this->user()?->id;

            if ($targetUser && $targetUser->id === $currentUserId) {
                // Admin editing self: cannot change role away from admin or deactivate self
                if ($this->role !== UserRole::Admin->value) {
                    $validator->errors()->add('role', 'Anda tidak dapat mengubah peran akun Anda sendiri dari Administrator.');
                }
                if ($this->has('is_active') && ! $this->boolean('is_active')) {
                    $validator->errors()->add('is_active', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
                }
            }
        });
    }
}
