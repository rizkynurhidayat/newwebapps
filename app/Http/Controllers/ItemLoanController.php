<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Http\Requests\ItemLoanRequest;
use App\Http\Requests\ItemReturnRequest;
use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\User;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemLoanController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = ItemLoan::with(['item', 'borrower', 'officer']);

        // Jika pegawai biasa, hanya tampilkan permohonan milik sendiri
        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        $loans = $query
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q, $search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($iq) => $iq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('borrower', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $statuses = LoanStatus::cases();
        $conditions = ItemCondition::cases();

        return view('admin.loans.index', compact('loans', 'statuses', 'conditions', 'status', 'search'));
    }

    public function create(): View
    {
        $user = auth()->user();

        // Ambil barang yang berstatus tersedia
        $availableItems = Item::with(['category', 'location'])
            ->where('status', ItemStatus::Tersedia)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $users = $user->canManageInventory()
            ? User::where('is_active', true)->orderBy('name')->get()
            : collect([$user]);

        return view('admin.loans.create', compact('availableItems', 'users'));
    }

    public function store(ItemLoanRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $item = Item::findOrFail($request->integer('item_id'));

        $borrower = $user->canManageInventory()
            ? User::findOrFail($request->integer('user_id'))
            : $user;

        $officer = $user->canManageInventory() ? $user : null;

        try {
            $loan = $this->inventoryService->borrowItem(
                $item,
                $borrower,
                $request->validated(),
                $officer
            );

            $msg = $loan->status === LoanStatus::Dipinjam
                ? "Peminjaman barang berhasil dicatat dengan kode: {$loan->loan_code}."
                : "Permohonan peminjaman berhasil diajukan dengan kode: {$loan->loan_code}. Menunggu persetujuan staf logistik.";

            return redirect()->route('loans.index')->with('success', $msg);
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function approve(ItemLoan $loan): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->canManageInventory()) {
            abort(403);
        }

        try {
            $this->inventoryService->approveLoan($loan, $user);

            return redirect()->route('loans.index')
                ->with('success', "Peminjaman {$loan->loan_code} berhasil disetujui.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ItemLoan $loan): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->canManageInventory()) {
            abort(403);
        }

        $reason = $request->input('reason');

        try {
            $this->inventoryService->rejectLoan($loan, $user, $reason);

            return redirect()->route('loans.index')
                ->with('success', "Pengajuan peminjaman {$loan->loan_code} telah ditolak.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnItem(ItemReturnRequest $request, ItemLoan $loan): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->canManageInventory()) {
            abort(403);
        }

        $condition = ItemCondition::from($request->string('return_condition')->value());
        $notes = $request->input('return_notes');

        try {
            $this->inventoryService->returnItem($loan, $condition, $notes, $user);

            return redirect()->route('loans.index')
                ->with('success', "Barang untuk peminjaman {$loan->loan_code} telah berhasil dikembalikan.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
