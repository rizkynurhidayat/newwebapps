<?php

namespace App\Enums;

enum ProductionShift: string
{
    case Shift1 = 'Shift 1';
    case Shift2 = 'Shift 2';
    case Shift3 = 'Shift 3';

    public function label(): string
    {
        return match ($this) {
            self::Shift1 => 'Shift 1 (Pagi 07:00 - 15:00)',
            self::Shift2 => 'Shift 2 (Sore 15:00 - 23:00)',
            self::Shift3 => 'Shift 3 (Malam 23:00 - 07:00)',
        };
    }
}
