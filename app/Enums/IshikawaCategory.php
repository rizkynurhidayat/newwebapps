<?php

namespace App\Enums;

enum IshikawaCategory: string
{
    case Man = 'man';
    case Machine = 'machine';
    case Method = 'method';
    case Material = 'material';
    case Measurement = 'measurement';
    case Environment = 'environment';

    public function label(): string
    {
        return match ($this) {
            self::Man => 'Man (Manusia / Operator)',
            self::Machine => 'Machine (Mesin / Peralatan)',
            self::Method => 'Method (Metode / Prosedur SOP)',
            self::Material => 'Material (Bahan Baku / Komponen)',
            self::Measurement => 'Measurement (Pengukuran / Kalibrasi)',
            self::Environment => 'Environment (Lingkungan Kerja / Suhu)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Man => 'user',
            self::Machine => 'cog',
            self::Method => 'clipboard-document-list',
            self::Material => 'cube',
            self::Measurement => 'scale',
            self::Environment => 'sun',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Man => 'bg-indigo-100 text-indigo-950 border border-indigo-300 font-bold dark:bg-indigo-950 dark:text-indigo-100 dark:border-indigo-700',
            self::Machine => 'bg-orange-100 text-orange-950 border border-orange-300 font-bold dark:bg-orange-950 dark:text-orange-100 dark:border-orange-700',
            self::Method => 'bg-blue-100 text-blue-950 border border-blue-300 font-bold dark:bg-blue-950 dark:text-blue-100 dark:border-blue-700',
            self::Material => 'bg-emerald-100 text-emerald-950 border border-emerald-300 font-bold dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-700',
            self::Measurement => 'bg-purple-100 text-purple-950 border border-purple-300 font-bold dark:bg-purple-950 dark:text-purple-100 dark:border-purple-700',
            self::Environment => 'bg-teal-100 text-teal-950 border border-teal-300 font-bold dark:bg-teal-950 dark:text-teal-100 dark:border-teal-700',
        };
    }
}
