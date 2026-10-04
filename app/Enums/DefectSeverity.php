<?php

namespace App\Enums;

enum DefectSeverity: string
{
    case Minor = 'minor';
    case Major = 'major';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Minor => 'Minor (Kecil/Visual)',
            self::Major => 'Major (Mempengaruhi Fungsi/Spesifikasi)',
            self::Critical => 'Critical (Bahaya/Reject Total)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Minor => 'bg-sky-100 text-sky-950 border border-sky-300 font-bold dark:bg-sky-950 dark:text-sky-100 dark:border-sky-700',
            self::Major => 'bg-amber-100 text-amber-950 border border-amber-300 font-bold dark:bg-amber-950 dark:text-amber-100 dark:border-amber-700',
            self::Critical => 'bg-rose-100 text-rose-950 border border-rose-300 font-bold dark:bg-rose-950 dark:text-rose-100 dark:border-rose-700',
        };
    }
}
