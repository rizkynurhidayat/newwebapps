<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $vendors = Vendor::withCount('items')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.vendors.index', compact('vendors', 'search'));
    }

    public function create(): View
    {
        return view('admin.vendors.create');
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        Vendor::create($request->validated());

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor baru berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor): View
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(VendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($request->validated());

        return redirect()->route('vendors.index')
            ->with('success', 'Data vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
