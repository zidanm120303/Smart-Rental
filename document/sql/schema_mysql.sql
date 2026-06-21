CREATE DATABASE IF NOT EXISTS smart_rental_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_rental_pro;

CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  display_name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  group_name VARCHAR(80) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE locations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  address TEXT NULL,
  city VARCHAR(80) NULL,
  phone VARCHAR(30) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE asset_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  icon VARCHAR(80) NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE asset_brands (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE assets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  asset_code VARCHAR(50) NOT NULL UNIQUE,
  category_id BIGINT UNSIGNED NOT NULL,
  brand_id BIGINT UNSIGNED NULL,
  location_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  serial_number VARCHAR(120) NULL,
  description TEXT NULL,
  purchase_date DATE NULL,
  purchase_price DECIMAL(15,2) NULL,
  daily_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
  deposit_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  replacement_value DECIMAL(15,2) NULL,
  condition_status VARCHAR(30) NOT NULL DEFAULT 'good',
  availability_status VARCHAR(30) NOT NULL DEFAULT 'available',
  shelf_position VARCHAR(120) NULL,
  qr_code VARCHAR(255) NULL,
  barcode VARCHAR(100) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
  INDEX idx_assets_status (availability_status),
  INDEX idx_assets_condition (condition_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE customers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_code VARCHAR(50) NOT NULL UNIQUE,
  type VARCHAR(30) NOT NULL DEFAULT 'personal',
  name VARCHAR(180) NOT NULL,
  contact_person VARCHAR(150) NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(30) NOT NULL,
  address TEXT NULL,
  city VARCHAR(80) NULL,
  identity_type VARCHAR(50) NULL,
  identity_number VARCHAR(100) NULL,
  identity_file VARCHAR(255) NULL,
  verification_status VARCHAR(30) NOT NULL DEFAULT 'pending',
  customer_level VARCHAR(30) NOT NULL DEFAULT 'reguler',
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(50) NOT NULL UNIQUE,
  customer_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  pickup_at DATETIME NOT NULL,
  return_at DATETIME NOT NULL,
  delivery_method VARCHAR(30) NOT NULL DEFAULT 'pickup',
  status VARCHAR(30) NOT NULL DEFAULT 'draft',
  subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  insurance_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  delivery_fee DECIMAL(15,2) NOT NULL DEFAULT 0,
  tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  deposit_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  grand_total DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  approved_by BIGINT UNSIGNED NULL,
  approved_at TIMESTAMP NULL,
  cancelled_reason TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
  INDEX idx_bookings_dates (pickup_at, return_at),
  INDEX idx_bookings_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE booking_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  asset_id BIGINT UNSIGNED NOT NULL,
  daily_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
  quantity INT NOT NULL DEFAULT 1,
  rental_days DECIMAL(8,2) NOT NULL DEFAULT 1,
  line_total DECIMAL(15,2) NOT NULL DEFAULT 0,
  condition_out VARCHAR(30) NULL,
  condition_in VARCHAR(30) NULL,
  returned_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_booking_items_asset (asset_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_code VARCHAR(50) NOT NULL UNIQUE,
  booking_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NOT NULL,
  issue_date DATE NOT NULL,
  due_date DATE NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'draft',
  subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  deposit_paid DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  payment_code VARCHAR(50) NOT NULL UNIQUE,
  invoice_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  payment_date DATE NOT NULL,
  method VARCHAR(50) NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  reference_number VARCHAR(120) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE maintenance_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  work_order_code VARCHAR(50) NOT NULL UNIQUE,
  asset_id BIGINT UNSIGNED NOT NULL,
  reported_by BIGINT UNSIGNED NOT NULL,
  assigned_to BIGINT UNSIGNED NULL,
  issue_title VARCHAR(180) NOT NULL,
  issue_description TEXT NOT NULL,
  priority VARCHAR(30) NOT NULL DEFAULT 'medium',
  status VARCHAR(30) NOT NULL DEFAULT 'new',
  scheduled_at DATETIME NULL,
  completed_at DATETIME NULL,
  estimated_cost DECIMAL(15,2) NOT NULL DEFAULT 0,
  actual_cost DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
