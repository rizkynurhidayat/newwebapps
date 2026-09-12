<?php

namespace App\Http\Controllers;

use App\Enums\StockLogType;
use App\Http\Requests\StockTransactionRequest;
use App\Models\Item;
use App\Models\ItemStockLog;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $stockLogs = ItemStockLog::with(['item', 'creator'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, function ($q, $search) {
                $q->whereHas('item', fn ($iq) => $iq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                    ->orWhere('notes', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $types = StockLogType::cases();

        return view('admin.stock.index', compact('stockLogs', 'types', 'type', 'search'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        $types = StockLogType::cases();

        return view('admin.stock.create', compact('items', 'types'));
    }

    public function store(StockTransactionRequest $request): RedirectResponse
    {
        $item = Item::findOrFail($request->integer('item_id'));
        $type = StockLogType::from($request->string('type')->value());
        $quantity = $request->integer('quantity');
        $notes = $request->string('notes')->value();
        $user = auth()->user();

        try {
            $this->inventoryService->adjustStock($item, $type, $quantity, $notes, $user);

            return redirect()->route('stock.index')
                ->with('success', "Transaksi stok barang '{$item->name}' berhasil dicatat.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
