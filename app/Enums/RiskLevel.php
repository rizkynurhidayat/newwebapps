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
            self::Low => 'bg-emerald-100 text-emerald-950 border border-emerald-400 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
            self::Medium => 'bg-amber-100 text-amber-950 border border-amber-400 font-bold dark:bg-amber-950 dark:text-amber-100 dark:border-amber-700',
            self::High => 'bg-orange-100 text-orange-950 border border-orange-400 font-bold dark:bg-orange-950 dark:text-orange-100 dark:border-orange-700',
            self::Extreme => 'bg-rose-100 text-rose-950 border border-rose-400 font-bold dark:bg-rose-950 dark:text-rose-100 dark:border-rose-700',
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
