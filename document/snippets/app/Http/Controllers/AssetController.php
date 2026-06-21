<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('assets.view'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        $viewMode = $request->get('view', 'table');

        $assets = Asset::query()
            ->with(['category', 'brand', 'location'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            }))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('availability_status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('pages.assets.index', compact('assets', 'categories', 'locations', 'viewMode'));
    }
}
