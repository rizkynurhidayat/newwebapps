<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMutationRequest;
use App\Models\Item;
use App\Models\ItemMutation;
use App\Models\Location;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemMutationController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->query('search');

        $mutations = ItemMutation::with(['item', 'fromLocation', 'toLocation', 'mover'])
            ->when($search, function ($q, $search) {
                $q->where('mutation_code', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($iq) => $iq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('mover', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mutations.index', compact('mutations', 'search'));
    }

    public function create(): View
    {
        $items = Item::with('location')->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('admin.mutations.create', compact('items', 'locations'));
    }

    public function store(ItemMutationRequest $request): RedirectResponse
    {
        $item = Item::findOrFail($request->integer('item_id'));
        $toLocation = Location::findOrFail($request->integer('to_location_id'));
        $quantity = $request->integer('quantity', 1);
        $reason = $request->input('reason');
        $user = auth()->user();

        try {
            $mutation = $this->inventoryService->mutateLocation(
                $item,
                $toLocation,
                $quantity,
                $user,
                $reason
            );

            return redirect()->route('mutations.index')
                ->with('success', "Mutasi barang berhasil dicatat dengan kode: {$mutation->mutation_code}.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
