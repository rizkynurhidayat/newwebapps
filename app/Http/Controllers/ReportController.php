<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\ItemMutation;
use App\Models\ItemStockLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('report_type', 'items');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $categoryId = $request->query('category_id');
        $locationId = $request->query('location_id');

        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        $items = collect();
        $loans = collect();
        $mutations = collect();
        $stockLogs = collect();

        if ($type === 'items') {
            $items = Item::with(['category', 'location', 'vendor'])
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
                ->latest('id')
                ->get();
        } elseif ($type === 'loans') {
            $loans = ItemLoan::with(['item', 'borrower', 'officer'])
                ->whereBetween('loan_date', [$startDate, $endDate])
                ->latest('id')
                ->get();
        } elseif ($type === 'mutations') {
            $mutations = ItemMutation::with(['item', 'fromLocation', 'toLocation', 'mover'])
                ->whereBetween('mutation_date', [$startDate, $endDate])
                ->latest('id')
                ->get();
        } elseif ($type === 'stock') {
            $stockLogs = ItemStockLog::with(['item', 'creator'])
                ->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"])
                ->latest('id')
                ->get();
        }

        return view('admin.reports.index', compact(
            'type',
            'startDate',
            'endDate',
            'categoryId',
            'locationId',
            'categories',
            'locations',
            'items',
            'loans',
            'mutations',
            'stockLogs'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->query('report_type', 'items');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $categoryId = $request->query('category_id');
        $locationId = $request->query('location_id');

        $filename = "laporan_{$type}_".now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($type, $startDate, $endDate, $categoryId, $locationId) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            if ($type === 'items') {
                fputcsv($handle, ['Kode Barang', 'Barcode', 'Nama Barang', 'Kategori', 'Lokasi', 'Stok', 'Satuan', 'Kondisi', 'Status', 'Harga Perolehan']);
                Item::with(['category', 'location'])
                    ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                    ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
                    ->chunk(200, function ($items) use ($handle) {
                        foreach ($items as $item) {
                            fputcsv($handle, [
                                $item->code,
                                $item->barcode ?? '-',
                                $item->name,
                                $item->category->name ?? '-',
                                $item->location->name ?? '-',
                                $item->stock,
                                $item->unit,
                                $item->condition->label(),
                                $item->status->label(),
                                $item->purchase_price ? (float) $item->purchase_price : 0,
                            ]);
                        }
                    });
            } elseif ($type === 'loans') {
                fputcsv($handle, ['Kode Peminjaman', 'Nama Barang', 'Peminjam', 'Jumlah', 'Tgl Pinjam', 'Batas Kembali', 'Tgl Kembali', 'Status', 'Catatan']);
                ItemLoan::with(['item', 'borrower'])
                    ->whereBetween('loan_date', [$startDate, $endDate])
                    ->chunk(200, function ($loans) use ($handle) {
                        foreach ($loans as $loan) {
                            fputcsv($handle, [
                                $loan->loan_code,
                                $loan->item->name ?? '-',
                                $loan->borrower->name ?? '-',
                                $loan->quantity,
                                $loan->loan_date?->format('d/m/Y'),
                                $loan->due_date?->format('d/m/Y'),
                                $loan->return_date?->format('d/m/Y') ?? '-',
                                $loan->status->label(),
                                $loan->notes ?? '-',
                            ]);
                        }
                    });
            } elseif ($type === 'mutations') {
                fputcsv($handle, ['Kode Mutasi', 'Nama Barang', 'Dari Lokasi', 'Ke Lokasi', 'Jumlah', 'Dipindahkan Oleh', 'Tgl Mutasi', 'Alasan']);
                ItemMutation::with(['item', 'fromLocation', 'toLocation', 'mover'])
                    ->whereBetween('mutation_date', [$startDate, $endDate])
                    ->chunk(200, function ($mutations) use ($handle) {
                        foreach ($mutations as $mut) {
                            fputcsv($handle, [
                                $mut->mutation_code,
                                $mut->item->name ?? '-',
                                $mut->fromLocation->name ?? '-',
                                $mut->toLocation->name ?? '-',
                                $mut->quantity,
                                $mut->mover->name ?? '-',
                                $mut->mutation_date?->format('d/m/Y'),
                                $mut->reason ?? '-',
                            ]);
                        }
                    });
            } elseif ($type === 'stock') {
                fputcsv($handle, ['Nama Barang', 'Tipe Transaksi', 'Kuantitas', 'Stok Sebelum', 'Stok Sesudah', 'Dicatat Oleh', 'Waktu', 'Catatan']);
                ItemStockLog::with(['item', 'creator'])
                    ->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"])
                    ->chunk(200, function ($logs) use ($handle) {
                        foreach ($logs as $log) {
                            fputcsv($handle, [
                                $log->item->name ?? '-',
                                $log->type->label(),
                                $log->quantity,
                                $log->before_stock,
                                $log->after_stock,
                                $log->creator->name ?? '-',
                                $log->created_at->format('d/m/Y H:i'),
                                $log->notes ?? '-',
                            ]);
                        }
                    });
            }

            fclose($handle);
        }, 200, $headers);
    }
}
