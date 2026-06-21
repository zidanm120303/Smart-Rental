<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('settings.manage') || auth()->user()->hasPermission('dashboard.view'), 403, 'Anda tidak memiliki akses ke Settings.');

        return view('pages.settings.index', [
            'settings' => Setting::all()->groupBy('group'),
            'roles' => Role::withCount('users')->get(),
            'categories' => AssetCategory::withCount('assets')->get(),
            'locations' => Location::withCount('assets')->get(),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('settings.manage'), 403, 'Anda tidak memiliki akses mengubah Settings.');

        foreach ($request->except(['_token', '_method']) as $group => $items) {
            foreach ((array) $items as $key => $value) {
                Setting::updateOrCreate(['group' => $group, 'key' => $key], ['value' => $value, 'type' => 'string']);
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
