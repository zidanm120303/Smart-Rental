<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetBrand;
use App\Models\AssetCategory;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SmartRentalSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            'bookings.view',
            'bookings.create',
            'bookings.update',
            'bookings.cancel',
            'bookings.approve',
            'customers.view',
            'customers.manage',
            'calendar.view',
            'invoices.view',
            'invoices.manage',
            'payments.manage',
            'maintenance.view',
            'maintenance.manage',
            'inventory.view',
            'inventory.manage',
            'reports.view',
            'users.manage',
            'settings.manage',
            'activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['group_name' => Str::before($permission, '.'), 'description' => 'Akses ' . $permission]
            );
        }

        $this->seedRoles($permissions);
        $this->seedUsers();
        $locations = $this->seedLocations();
        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $assets = $this->seedAssets($categories, $brands, $locations);
        $customers = $this->seedCustomers();
        $bookings = $this->seedBookings($customers, $assets);
        $this->seedInvoices($bookings);
        $this->refreshCustomerTotals();
        $this->seedMaintenance($assets);
        $this->seedInventory($locations);
        $this->seedSettings();
    }

    private function seedRoles(array $permissions): void
    {
        $roles = [
            'pemilik' => ['Pemilik', $permissions],
            'admin_operasional' => ['Admin Operasional', $permissions],
            'staff_gudang' => ['Staf Gudang', ['dashboard.view', 'assets.view', 'assets.update', 'bookings.view', 'bookings.update', 'calendar.view', 'inventory.view', 'inventory.manage']],
            'teknisi' => ['Teknisi', ['dashboard.view', 'assets.view', 'calendar.view', 'maintenance.view', 'maintenance.manage', 'inventory.view']],
            'finance' => ['Finance', ['dashboard.view', 'customers.view', 'calendar.view', 'invoices.view', 'invoices.manage', 'payments.manage', 'reports.view']],
        ];

        foreach ($roles as $name => [$displayName, $rolePermissions]) {
            $role = Role::updateOrCreate(['name' => $name], ['display_name' => $displayName]);
            $role->permissions()->sync(Permission::whereIn('name', $rolePermissions)->pluck('id')->all());
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['Pemilik Sistem', 'pemilik@smartrental.local', 'pemilik', '0812-1000-0001'],
            ['Admin Operasional', 'admin@smartrental.local', 'admin_operasional', '0812-1000-0002'],
            ['Staf Gudang', 'gudang@smartrental.local', 'staff_gudang', '0812-1000-0003'],
            ['Teknisi', 'teknisi@smartrental.local', 'teknisi', '0812-1000-0004'],
            ['Finance', 'finance@smartrental.local', 'finance', '0812-1000-0005'],
        ];

        foreach ($users as [$name, $email, $roleName, $phone]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'phone' => $phone,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'last_login_at' => $email === 'admin@smartrental.local' ? now() : null,
                ]
            );

            $user->roles()->sync([Role::where('name', $roleName)->value('id')]);
        }
    }

    private function seedLocations()
    {
        return collect([
            ['GDG-001', 'Gudang Utama Jakarta', 'gudang', 'Jl. Kemang Raya No. 21', 'Jakarta'],
            ['STD-002', 'Studio Kamera A', 'studio', 'Jl. Senopati No. 8', 'Jakarta'],
            ['STD-003', 'Studio Audio B', 'studio', 'Jl. Radio Dalam No. 12', 'Jakarta'],
            ['GDG-004', 'Gudang Bandung', 'gudang', 'Jl. Asia Afrika No. 4', 'Bandung'],
            ['PCK-005', 'Titik Pickup Surabaya', 'pickup', 'Jl. Basuki Rahmat No. 18', 'Surabaya'],
            ['GDG-006', 'Gudang Yogyakarta', 'gudang', 'Jl. Kaliurang No. 30', 'Yogyakarta'],
            ['STD-007', 'Studio Live Streaming', 'studio', 'Jl. Pejaten Barat No. 7', 'Jakarta'],
            ['RAK-008', 'Rak Media dan Aksesori', 'rak', 'Area Gudang Utama', 'Jakarta'],
            ['RAK-009', 'Rak Lighting dan Grip', 'rak', 'Area Studio Kamera A', 'Jakarta'],
            ['GDG-010', 'Gudang Denpasar', 'gudang', 'Jl. Teuku Umar No. 45', 'Denpasar'],
        ])->mapWithKeys(fn ($row) => [
            $row[0] => Location::updateOrCreate(
                ['code' => $row[0]],
                ['name' => $row[1], 'type' => $row[2], 'address' => $row[3], 'city' => $row[4], 'phone' => '021-555-' . substr($row[0], -3), 'is_active' => true]
            ),
        ]);
    }

    private function seedCategories()
    {
        return collect(['Kamera', 'Lensa', 'Drone', 'Audio', 'Speaker', 'Lighting', 'Tripod', 'Mixer', 'Monitor', 'Aksesori'])
            ->mapWithKeys(fn ($name) => [
                $name => AssetCategory::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'icon' => Str::slug($name), 'description' => 'Kategori aset ' . strtolower($name), 'is_active' => true]
                ),
            ]);
    }

    private function seedBrands()
    {
        return collect(['Sony', 'Canon', 'DJI', 'Sennheiser', 'JBL', 'Aputure', 'Manfrotto', 'Yamaha', 'Atomos', 'SanDisk'])
            ->mapWithKeys(fn ($name) => [
                $name => AssetBrand::updateOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name), 'description' => 'Merek peralatan ' . $name, 'is_active' => true]
                ),
            ]);
    }

    private function seedAssets($categories, $brands, $locations)
    {
        $templates = $this->assetTemplates();
        $locationList = $locations->values();
        $assets = collect();

        foreach (range(1, 100) as $sequence) {
            $template = $templates[($sequence - 1) % count($templates)];
            $code = sprintf('AST-%s-%04d', $template['code'], $sequence);
            $status = match (true) {
                $sequence % 25 === 0 => 'maintenance',
                $sequence % 13 === 0 => 'rented',
                $sequence % 9 === 0 => 'reserved',
                default => 'available',
            };

            $asset = Asset::updateOrCreate(
                ['asset_code' => $code],
                [
                    'category_id' => $categories[$template['category']]->id,
                    'brand_id' => $brands[$template['brand']]->id,
                    'location_id' => $locationList[($sequence - 1) % $locationList->count()]->id,
                    'name' => $template['name'] . ' #' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                    'serial_number' => $code . '-SN',
                    'description' => 'Aset rental profesional dengan gambar produk berlatar flat untuk katalog operasional.',
                    'purchase_date' => Carbon::parse('2025-01-01')->addDays($sequence * 3),
                    'purchase_price' => $template['rate'] * 45,
                    'daily_rate' => $template['rate'],
                    'deposit_amount' => $template['deposit'],
                    'replacement_value' => $template['rate'] * 70,
                    'condition_status' => ['excellent', 'good', 'good', 'fair'][($sequence - 1) % 4],
                    'availability_status' => $status,
                    'shelf_position' => sprintf('Rak %s-%02d', $template['code'], (($sequence - 1) % 20) + 1),
                    'image_url' => '/assets/equipment/flat/' . $template['image'],
                    'barcode' => str_replace('-', '', $code),
                    'utilization_rate' => 25 + ($sequence * 7) % 56,
                    'total_rented' => 4 + ($sequence * 5) % 72,
                    'last_maintenance_at' => Carbon::parse('2026-01-05')->addDays($sequence),
                    'is_active' => true,
                    'created_by' => User::where('email', 'admin@smartrental.local')->value('id'),
                ]
            );

            $asset->specifications()->delete();
            foreach ($this->specsFor($template['category'], $template['name']) as $index => [$name, $value]) {
                $asset->specifications()->create(['name' => $name, 'value' => $value, 'sort_order' => $index + 1]);
            }

            $assets->push($asset);
        }

        return $assets;
    }

    private function seedCustomers()
    {
        $names = [
            'BrightFrame Productions',
            'Northline Studios',
            'Visionary Films',
            'Event Horizon Co.',
            'Creative Lens Media',
            'Nusantara Broadcast',
            'Orbit Creative Lab',
            'PixelWorks Studio',
            'Garuda Event Production',
            'Metro Live Streaming',
        ];
        $contacts = ['Marcus Chen', 'David Kim', 'Priya Nair', 'Lena Patel', 'Raka Pratama', 'Maya Anindita', 'Hendra Wijaya', 'Nadia Putri', 'Fajar Maulana', 'Tania Kirana'];
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Denpasar', 'Makassar', 'Medan'];
        $customers = collect();

        foreach (range(1, 100) as $sequence) {
            $name = $names[($sequence - 1) % count($names)] . ' ' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $code = sprintf('CUST-%04d', $sequence);
            $type = $sequence % 5 === 0 ? 'personal' : 'company';

            $customers->push(Customer::updateOrCreate(
                ['customer_code' => $code],
                [
                    'type' => $type,
                    'name' => $name,
                    'contact_person' => $contacts[($sequence - 1) % count($contacts)],
                    'email' => 'pelanggan' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT) . '@smartrental.test',
                    'phone' => '0812-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT) . '-' . str_pad((string) (($sequence * 37) % 10000), 4, '0', STR_PAD_LEFT),
                    'address' => 'Alamat operasional ' . $name,
                    'city' => $cities[($sequence - 1) % count($cities)],
                    'identity_type' => $type === 'company' ? 'NIB' : 'KTP',
                    'identity_number' => 'ID-2026-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                    'verification_status' => $sequence % 11 === 0 ? 'pending' : 'verified',
                    'customer_level' => $sequence % 7 === 0 ? 'vip' : 'reguler',
                    'tag' => ['Production House', 'Event Organizer', 'Agency', 'Corporate', 'Content Creator'][($sequence - 1) % 5],
                    'lifetime_value' => 0,
                    'total_bookings' => 0,
                    'customer_since' => Carbon::parse('2024-01-01')->addDays($sequence * 5),
                    'notes' => 'Data awal pelanggan berurutan untuk simulasi penggunaan pertama kali.',
                    'is_active' => true,
                ]
            ));
        }

        return $customers;
    }

    private function seedBookings($customers, $assets)
    {
        $adminId = User::where('email', 'admin@smartrental.local')->value('id');
        $ownerId = User::where('email', 'pemilik@smartrental.local')->value('id');
        $statuses = ['completed', 'approved', 'pending', 'active', 'overdue'];
        $bookings = collect();

        foreach (range(1, 100) as $sequence) {
            $customer = $customers[$sequence - 1];
            $asset = $assets[$sequence - 1];
            $pickup = Carbon::parse('2026-03-01 09:00:00')->addDays($sequence);
            $return = (clone $pickup)->addDays(2 + ($sequence % 4))->setTime(17, 0);
            $status = $statuses[($sequence - 1) % count($statuses)];
            $rentalDays = max(1, (int) ceil($pickup->floatDiffInHours($return) / 24));
            $discount = $sequence % 6 === 0 ? 75000 : 0;
            $insurance = round((float) $asset->daily_rate * $rentalDays * 0.04);
            $delivery = $sequence % 3 === 0 ? 150000 : 0;
            $subtotal = (float) $asset->daily_rate * $rentalDays;
            $tax = round(max(0, $subtotal - $discount + $insurance + $delivery) * 0.11);
            $grandTotal = max(0, $subtotal - $discount + $insurance + $delivery + $tax);

            $booking = Booking::updateOrCreate(
                ['booking_code' => sprintf('BK-2026-%04d', $sequence)],
                [
                    'customer_id' => $customer->id,
                    'user_id' => $adminId,
                    'pickup_at' => $pickup,
                    'return_at' => $return,
                    'delivery_method' => $delivery > 0 ? 'delivery' : 'pickup',
                    'delivery_address' => $delivery > 0 ? 'Alamat event ' . $customer->city : null,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'insurance_amount' => $insurance,
                    'delivery_fee' => $delivery,
                    'tax_amount' => $tax,
                    'deposit_amount' => round($grandTotal * 0.30),
                    'grand_total' => $grandTotal,
                    'notes' => 'Pastikan aset #' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT) . ' sudah dites sebelum pengambilan.',
                    'approved_by' => in_array($status, ['approved', 'active', 'completed', 'overdue'], true) ? $ownerId : null,
                    'approved_at' => in_array($status, ['approved', 'active', 'completed', 'overdue'], true) ? (clone $pickup)->subDay() : null,
                ]
            );

            $booking->items()->delete();
            $booking->items()->create([
                'asset_id' => $asset->id,
                'daily_rate' => $asset->daily_rate,
                'quantity' => 1,
                'rental_days' => $rentalDays,
                'line_total' => $subtotal,
                'returned_at' => $status === 'completed' ? $return : null,
            ]);

            $booking->services()->delete();
            $booking->services()->create(['name' => 'Asuransi perlindungan aset', 'amount' => $insurance]);

            $asset->update([
                'availability_status' => match ($status) {
                    'active', 'overdue' => 'rented',
                    'approved', 'pending' => 'reserved',
                    default => 'available',
                },
            ]);

            $bookings->push($booking);
        }

        return $bookings;
    }

    private function seedInvoices($bookings): void
    {
        $financeId = User::where('email', 'finance@smartrental.local')->value('id');
        $statuses = ['sent', 'paid', 'partially_paid', 'overdue', 'draft'];

        foreach ($bookings as $index => $booking) {
            $sequence = $index + 1;
            $status = $statuses[$index % count($statuses)];
            $paidAmount = match ($status) {
                'paid' => (float) $booking->grand_total,
                'partially_paid' => round((float) $booking->grand_total * 0.45),
                default => 0,
            };

            $invoice = Invoice::updateOrCreate(
                ['invoice_code' => sprintf('INV-2026-%04d', $sequence)],
                [
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'issue_date' => Carbon::parse($booking->pickup_at)->subDays(2),
                    'due_date' => Carbon::parse($booking->pickup_at)->addDays(14),
                    'status' => $status,
                    'subtotal' => $booking->subtotal,
                    'discount_amount' => $booking->discount_amount,
                    'tax_amount' => $booking->tax_amount,
                    'deposit_paid' => $booking->deposit_amount,
                    'total_amount' => $booking->grand_total,
                    'paid_amount' => $paidAmount,
                    'total_due' => max(0, (float) $booking->grand_total - $paidAmount),
                    'notes' => 'Tagihan awal berurutan dari pemesanan ' . $booking->booking_code . '.',
                ]
            );

            $invoice->items()->delete();
            foreach ($booking->items()->with('asset')->get() as $item) {
                $invoice->items()->create([
                    'description' => $item->asset->name,
                    'rental_start' => $booking->pickup_at,
                    'rental_end' => $booking->return_at,
                    'quantity' => $item->quantity,
                    'rate' => $item->daily_rate,
                    'amount' => $item->line_total,
                ]);
            }

            $invoice->payments()->delete();
            if ($paidAmount > 0) {
                Payment::create([
                    'payment_code' => sprintf('PAY-2026-%04d', $sequence),
                    'invoice_id' => $invoice->id,
                    'user_id' => $financeId,
                    'payment_date' => Carbon::parse($invoice->issue_date)->addDays(3),
                    'method' => $status === 'paid' ? 'Transfer Bank' : 'Kartu Kredit',
                    'amount' => $paidAmount,
                    'reference_number' => 'REF-' . $invoice->invoice_code,
                    'notes' => 'Pembayaran seed awal.',
                ]);
            }
        }
    }

    private function refreshCustomerTotals(): void
    {
        Customer::with('bookings')->get()->each(function (Customer $customer) {
            $customer->update([
                'total_bookings' => $customer->bookings->count(),
                'lifetime_value' => $customer->bookings->sum('grand_total'),
            ]);
        });
    }

    private function seedMaintenance($assets): void
    {
        $technicianId = User::where('email', 'teknisi@smartrental.local')->value('id');
        $reporterId = User::where('email', 'admin@smartrental.local')->value('id');
        $statuses = ['new', 'in_progress', 'waiting_parts', 'completed'];
        $priorities = ['low', 'medium', 'high'];

        foreach (range(1, 100) as $sequence) {
            $asset = $assets[$sequence - 1];
            $status = $statuses[($sequence - 1) % count($statuses)];
            $request = MaintenanceRequest::updateOrCreate(
                ['work_order_code' => sprintf('WO-2026-%04d', $sequence)],
                [
                    'asset_id' => $asset->id,
                    'reported_by' => $reporterId,
                    'assigned_to' => $technicianId,
                    'issue_title' => ['Pembersihan Sensor', 'Inspeksi Konektor', 'Kalibrasi Performa', 'Penggantian Suku Cadang'][($sequence - 1) % 4],
                    'issue_type' => ['Sensor', 'Konektor', 'Kalibrasi', 'Suku Cadang'][($sequence - 1) % 4],
                    'issue_description' => 'Work order awal #' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT) . ' untuk menjaga kondisi aset tetap siap rental.',
                    'priority' => $priorities[($sequence - 1) % count($priorities)],
                    'status' => $status,
                    'progress' => $status === 'completed' ? 100 : (($sequence * 13) % 75),
                    'scheduled_at' => Carbon::parse('2026-06-01 10:00:00')->addDays($sequence),
                    'completed_at' => $status === 'completed' ? Carbon::parse('2026-06-01 10:00:00')->addDays($sequence)->addHours(3) : null,
                    'estimated_cost' => 75000 + ($sequence * 15000),
                    'actual_cost' => $status === 'completed' ? 75000 + ($sequence * 12000) : 0,
                ]
            );

            $request->checklists()->delete();
            foreach (['Inspeksi visual', 'Tes fungsi utama', 'Bersihkan unit', 'Catat hasil servis'] as $index => $label) {
                $request->checklists()->create([
                    'label' => $label,
                    'is_checked' => $status === 'completed' || $index < 2,
                    'type' => 'inspection',
                ]);
            }
        }
    }

    private function seedInventory($locations): void
    {
        $templates = [
            ['Baterai NP-FZ100', 'Baterai', 'pcs', 650000],
            ['Kabel HDMI 25ft', 'Kabel', 'pcs', 180000],
            ['Gaffer Tape Hitam', 'Barang Habis Pakai', 'roll', 85000],
            ['Sensor Cleaning Swab Kit', 'Suku Cadang', 'paket', 120000],
            ['SD Card 128GB', 'Media', 'pcs', 260000],
            ['CFexpress Type B 256GB', 'Media', 'pcs', 1850000],
            ['Baterai V-Mount 99Wh', 'Baterai', 'pcs', 2100000],
            ['Clamp C-Stand Heavy Duty', 'Grip', 'pcs', 220000],
            ['Lighting Gel Pack CTO/CTB', 'Barang Habis Pakai', 'paket', 145000],
            ['Kabel XLR 10m', 'Kabel', 'pcs', 135000],
        ];
        $locationList = $locations->values();
        $userId = User::where('email', 'gudang@smartrental.local')->value('id');

        foreach (range(1, 100) as $sequence) {
            $template = $templates[($sequence - 1) % count($templates)];
            $stock = 5 + (($sequence * 7) % 38);
            $minimum = 6 + ($sequence % 12);
            $item = InventoryItem::updateOrCreate(
                ['sku' => sprintf('INV-%04d', $sequence)],
                [
                    'name' => $template[0] . ' #' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                    'category' => $template[1],
                    'location_id' => $locationList[($sequence - 1) % $locationList->count()]->id,
                    'stock' => $stock,
                    'minimum_stock' => $minimum,
                    'unit' => $template[2],
                    'unit_cost' => $template[3],
                    'is_active' => true,
                ]
            );

            $item->movements()->delete();
            $item->movements()->create([
                'user_id' => $userId,
                'type' => 'masuk',
                'quantity' => $stock,
                'reference_number' => sprintf('STOK-AWAL-%04d', $sequence),
                'notes' => 'Stok awal berurutan.',
            ]);
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['company', 'name', 'Smart Rental Pro', 'string'],
            ['company', 'legal_name', 'Smart Rental Pro Indonesia', 'string'],
            ['company', 'email', 'info@smartrental.local', 'string'],
            ['company', 'phone', '+62 21 555 0123', 'string'],
            ['company', 'address', 'Jl. Kemang Raya No. 21, Jakarta Selatan', 'string'],
            ['rental', 'minimum_days', '1', 'integer'],
            ['rental', 'deposit_rate', '0.30', 'decimal'],
            ['finance', 'tax_rate', '0.11', 'decimal'],
            ['finance', 'currency', 'IDR', 'string'],
            ['appearance', 'theme', 'light', 'string'],
        ];

        foreach ($settings as [$group, $key, $value, $type]) {
            Setting::updateOrCreate(['group' => $group, 'key' => $key], ['value' => $value, 'type' => $type]);
        }
    }

    private function assetTemplates(): array
    {
        return [
            ['code' => 'CAM', 'name' => 'Sony FX6 Cinema Camera', 'category' => 'Kamera', 'brand' => 'Sony', 'rate' => 1500000, 'deposit' => 2000000, 'image' => 'camera-sony-fx6.png'],
            ['code' => 'CAM', 'name' => 'Canon EOS R5 C Cinema Kit', 'category' => 'Kamera', 'brand' => 'Canon', 'rate' => 1200000, 'deposit' => 1700000, 'image' => 'camera-canon-r5c.png'],
            ['code' => 'LEN', 'name' => 'Canon RF 24-70mm f/2.8L II', 'category' => 'Lensa', 'brand' => 'Canon', 'rate' => 450000, 'deposit' => 750000, 'image' => 'lens-canon-24-70.png'],
            ['code' => 'DRN', 'name' => 'DJI Mavic 3 Pro', 'category' => 'Drone', 'brand' => 'DJI', 'rate' => 900000, 'deposit' => 1500000, 'image' => 'drone-dji-mavic-3.png'],
            ['code' => 'MIC', 'name' => 'Sennheiser AVX-ME2 Set', 'category' => 'Audio', 'brand' => 'Sennheiser', 'rate' => 250000, 'deposit' => 350000, 'image' => 'microphone-sennheiser.png'],
            ['code' => 'SPK', 'name' => 'JBL EON712 Speaker', 'category' => 'Speaker', 'brand' => 'JBL', 'rate' => 350000, 'deposit' => 600000, 'image' => 'speaker-jbl-eon712.png'],
            ['code' => 'LGT', 'name' => 'Aputure 300d II Light Kit', 'category' => 'Lighting', 'brand' => 'Aputure', 'rate' => 650000, 'deposit' => 900000, 'image' => 'light-aputure-300d.png'],
            ['code' => 'TRP', 'name' => 'Manfrotto 504X Tripod', 'category' => 'Tripod', 'brand' => 'Manfrotto', 'rate' => 180000, 'deposit' => 300000, 'image' => 'tripod-manfrotto-504x.png'],
            ['code' => 'MIX', 'name' => 'Yamaha MG10XU Mixer', 'category' => 'Mixer', 'brand' => 'Yamaha', 'rate' => 300000, 'deposit' => 500000, 'image' => 'mixer-yamaha-mg10xu.png'],
            ['code' => 'MON', 'name' => 'Atomos Ninja V Monitor Recorder', 'category' => 'Monitor', 'brand' => 'Atomos', 'rate' => 240000, 'deposit' => 400000, 'image' => 'monitor-atomos-ninja-v.png'],
        ];
    }

    private function specsFor(string $category, string $name): array
    {
        return match ($category) {
            'Kamera' => [['Sensor', 'Full-frame CMOS'], ['Resolusi', '4K Cinema'], ['Recording', '10-bit 4:2:2'], ['Mount', str_contains($name, 'Canon') ? 'RF Mount' : 'E-mount']],
            'Lensa' => [['Focal Length', '24-70mm'], ['Aperture', 'f/2.8'], ['Mount', 'RF Mount'], ['Stabilizer', 'Optical IS']],
            'Drone' => [['Video', '5.1K'], ['Flight Time', '43 menit'], ['Sensor', '4/3 CMOS'], ['Weight', '958g']],
            'Audio' => [['Frekuensi', '1.9GHz'], ['Jangkauan', '100m'], ['Kapsul', 'Lavalier ME2'], ['Baterai', 'Isi ulang']],
            'Speaker' => [['Power', '1300W'], ['Woofer', '12 inch'], ['Input', 'XLR/TRS'], ['DSP', 'Built-in']],
            'Lighting' => [['Output', '300W'], ['Color', '5600K'], ['Control', 'DMX/App'], ['Mount', 'Bowens']],
            'Tripod' => [['Payload', '12kg'], ['Head', 'Fluid video'], ['Height', '173cm'], ['Plate', 'Quick release']],
            'Mixer' => [['Channel', '10 channel'], ['FX', 'SPX'], ['USB', '2-in/2-out'], ['Power', 'Adaptor']],
            'Monitor' => [['Ukuran', '5 inch'], ['Input', 'HDMI'], ['Recording', 'ProRes'], ['Power', 'NP-F/V-Mount']],
            default => [['Kondisi', 'Siap rental'], ['Paket', 'Lengkap'], ['Garansi', 'Internal'], ['Catatan', 'Dicek berkala']],
        };
    }
}
