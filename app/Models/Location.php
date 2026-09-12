<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'pic_name',
        'description',
    ];

    /**
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @return HasMany<ItemMutation, $this>
     */
    public function outgoingMutations(): HasMany
    {
        return $this->hasMany(ItemMutation::class, 'from_location_id');
    }

    /**
     * @return HasMany<ItemMutation, $this>
     */
    public function incomingMutations(): HasMany
    {
        return $this->hasMany(ItemMutation::class, 'to_location_id');
    }
}
