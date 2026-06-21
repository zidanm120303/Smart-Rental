<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('inventory.view'), 403, 'Anda tidak memiliki akses ke Inventori.');

        $items = InventoryItem::with('location')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categoryCount = InventoryItem::query()->distinct('category')->count('category');

        return view('pages.inventory.index', [
            'items' => $items,
            'categoryCount' => $categoryCount,
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'categories' => InventoryItem::query()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('inventory.manage'), 403, 'Anda tidak memiliki akses menambah inventori.');

        $validated = $this->validatedPayload($request);

        $item = DB::transaction(function () use ($validated) {
            $item = InventoryItem::create($validated);
            $item->movements()->create([
                'user_id' => auth()->id(),
                'type' => 'masuk',
                'quantity' => $item->stock,
                'reference_number' => 'STOK-AWAL-' . $item->sku,
                'notes' => 'Stok awal item inventori.',
            ]);

            return $item;
        });

        return redirect()->route('inventory.index')->with('success', 'Item inventori berhasil ditambahkan.');
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        abort_unless(auth()->user()->hasPermission('inventory.manage'), 403, 'Anda tidak memiliki akses mengubah inventori.');

        $oldStock = $inventoryItem->stock;
        $validated = $this->validatedPayload($request, $inventoryItem);

        DB::transaction(function () use ($inventoryItem, $validated, $oldStock) {
            $inventoryItem->update($validated);

            $delta = $inventoryItem->stock - $oldStock;
            if ($delta !== 0) {
                $inventoryItem->movements()->create([
                    'user_id' => auth()->id(),
                    'type' => $delta > 0 ? 'masuk' : 'keluar',
                    'quantity' => abs($delta),
                    'reference_number' => 'ADJ-' . now()->format('YmdHis'),
                    'notes' => 'Penyesuaian stok dari form edit.',
                ]);
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Item inventori berhasil diperbarui.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        abort_unless(auth()->user()->hasPermission('inventory.manage'), 403, 'Anda tidak memiliki akses menghapus inventori.');

        $inventoryItem->delete();

        return redirect()->route('inventory.index')->with('success', 'Item inventori berhasil dihapus.');
    }

    public function moveStock(Request $request, InventoryItem $inventoryItem)
    {
        abort_unless(auth()->user()->hasPermission('inventory.manage'), 403, 'Anda tidak memiliki akses mutasi stok.');

        $validated = $request->validate([
            'type' => ['required', 'in:masuk,keluar,penyesuaian'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($inventoryItem, $validated) {
            $quantity = (int) $validated['quantity'];
            $newStock = match ($validated['type']) {
                'masuk' => $inventoryItem->stock + $quantity,
                'keluar' => $inventoryItem->stock - $quantity,
                default => $quantity,
            };

            if ($newStock < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Stok tidak boleh kurang dari nol.',
                ]);
            }

            $inventoryItem->update(['stock' => $newStock]);
            $inventoryItem->movements()->create([
                'user_id' => auth()->id(),
                ...$validated,
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }

    private function validatedPayload(Request $request, ?InventoryItem $item = null): array
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('inventory_items', 'sku')->ignore($item?->id)],
            'name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan.',
            'name.required' => 'Nama item wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
