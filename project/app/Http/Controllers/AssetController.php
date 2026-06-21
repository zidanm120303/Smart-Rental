<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetBrand;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('assets.view'), 403, 'Anda tidak memiliki akses ke Manajemen Aset.');

        $viewMode = $request->get('view', 'table');

        $assets = Asset::query()
            ->with(['category', 'brand', 'location', 'specifications'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, fn ($query, $id) => $query->where('category_id', $id))
            ->when($request->status, fn ($query, $status) => $query->where('availability_status', $status))
            ->when($request->location_id, fn ($query, $id) => $query->where('location_id', $id))
            ->when($request->condition, fn ($query, $condition) => $query->where('condition_status', $condition))
            ->orderByDesc('created_at')
            ->paginate($viewMode === 'grid' ? 12 : 10)
            ->withQueryString();

        $selectedAsset = Asset::with(['category', 'brand', 'location', 'specifications', 'bookingItems.booking.customer', 'maintenanceRequests.technician'])
            ->find($request->get('asset_id'));

        if (!$selectedAsset && $assets->getCollection()->isNotEmpty()) {
            $selectedAsset = $assets->getCollection()->first()->load(['category', 'brand', 'location', 'specifications', 'bookingItems.booking.customer', 'maintenanceRequests.technician']);
        }

        return view('pages.assets.index', [
            'assets' => $assets,
            'selectedAsset' => $selectedAsset,
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'brands' => AssetBrand::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'viewMode' => $viewMode,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('assets.create'), 403, 'Anda tidak memiliki akses menambah aset.');

        $validated = $this->validatedPayload($request);

        $asset = DB::transaction(function () use ($validated) {
            $asset = Asset::create([
                ...$validated,
                'created_by' => auth()->id(),
            ]);

            $this->syncSpecifications($asset, request());

            return $asset;
        });

        return redirect()->route('assets.index', ['asset_id' => $asset->id])->with('success', 'Aset berhasil ditambahkan.');
    }

    public function update(Request $request, Asset $asset)
    {
        abort_unless(auth()->user()->hasPermission('assets.update'), 403, 'Anda tidak memiliki akses mengubah aset.');

        $validated = $this->validatedPayload($request, $asset);

        DB::transaction(function () use ($asset, $validated, $request) {
            $asset->update($validated);
            $this->syncSpecifications($asset, $request);
        });

        return redirect()->route('assets.index', ['asset_id' => $asset->id])->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        abort_unless(auth()->user()->hasPermission('assets.delete'), 403, 'Anda tidak memiliki akses menghapus aset.');

        $hasActiveBooking = $asset->bookingItems()
            ->whereHas('booking', fn ($query) => $query->whereIn('status', ['pending', 'approved', 'active']))
            ->exists();

        if ($hasActiveBooking) {
            return back()->withErrors(['asset' => 'Aset tidak dapat dihapus karena masih ada pemesanan aktif.']);
        }

        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?Asset $asset = null): array
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:50', Rule::unique('assets', 'asset_code')->ignore($asset?->id)],
            'category_id' => ['required', 'exists:asset_categories,id'],
            'brand_id' => ['nullable', 'exists:asset_brands,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'name' => ['required', 'string', 'max:180'],
            'serial_number' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'replacement_value' => ['nullable', 'numeric', 'min:0'],
            'condition_status' => ['required', 'in:excellent,good,fair,damaged'],
            'availability_status' => ['required', 'in:available,rented,reserved,maintenance,retired'],
            'shelf_position' => ['nullable', 'string', 'max:120'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'utilization_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'total_rented' => ['nullable', 'integer', 'min:0'],
            'last_maintenance_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'asset_code.required' => 'Kode aset wajib diisi.',
            'asset_code.unique' => 'Kode aset sudah digunakan.',
            'category_id.required' => 'Kategori aset wajib dipilih.',
            'location_id.required' => 'Lokasi aset wajib dipilih.',
            'name.required' => 'Nama aset wajib diisi.',
            'daily_rate.required' => 'Tarif harian wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['utilization_rate'] = $request->integer('utilization_rate', 0);
        $validated['total_rented'] = $request->integer('total_rented', 0);

        return $validated;
    }

    private function syncSpecifications(Asset $asset, Request $request): void
    {
        $names = $request->input('spec_name', []);
        $values = $request->input('spec_value', []);

        if (!is_array($names) || !is_array($values)) {
            return;
        }

        $asset->specifications()->delete();

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $value = trim((string) ($values[$index] ?? ''));

            if ($name === '' || $value === '') {
                continue;
            }

            $asset->specifications()->create([
                'name' => $name,
                'value' => $value,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
