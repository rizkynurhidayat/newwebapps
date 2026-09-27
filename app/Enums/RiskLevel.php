<?php

namespace App\Enums;

enum RiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Extreme = 'extreme';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah (Low)',
            self::Medium => 'Sedang (Medium)',
            self::High => 'Tinggi (High)',
            self::Extreme => 'Ekstrem (Extreme)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Medium => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300',
            self::High => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/30 dark:text-orange-300',
            self::Extreme => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-300 font-bold',
        };
    }

    public static function fromScore(int $score): self
    {
        if ($score <= 4) {
            return self::Low;
        }

        if ($score <= 9) {
            return self::Medium;
        }

        if ($score <= 15) {
            return self::High;
        }

        return self::Extreme;
    }
}
