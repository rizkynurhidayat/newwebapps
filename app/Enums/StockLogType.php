<?php

namespace App\Enums;

enum StockLogType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Barang Masuk / Restock',
            self::Out => 'Barang Keluar / Pemakaian',
            self::Adjustment => 'Penyesuaian / Stock Opname',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::In => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Out => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            self::Adjustment => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
        };
    }
}
