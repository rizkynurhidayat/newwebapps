<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualityInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('inspection_month') && $this->filled('inspection_week')) {
            $year = (int) ($this->input('inspection_year') ?: now()->year);
            $month = (int) $this->input('inspection_month');
            $week = (int) $this->input('inspection_week');
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            $day = min($daysInMonth, ($week - 1) * 7 + 1);

            $this->merge([
                'inspection_year' => $year,
                'inspection_time' => Carbon::create($year, $month, $day, 9, 0, 0)->toDateTimeString(),
            ]);
        } elseif ($this->filled('inspection_time')) {
            $dt = Carbon::parse($this->input('inspection_time'));
            $this->merge([
                'inspection_year' => $this->input('inspection_year') ?: $dt->year,
                'inspection_month' => $this->input('inspection_month') ?: $dt->month,
                'inspection_week' => $this->input('inspection_week') ?: min(4, (int) ceil($dt->day / 7)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'production_batch_id' => ['required', 'exists:production_batches,id'],
            'inspection_year' => ['nullable', 'integer', 'between:2020,2099'],
            'inspection_month' => ['required_without:inspection_time', 'nullable', 'integer', 'between:1,12'],
            'inspection_week' => ['required_without:inspection_time', 'nullable', 'integer', 'between:1,4'],
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

    public function attributes(): array
    {
        return [
            'inspection_year' => 'tahun pemeriksaan',
            'inspection_month' => 'bulan pemeriksaan',
            'inspection_week' => 'minggu pemeriksaan',
        ];
    }
}
