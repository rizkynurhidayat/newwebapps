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
            self::Minor => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
            self::Major => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Critical => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 font-semibold',
        };
    }
}
