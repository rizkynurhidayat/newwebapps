<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'is_active',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Staff;
    }

    public function isEmployee(): bool
    {
        return $this->role === UserRole::Employee;
    }

    public function canManageInventory(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    /**
     * @return HasMany<ItemLoan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(ItemLoan::class, 'user_id');
    }

    /**
     * @return HasMany<ItemLoan, $this>
     */
    public function processedLoans(): HasMany
    {
        return $this->hasMany(ItemLoan::class, 'processed_by');
    }

    /**
     * @return HasMany<ItemMutation, $this>
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(ItemMutation::class, 'moved_by');
    }

    /**
     * @return HasMany<ItemStockLog, $this>
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(ItemStockLog::class, 'created_by');
    }
}
