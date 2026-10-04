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
            self::Passed => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
            self::Conditional => 'bg-amber-100 text-amber-950 border border-amber-300 font-bold dark:bg-amber-950 dark:text-amber-100 dark:border-amber-700',
            self::Rejected => 'bg-rose-100 text-rose-950 border border-rose-300 font-bold dark:bg-rose-950 dark:text-rose-100 dark:border-rose-700',
        };
    }
}
