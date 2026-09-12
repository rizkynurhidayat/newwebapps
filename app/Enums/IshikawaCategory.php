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
            self::Man => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            self::Machine => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            self::Method => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::Material => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Measurement => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::Environment => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300',
        };
    }
}
