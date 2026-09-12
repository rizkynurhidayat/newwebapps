<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCapaActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'defect_type_id' => ['required', 'exists:defect_types,id'],
            'assigned_to_user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'problem_statement' => ['required', 'string'],
            'root_cause_analysis' => ['required', 'string'],
            'corrective_action' => ['required', 'string'],
            'preventive_action' => ['required', 'string'],
            'target_completion_date' => ['required', 'date'],
            'actual_completion_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:open,in_progress,implemented,verified,closed'],
            'verification_notes' => ['nullable', 'string'],
        ];
    }
}
