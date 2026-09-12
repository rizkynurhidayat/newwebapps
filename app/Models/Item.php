<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'barcode',
        'name',
        'description',
        'category_id',
        'location_id',
        'vendor_id',
        'unit',
        'stock',
        'min_stock',
        'is_consumable',
        'condition',
        'status',
        'purchase_price',
        'purchase_date',
        'image_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => ItemCondition::class,
            'status' => ItemStatus::class,
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'is_consumable' => 'boolean',
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return HasMany<ItemLoan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(ItemLoan::class);
    }

    /**
     * @return HasMany<ItemMutation, $this>
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(ItemMutation::class);
    }

    /**
     * @return HasMany<ItemStockLog, $this>
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(ItemStockLog::class);
    }

    /**
     * @param  Builder<Item>  $query
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', ItemStatus::Tersedia);
    }

    /**
     * @param  Builder<Item>  $query
     */
    public function scopeBorrowed(Builder $query): void
    {
        $query->where('status', ItemStatus::Dipinjam);
    }

    /**
     * @param  Builder<Item>  $query
     */
    public function scopeDamaged(Builder $query): void
    {
        $query->whereIn('condition', [ItemCondition::RusakRingan, ItemCondition::RusakBerat]);
    }

    /**
     * @param  Builder<Item>  $query
     */
    public function scopeLowStock(Builder $query): void
    {
        $query->where('is_consumable', true)
            ->whereColumn('stock', '<=', 'min_stock');
    }

    public function isAvailable(): bool
    {
        return $this->status === ItemStatus::Tersedia && $this->stock > 0;
    }

    public function formattedPrice(): string
    {
        if ($this->purchase_price === null) {
            return '-';
        }

        return 'Rp '.number_format((float) $this->purchase_price, 0, ',', '.');
    }
}
