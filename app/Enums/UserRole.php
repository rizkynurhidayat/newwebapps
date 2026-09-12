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
            self::Admin => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::Staff => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Employee => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        };
    }
}
