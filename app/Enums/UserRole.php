<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Plant Manager / Administrator',
            self::Staff => 'Quality Control (QC) Inspector',
            self::Employee => 'Production Supervisor / Staff',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Admin => 'bg-purple-100 text-purple-950 border border-purple-300 font-bold dark:bg-purple-950 dark:text-purple-100 dark:border-purple-700',
            self::Staff => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
            self::Employee => 'bg-blue-100 text-blue-950 border border-blue-300 font-bold dark:bg-blue-950 dark:text-blue-100 dark:border-blue-700',
        };
    }
}
