<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $locationId = $request->query('location_id');
        $status = $request->query('status');
        $condition = $request->query('condition');
        $isConsumable = $request->query('is_consumable');

        $items = Item::with(['category', 'location', 'vendor'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($condition, fn ($q) => $q->where('condition', $condition))
            ->when($isConsumable !== null && $isConsumable !== '', fn ($q) => $q->where('is_consumable', (bool) $isConsumable))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $conditions = ItemCondition::cases();
        $statuses = ItemStatus::cases();

        return view('admin.items.index', compact(
            'items',
            'categories',
            'locations',
            'conditions',
            'statuses',
            'search',
            'categoryId',
            'locationId',
            'status',
            'condition',
            'isConsumable'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $conditions = ItemCondition::cases();
        $statuses = ItemStatus::cases();

        return view('admin.items.create', compact(
            'categories',
            'locations',
            'vendors',
            'conditions',
            'statuses'
        ));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->route('items.index')
            ->with('success', 'Data barang inventaris berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load([
            'category',
            'location',
            'vendor',
            'loans.borrower',
            'loans.officer',
            'mutations.fromLocation',
            'mutations.toLocation',
            'mutations.mover',
            'stockLogs.creator',
        ]);

        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $conditions = ItemCondition::cases();
        $statuses = ItemStatus::cases();

        return view('admin.items.edit', compact(
            'item',
            'categories',
            'locations',
            'vendors',
            'conditions',
            'statuses'
        ));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('items.show', $item)
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->loans()->whereIn('status', [LoanStatus::Dipinjam, LoanStatus::Diajukan])->exists()) {
            return back()->with('error', "Barang '{$item->name}' tidak dapat dihapus karena sedang dalam proses atau berstatus dipinjam.");
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus (soft delete).');
    }

    public function barcode(Item $item): View
    {
        return view('admin.items.barcode', compact('item'));
    }

    public function getNextCode(Category $category): JsonResponse
    {
        $code = $this->inventoryService->generateItemCode($category);

        return response()->json(['code' => $code]);
    }
}
