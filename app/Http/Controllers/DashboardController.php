<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\ItemMutation;
use App\Models\ItemStockLog;
use App\Models\Location;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Metrik Statistik Umum
        $totalItems = Item::count();
        $totalValuation = Item::sum('purchase_price') ?? 0;
        $activeLoansCount = ItemLoan::where('status', LoanStatus::Dipinjam)->count();
        $inRepairCount = Item::where('status', ItemStatus::DalamPerbaikan)->count();
        $damagedCount = Item::whereIn('condition', [ItemCondition::RusakRingan, ItemCondition::RusakBerat])->count();
        $lowStockCount = Item::where('is_consumable', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $totalCategories = Category::count();
        $totalLocations = Location::count();
        $totalVendors = Vendor::count();

        // Aktivitas Terbaru
        $recentLoans = ItemLoan::with(['item', 'borrower'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentMutations = ItemMutation::with(['item', 'fromLocation', 'toLocation', 'mover'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentStockLogs = ItemStockLog::with(['item', 'creator'])
            ->latest('id')
            ->take(5)
            ->get();

        // Data Peminjaman Aktif User yang sedang login (untuk Pegawai biasa)
        $userActiveLoans = $user->isEmployee()
            ? ItemLoan::with('item')
                ->where('user_id', $user->id)
                ->whereIn('status', [LoanStatus::Diajukan, LoanStatus::Dipinjam])
                ->latest('id')
                ->get()
            : collect();

        return view('admin.dashboard', compact(
            'totalItems',
            'totalValuation',
            'activeLoansCount',
            'inRepairCount',
            'damagedCount',
            'lowStockCount',
            'totalCategories',
            'totalLocations',
            'totalVendors',
            'recentLoans',
            'recentMutations',
            'recentStockLogs',
            'userActiveLoans'
        ));
    }
}
