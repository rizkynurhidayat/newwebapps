<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Tersedia = 'tersedia';
    case Dipinjam = 'dipinjam';
    case DalamPerbaikan = 'dalam_perbaikan';
    case Dihapuskan = 'dihapuskan';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Dipinjam => 'Sedang Dipinjam',
            self::DalamPerbaikan => 'Dalam Perbaikan',
            self::Dihapuskan => 'Dihapuskan / Rusak Total',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Tersedia => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Dipinjam => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::DalamPerbaikan => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Dihapuskan => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
