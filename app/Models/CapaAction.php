<?php

namespace App\Models;

use App\Enums\CapaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapaAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'capa_number',
        'defect_type_id',
        'assigned_to_user_id',
        'title',
        'problem_statement',
        'root_cause_analysis',
        'corrective_action',
        'preventive_action',
        'target_completion_date',
        'actual_completion_date',
        'status',
        'verification_notes',
    ];

    protected $casts = [
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'status' => CapaStatus::class,
    ];

    public function defectType(): BelongsTo
    {
        return $this->belongsTo(DefectType::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
