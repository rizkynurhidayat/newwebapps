<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $locations = Location::withCount('items')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('pic_name', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.locations.index', compact('locations', 'search'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi/Ruangan baru berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('locations.index')
            ->with('success', 'Data lokasi/ruangan berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->items()->exists()) {
            return back()->with('error', "Lokasi '{$location->name}' tidak dapat dihapus karena masih ada barang yang tersimpan di sini.");
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi/Ruangan berhasil dihapus.');
    }
}
