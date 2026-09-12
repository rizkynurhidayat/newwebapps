<?php

namespace App\Enums;

enum ItemCondition: string
{
    case Baik = 'baik';
    case RusakRingan = 'rusak_ringan';
    case RusakBerat = 'rusak_berat';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik',
            self::RusakRingan => 'Rusak Ringan',
            self::RusakBerat => 'Rusak Berat',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Baik => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::RusakRingan => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::RusakBerat => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
