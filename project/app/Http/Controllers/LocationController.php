<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('assets.view'), 403, 'Anda tidak memiliki akses ke Lokasi.');

        return view('pages.locations.index', [
            'locations' => Location::withCount('assets')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('assets.create'), 403, 'Anda tidak memiliki akses menambah lokasi.');

        Location::create($this->validatedPayload($request));

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, Location $location)
    {
        abort_unless(auth()->user()->hasPermission('assets.update'), 403, 'Anda tidak memiliki akses mengubah lokasi.');

        $location->update($this->validatedPayload($request, $location));

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        abort_unless(auth()->user()->hasPermission('assets.delete'), 403, 'Anda tidak memiliki akses menghapus lokasi.');

        if ($location->assets()->exists()) {
            return back()->withErrors(['location' => 'Lokasi tidak dapat dihapus karena masih digunakan aset.']);
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?Location $location = null): array
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('locations', 'code')->ignore($location?->id)],
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Kode lokasi wajib diisi.',
            'code.unique' => 'Kode lokasi sudah digunakan.',
            'name.required' => 'Nama lokasi wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
