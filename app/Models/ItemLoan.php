<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLoan extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'loan_code',
        'item_id',
        'user_id',
        'processed_by',
        'quantity',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'return_condition',
        'return_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LoanStatus::class,
            'return_condition' => ItemCondition::class,
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * @param  Builder<ItemLoan>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', [LoanStatus::Diajukan, LoanStatus::Disetujui, LoanStatus::Dipinjam]);
    }

    /**
     * @param  Builder<ItemLoan>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', LoanStatus::Diajukan);
    }

    /**
     * @param  Builder<ItemLoan>  $query
     */
    public function scopeReturned(Builder $query): void
    {
        $query->where('status', LoanStatus::Kembali);
    }
}
