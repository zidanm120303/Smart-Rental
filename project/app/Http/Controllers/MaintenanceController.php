<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.view'), 403, 'Anda tidak memiliki akses ke Perawatan.');

        $requests = MaintenanceRequest::with(['asset.category', 'asset.primaryMedia', 'technician'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $selectedRequest = MaintenanceRequest::with(['asset.category', 'asset.location', 'asset.primaryMedia', 'technician', 'checklists'])
            ->find($request->get('maintenance_id')) ?? MaintenanceRequest::with(['asset.category', 'asset.location', 'asset.primaryMedia', 'technician', 'checklists'])->latest()->first();

        return view('pages.maintenance.index', [
            'requests' => $requests,
            'selectedRequest' => $selectedRequest,
            'assets' => Asset::with(['category', 'location', 'primaryMedia'])->where('is_active', true)->orderBy('name')->get(),
            'technicians' => User::whereHas('roles', fn ($query) => $query->whereIn('name', ['teknisi', 'staff_gudang', 'admin_operasional']))->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.manage'), 403, 'Anda tidak memiliki akses menambah perawatan.');

        $validated = $this->validatedPayload($request);

        $maintenanceRequest = DB::transaction(function () use ($validated, $request) {
            $maintenanceRequest = MaintenanceRequest::create([
                ...$validated,
                'reported_by' => auth()->id(),
            ]);

            $this->syncChecklists($maintenanceRequest, $request);

            if (!in_array($maintenanceRequest->status, ['completed'], true)) {
                $maintenanceRequest->asset->update(['availability_status' => 'maintenance']);
            }

            return $maintenanceRequest;
        });

        return redirect()->route('maintenance.index', ['maintenance_id' => $maintenanceRequest->id])->with('success', 'Perintah kerja berhasil dibuat.');
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.manage'), 403, 'Anda tidak memiliki akses mengubah perawatan.');

        $validated = $this->validatedPayload($request, $maintenanceRequest);

        DB::transaction(function () use ($maintenanceRequest, $validated, $request) {
            $maintenanceRequest->update([
                ...$validated,
                'completed_at' => $validated['status'] === 'completed' ? ($maintenanceRequest->completed_at ?? now()) : null,
            ]);

            $this->syncChecklists($maintenanceRequest, $request);

            $maintenanceRequest->asset->update([
                'availability_status' => $validated['status'] === 'completed' ? 'available' : 'maintenance',
                'last_maintenance_at' => $validated['status'] === 'completed' ? now()->toDateString() : $maintenanceRequest->asset->last_maintenance_at,
            ]);
        });

        return redirect()->route('maintenance.index', ['maintenance_id' => $maintenanceRequest->id])->with('success', 'Perintah kerja berhasil diperbarui.');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.manage'), 403, 'Anda tidak memiliki akses menghapus perawatan.');

        DB::transaction(function () use ($maintenanceRequest) {
            if ($maintenanceRequest->status !== 'completed') {
                $maintenanceRequest->asset?->update(['availability_status' => 'available']);
            }

            $maintenanceRequest->delete();
        });

        return redirect()->route('maintenance.index')->with('success', 'Perintah kerja berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?MaintenanceRequest $maintenanceRequest = null): array
    {
        return $request->validate([
            'work_order_code' => ['required', 'string', 'max:50', Rule::unique('maintenance_requests', 'work_order_code')->ignore($maintenanceRequest?->id)],
            'asset_id' => ['required', 'exists:assets,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'issue_title' => ['required', 'string', 'max:180'],
            'issue_type' => ['nullable', 'string', 'max:80'],
            'issue_description' => ['required', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:new,in_progress,waiting_parts,completed'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'scheduled_at' => ['nullable', 'date'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
        ], [
            'work_order_code.required' => 'Kode WO wajib diisi.',
            'work_order_code.unique' => 'Kode WO sudah digunakan.',
            'asset_id.required' => 'Aset wajib dipilih.',
            'issue_title.required' => 'Judul masalah wajib diisi.',
            'issue_description.required' => 'Deskripsi masalah wajib diisi.',
        ]);
    }

    private function syncChecklists(MaintenanceRequest $maintenanceRequest, Request $request): void
    {
        $labels = $request->input('checklist_labels');

        if (is_string($labels)) {
            $labels = preg_split('/\r\n|\r|\n/', $labels);
        }

        if (!is_array($labels)) {
            return;
        }

        $checked = collect($request->input('checked_labels', []))->map(fn ($value) => (string) $value)->all();

        $maintenanceRequest->checklists()->delete();

        foreach ($labels as $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }

            $maintenanceRequest->checklists()->create([
                'label' => $label,
                'is_checked' => in_array($label, $checked, true) || $maintenanceRequest->status === 'completed',
                'type' => 'inspection',
            ]);
        }
    }
}
