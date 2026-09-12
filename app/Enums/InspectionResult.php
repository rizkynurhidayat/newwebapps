<?php

namespace App\Enums;

enum InspectionResult: string
{
    case Passed = 'passed';
    case Conditional = 'conditional';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'Lulus (Passed)',
            self::Conditional => 'Lulus Bersyarat (Rework)',
            self::Rejected => 'Ditolak / Afkir (Rejected)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Passed => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-300',
            self::Conditional => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-300',
            self::Rejected => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 border-rose-300',
        };
    }
}
