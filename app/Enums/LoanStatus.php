<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Diajukan = 'diajukan';
    case Disetujui = 'disetujui';
    case Dipinjam = 'dipinjam';
    case Kembali = 'kembali';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Menunggu Persetujuan',
            self::Disetujui => 'Disetujui',
            self::Dipinjam => 'Sedang Dipinjam',
            self::Kembali => 'Sudah Dikembalikan',
            self::Ditolak => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Diajukan => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            self::Disetujui => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
            self::Dipinjam => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::Kembali => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Ditolak => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
