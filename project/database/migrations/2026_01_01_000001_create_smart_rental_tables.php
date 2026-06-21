<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('display_name', 120);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('group_name', 80);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 120);
            $table->string('type', 50)->default('gudang')->index();
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 80)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('brand_id')->nullable()->constrained('asset_brands')->nullOnDelete();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('name', 180);
            $table->string('serial_number', 120)->nullable()->index();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('replacement_value', 15, 2)->nullable();
            $table->string('condition_status', 30)->default('good')->index();
            $table->string('availability_status', 30)->default('available')->index();
            $table->string('shelf_position', 120)->nullable();
            $table->string('image_url')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('barcode', 100)->nullable();
            $table->unsignedInteger('utilization_rate')->default(0);
            $table->unsignedInteger('total_rented')->default(0);
            $table->date('last_maintenance_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type', 50)->default('image');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('value', 255);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_kits', function (Blueprint $table) {
            $table->id();
            $table->string('kit_code', 50)->unique();
            $table->string('name', 180);
            $table->text('description')->nullable();
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_kit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_kit_id')->constrained('asset_kits')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 50)->unique();
            $table->string('type', 30)->default('personal')->index();
            $table->string('name', 180);
            $table->string('contact_person', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30);
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('identity_type', 50)->nullable();
            $table->string('identity_number', 100)->nullable();
            $table->string('identity_file')->nullable();
            $table->string('verification_status', 30)->default('pending')->index();
            $table->string('customer_level', 30)->default('reguler')->index();
            $table->string('tag', 80)->nullable();
            $table->decimal('lifetime_value', 15, 2)->default(0);
            $table->unsignedInteger('total_bookings')->default(0);
            $table->date('customer_since')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('pickup_at')->index();
            $table->dateTime('return_at')->index();
            $table->string('delivery_method', 30)->default('pickup');
            $table->string('delivery_address')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('insurance_amount', 15, 2)->default(0);
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets');
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('rental_days', 8, 2)->default(1);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->string('condition_out', 30)->nullable();
            $table->string('condition_in', 30)->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('name', 120);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50)->unique();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status', 30)->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('deposit_paid', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('total_due', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->date('rental_start')->nullable();
            $table->date('rental_end')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code', 50)->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('payment_date');
            $table->string('method', 50);
            $table->decimal('amount', 15, 2);
            $table->string('reference_number', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_code', 50)->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('issue_title', 180);
            $table->string('issue_type', 80)->nullable();
            $table->text('issue_description');
            $table->string('priority', 30)->default('medium')->index();
            $table->string('status', 30)->default('new')->index();
            $table->unsignedInteger('progress')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained('maintenance_requests')->cascadeOnDelete();
            $table->string('label', 180);
            $table->boolean('is_checked')->default(false);
            $table->string('type', 40)->default('inspection');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 180);
            $table->string('category', 80);
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->string('unit', 30)->default('pcs');
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 30)->index();
            $table->integer('quantity');
            $table->string('reference_number', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module', 80)->index();
            $table->string('action', 120);
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 80)->index();
            $table->string('key', 120);
            $table->text('value')->nullable();
            $table->string('type', 40)->default('string');
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('maintenance_checklists');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('asset_kit_items');
        Schema::dropIfExists('asset_kits');
        Schema::dropIfExists('asset_specifications');
        Schema::dropIfExists('asset_media');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_brands');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
