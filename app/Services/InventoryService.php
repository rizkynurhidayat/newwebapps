<?php

namespace App\Services;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Enums\StockLogType;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\ItemMutation;
use App\Models\ItemStockLog;
use App\Models\Location;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Membuat pengajuan peminjaman atau langsung meminjamkan barang.
     *
     * @param  array{quantity?: int, loan_date: string, due_date: string, notes?: string|null}  $data
     */
    public function borrowItem(Item $item, User $borrower, array $data, ?User $processedBy = null): ItemLoan
    {
        $quantity = $data['quantity'] ?? 1;

        if (! $item->isAvailable()) {
            throw new DomainException("Barang '{$item->name}' saat ini sedang tidak tersedia untuk dipinjam.");
        }

        if ($item->is_consumable && $item->stock < $quantity) {
            throw new DomainException("Stok barang tidak mencukupi untuk dipinjam. Stok tersedia: {$item->stock}.");
        }

        return DB::transaction(function () use ($item, $borrower, $data, $processedBy, $quantity) {
            $isDirectApproval = $processedBy !== null && $processedBy->canManageInventory();
            $status = $isDirectApproval ? LoanStatus::Dipinjam : LoanStatus::Diajukan;

            $loan = ItemLoan::create([
                'loan_code' => $this->generateLoanCode(),
                'item_id' => $item->id,
                'user_id' => $borrower->id,
                'processed_by' => $isDirectApproval ? $processedBy->id : null,
                'quantity' => $quantity,
                'loan_date' => $data['loan_date'],
                'due_date' => $data['due_date'],
                'status' => $status,
                'notes' => $data['notes'] ?? null,
            ]);

            if ($status === LoanStatus::Dipinjam) {
                if ($item->is_consumable) {
                    $before = $item->stock;
                    $item->decrement('stock', $quantity);
                    ItemStockLog::create([
                        'item_id' => $item->id,
                        'type' => StockLogType::Out,
                        'quantity' => $quantity,
                        'before_stock' => $before,
                        'after_stock' => $item->fresh()->stock,
                        'notes' => "Peminjaman: {$loan->loan_code} oleh {$borrower->name}",
                        'created_by' => $processedBy?->id,
                    ]);
                } else {
                    $item->update(['status' => ItemStatus::Dipinjam]);
                }
            }

            return $loan;
        });
    }

    /**
     * Menyetujui pengajuan peminjaman barang oleh petugas.
     */
    public function approveLoan(ItemLoan $loan, User $officer): ItemLoan
    {
        if ($loan->status !== LoanStatus::Diajukan) {
            throw new DomainException('Hanya peminjaman dengan status Diajukan yang dapat disetujui.');
        }

        $item = $loan->item;

        if (! $item->isAvailable()) {
            throw new DomainException("Barang '{$item->name}' sudah tidak tersedia.");
        }

        return DB::transaction(function () use ($loan, $item, $officer) {
            $loan->update([
                'status' => LoanStatus::Dipinjam,
                'processed_by' => $officer->id,
            ]);

            if ($item->is_consumable) {
                $before = $item->stock;
                $item->decrement('stock', $loan->quantity);
                ItemStockLog::create([
                    'item_id' => $item->id,
                    'type' => StockLogType::Out,
                    'quantity' => $loan->quantity,
                    'before_stock' => $before,
                    'after_stock' => $item->fresh()->stock,
                    'notes' => "Persetujuan Peminjaman: {$loan->loan_code} oleh {$loan->borrower->name}",
                    'created_by' => $officer->id,
                ]);
            } else {
                $item->update(['status' => ItemStatus::Dipinjam]);
            }

            return $loan->fresh();
        });
    }

    /**
     * Menolak pengajuan peminjaman barang.
     */
    public function rejectLoan(ItemLoan $loan, User $officer, ?string $reason = null): ItemLoan
    {
        if ($loan->status !== LoanStatus::Diajukan) {
            throw new DomainException('Hanya peminjaman dengan status Diajukan yang dapat ditolak.');
        }

        $notes = $loan->notes;
        if ($reason) {
            $notes = ($notes ? $notes."\n" : '').'[Alasan Penolakan]: '.$reason;
        }

        $loan->update([
            'status' => LoanStatus::Ditolak,
            'processed_by' => $officer->id,
            'notes' => $notes,
        ]);

        return $loan;
    }

    /**
     * Memproses pengembalian barang yang sedang dipinjam.
     */
    public function returnItem(ItemLoan $loan, ItemCondition $returnCondition, ?string $returnNotes = null, ?User $officer = null): ItemLoan
    {
        if ($loan->status !== LoanStatus::Dipinjam) {
            throw new DomainException('Hanya peminjaman berstatus Dipinjam yang dapat dikembalikan.');
        }

        return DB::transaction(function () use ($loan, $returnCondition, $returnNotes, $officer) {
            $item = $loan->item;

            $loan->update([
                'status' => LoanStatus::Kembali,
                'return_date' => now()->toDateString(),
                'return_condition' => $returnCondition,
                'return_notes' => $returnNotes,
                'processed_by' => $officer ? $officer->id : $loan->processed_by,
            ]);

            if (! $item->is_consumable) {
                $newStatus = match ($returnCondition) {
                    ItemCondition::Baik => ItemStatus::Tersedia,
                    ItemCondition::RusakRingan => ItemStatus::DalamPerbaikan,
                    ItemCondition::RusakBerat => ItemStatus::DalamPerbaikan,
                };

                $item->update([
                    'condition' => $returnCondition,
                    'status' => $newStatus,
                ]);
            }

            return $loan->fresh();
        });
    }

    /**
     * Memindahkan lokasi fisik barang dan mencatat riwayat mutasi.
     */
    public function mutateLocation(Item $item, Location $toLocation, int $quantity, User $movedBy, ?string $reason = null): ItemMutation
    {
        if ($item->location_id === $toLocation->id) {
            throw new DomainException('Lokasi tujuan mutasi tidak boleh sama dengan lokasi saat ini.');
        }

        return DB::transaction(function () use ($item, $toLocation, $quantity, $movedBy, $reason) {
            $fromLocationId = $item->location_id;

            $mutation = ItemMutation::create([
                'mutation_code' => $this->generateMutationCode(),
                'item_id' => $item->id,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocation->id,
                'quantity' => $quantity,
                'moved_by' => $movedBy->id,
                'mutation_date' => now()->toDateString(),
                'reason' => $reason,
            ]);

            $item->update([
                'location_id' => $toLocation->id,
            ]);

            return $mutation;
        });
    }

    /**
     * Menyesuaikan stok barang (barang masuk, barang keluar, atau penyesuaian opname).
     */
    public function adjustStock(Item $item, StockLogType $type, int $quantity, string $notes, User $user): ItemStockLog
    {
        if ($quantity <= 0) {
            throw new DomainException('Jumlah stok harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($item, $type, $quantity, $notes, $user) {
            $before = $item->stock;

            $after = match ($type) {
                StockLogType::In => $before + $quantity,
                StockLogType::Out => $before - $quantity,
                StockLogType::Adjustment => $quantity,
            };

            if ($after < 0) {
                throw new DomainException("Stok tidak boleh bernilai negatif. Stok saat ini: {$before}.");
            }

            $item->update([
                'stock' => $after,
                'status' => $after > 0 ? ItemStatus::Tersedia : ItemStatus::DalamPerbaikan,
            ]);

            return ItemStockLog::create([
                'item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantity,
                'before_stock' => $before,
                'after_stock' => $after,
                'notes' => $notes,
                'created_by' => $user->id,
            ]);
        });
    }

    /**
     * Menghasilkan kode unik barang berdasarkan kode kategori.
     */
    public function generateItemCode(Category $category): string
    {
        $prefix = 'BRG-'.strtoupper($category->code).'-';
        $latest = Item::where('code', 'LIKE', $prefix.'%')->latest('id')->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->code, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghasilkan kode unik peminjaman.
     */
    public function generateLoanCode(): string
    {
        $date = now()->format('Ym');
        $prefix = "PJ-{$date}-";
        $latest = ItemLoan::where('loan_code', 'LIKE', "{$prefix}%")->latest('id')->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->loan_code, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghasilkan kode unik mutasi lokasi.
     */
    public function generateMutationCode(): string
    {
        $date = now()->format('Ym');
        $prefix = "MUT-{$date}-";
        $latest = ItemMutation::where('mutation_code', 'LIKE', "{$prefix}%")->latest('id')->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->mutation_code, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
