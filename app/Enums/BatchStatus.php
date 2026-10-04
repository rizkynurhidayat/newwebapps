<?php

namespace App\Enums;

enum BatchStatus: string
{
    case Draft = 'draft';
    case InProduction = 'in_production';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft / Dijadwalkan',
            self::InProduction => 'Sedang Diproduksi',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-900 border border-slate-300 font-bold dark:bg-slate-800 dark:text-slate-100 dark:border-slate-600',
            self::InProduction => 'bg-blue-100 text-blue-950 border border-blue-300 font-bold dark:bg-blue-950 dark:text-blue-100 dark:border-blue-700',
            self::Completed => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
            self::Cancelled => 'bg-rose-100 text-rose-950 border border-rose-300 font-bold dark:bg-rose-950 dark:text-rose-100 dark:border-rose-700',
        };
    }
}
