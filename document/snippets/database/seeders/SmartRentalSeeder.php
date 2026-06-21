<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetBrand;
use App\Models\AssetCategory;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SmartRentalSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view', 'assets.view', 'assets.create', 'assets.update', 'assets.delete',
            'bookings.view', 'bookings.create', 'bookings.update', 'bookings.cancel', 'bookings.approve',
            'customers.view', 'customers.manage', 'invoices.view', 'invoices.manage', 'payments.manage',
            'maintenance.view', 'maintenance.manage', 'calendar.view', 'reports.view', 'settings.manage',
            'users.manage', 'activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], [
                'group_name' => Str::before($permission, '.'),
                'description' => 'Permission ' . $permission,
            ]);
        }

        foreach (['pemilik'=>'Pemilik','admin_operasional'=>'Admin Operasional','staff_gudang'=>'Staff Gudang','teknisi'=>'Teknisi','finance'=>'Finance'] as $name => $displayName) {
            Role::firstOrCreate(['name' => $name], ['display_name' => $displayName]);
        }

        $users = [
            ['Pemilik Sistem', 'pemilik@smartrental.local', 'pemilik'],
            ['Admin Operasional', 'admin@smartrental.local', 'admin_operasional'],
            ['Staff Gudang', 'gudang@smartrental.local', 'staff_gudang'],
            ['Teknisi', 'teknisi@smartrental.local', 'teknisi'],
            ['Finance', 'finance@smartrental.local', 'finance'],
        ];

        foreach ($users as [$name, $email, $roleName]) {
            $user = User::firstOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $roleId = Role::where('name', $roleName)->value('id');
            $user->roles()->syncWithoutDetaching([$roleId]);
        }

        $location = Location::firstOrCreate(['code' => 'GDG-UTM'], ['name' => 'Gudang Utama', 'city' => 'Jakarta']);

        foreach (['Kamera','Lensa','Drone','Sound System','Lighting','Tripod','Aksesori'] as $category) {
            AssetCategory::firstOrCreate(['slug' => Str::slug($category)], ['name' => $category, 'is_active' => true]);
        }

        foreach (['Sony','Canon','DJI','Yamaha','JBL','Aputure','Rode','Manfrotto'] as $brand) {
            AssetBrand::firstOrCreate(['name' => $brand], ['is_active' => true]);
        }

        Asset::firstOrCreate(['asset_code' => 'AST-CAM-0001'], [
            'category_id' => AssetCategory::where('name', 'Kamera')->value('id'),
            'brand_id' => AssetBrand::where('name', 'Sony')->value('id'),
            'location_id' => $location->id,
            'name' => 'Sony FX6 Cinema Camera',
            'serial_number' => 'FX6-001',
            'daily_rate' => 1500000,
            'deposit_amount' => 2000000,
            'replacement_value' => 85000000,
            'condition_status' => 'excellent',
            'availability_status' => 'available',
            'shelf_position' => 'Rak Kamera A1',
        ]);

        Customer::firstOrCreate(['customer_code' => 'CUST-0001'], [
            'type' => 'company',
            'name' => 'BrightFrame Productions',
            'contact_person' => 'Marcus Chen',
            'email' => 'marcus@brightframe.test',
            'phone' => '081234567890',
            'address' => 'Jakarta',
            'verification_status' => 'verified',
            'customer_level' => 'vip',
        ]);
    }
}
