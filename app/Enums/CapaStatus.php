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
            self::Open => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
            self::InProgress => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Implemented => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::Verified => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::Closed => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
        };
    }
}
