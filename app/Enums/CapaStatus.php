<?php

namespace App\Enums;

enum CapaStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Implemented = 'implemented';
    case Verified = 'verified';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open (Baru Dibuat)',
            self::InProgress => 'In Progress (Dalam Penanganan)',
            self::Implemented => 'Implemented (Sudah Diterapkan)',
            self::Verified => 'Verified (Efektivitas Teruji)',
            self::Closed => 'Closed (Selesai & Ditutup)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'bg-rose-100 text-rose-950 border border-rose-300 font-bold dark:bg-rose-950 dark:text-rose-100 dark:border-rose-700',
            self::InProgress => 'bg-amber-100 text-amber-950 border border-amber-300 font-bold dark:bg-amber-950 dark:text-amber-100 dark:border-amber-700',
            self::Implemented => 'bg-blue-100 text-blue-950 border border-blue-300 font-bold dark:bg-blue-950 dark:text-blue-100 dark:border-blue-700',
            self::Verified => 'bg-purple-100 text-purple-950 border border-purple-300 font-bold dark:bg-purple-950 dark:text-purple-100 dark:border-purple-700',
            self::Closed => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
        };
    }
}
