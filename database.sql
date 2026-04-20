CREATE DATABASE IF NOT EXISTS sewa_iphone_db;
USE sewa_iphone_db;

-- customers (pelanggan)
CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_customers_email ON customers(email);
CREATE INDEX idx_customers_deleted_at ON customers(deleted_at);

-- iphones (unit iPhone yang tersedia)
CREATE TABLE IF NOT EXISTS iphones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(100) NOT NULL,
    storage  VARCHAR(20) NOT NULL COMMENT '64GB, 128GB, 256GB, 512GB',
    color VARCHAR(50) NOT NULL,
    `condition` ENUM('baru','baik','cukup') NOT NULL DEFAULT 'baik',
    daily_price  DECIMAL(10,2)  NOT NULL COMMENT 'Harga sewa per hari',
    status ENUM('tersedia','disewa','maintenance') NOT NULL DEFAULT 'tersedia',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_iphones_status ON iphones(status);
CREATE INDEX idx_iphones_model ON iphones(model);
CREATE INDEX idx_iphones_deleted_at ON iphones(deleted_at);

-- rentals (sewa)
CREATE TABLE IF NOT EXISTS rentals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    iphone_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT UNSIGNED NOT NULL,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('aktif','selesai','dibatalkan') NOT NULL DEFAULT 'aktif',
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rentals_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
    CONSTRAINT fk_rentals_iphone FOREIGN KEY (iphone_id) REFERENCES iphones(id)
);

CREATE INDEX idx_rentals_customer_id ON rentals(customer_id);
CREATE INDEX idx_rentals_iphone_id ON rentals(iphone_id);
CREATE INDEX idx_rentals_status ON rentals(status);
CREATE INDEX idx_rentals_start_date ON rentals(start_date);

-- payments (pembayaran)
CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rental_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    method ENUM('cash','transfer','qris') NOT NULL DEFAULT 'cash',
    status ENUM('pending','lunas','gagal') NOT NULL DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_rental FOREIGN KEY (rental_id) REFERENCES rentals(id)
);

CREATE INDEX idx_payments_rental_id ON payments(rental_id);
CREATE INDEX idx_payments_status    ON payments(status);


-- SAMPLE DATA
-- customers
INSERT INTO customers (name, email, phone, address) VALUES
    ('Budi Santoso', 'budi@email.com', '081234567890', 'Jl. Raya Darmo No.1, Surabaya'),
    ('Siti Rahayu', 'siti@email.com', '082345678901', 'Jl. Pemuda No.10, Surabaya'),
    ('Andi Wijaya', 'andi@email.com', '083456789012', 'Jl. Ahmad Yani No.5, Malang'),
    ('Dewi Lestari', 'dewi@email.com', '084567890123', 'Jl. Diponegoro No.20, Surabaya'),
    ('Rizky Firmansyah','rizky@email.com', '085678901234', 'Jl. Mayjend Sungkono No.3, Surabaya'),
    ('Maya Putri', 'maya@email.com', '086789012345', 'Jl. Rungkut Indah No.15, Surabaya'),
    ('Fajar Nugroho', 'fajar@email.com', '087890123456', 'Jl. Semolowaru No.8, Surabaya');

-- iphones
INSERT INTO iphones (model, storage, color, `condition`, daily_price, status) VALUES
    ('iPhone 15 Pro Max', '256GB', 'Natural Titanium', 'baru', 350000, 'tersedia'),
    ('iPhone 15 Pro', '128GB', 'Blue Titanium', 'baru', 300000, 'disewa'),
    ('iPhone 15', '128GB', 'Pink', 'baru',  250000, 'tersedia'),
    ('iPhone 14 Pro Max', '256GB', 'Deep Purple', 'baik',  275000, 'disewa'),
    ('iPhone 14 Pro', '128GB', 'Gold', 'baik',  225000, 'tersedia'),
    ('iPhone 14', '128GB', 'Midnight', 'baik',  175000, 'maintenance'),
    ('iPhone 13 Pro Max', '256GB', 'Sierra Blue', 'baik',  200000, 'tersedia'),
    ('iPhone 13', '128GB', 'Green', 'cukup', 125000, 'tersedia'),
    ('iPhone 12 Pro', '128GB', 'Pacific Blue', 'cukup', 100000, 'tersedia'),
    ('iPhone 12', '64GB', 'White', 'cukup', 75000, 'tersedia');

-- rentals
INSERT INTO rentals (customer_id, iphone_id, start_date, end_date, duration_days, total_price, status, notes) VALUES
    (1, 2, '2024-01-10', '2024-01-13', 3,  900000, 'selesai', 'Untuk keperluan bisnis'),
    (2, 4, '2024-01-15', '2024-01-17', 2,  550000, 'aktif', NULL),
    (3, 1, '2024-01-18', '2024-01-20', 2,  700000, 'aktif', 'Untuk foto produk'),
    (4, 7, '2024-01-05', '2024-01-08', 3,  600000, 'selesai', NULL),
    (5, 3, '2024-01-20', '2024-01-22', 2,  500000, 'dibatalkan', 'Pelanggan membatalkan'),
    (6, 8, '2024-01-12', '2024-01-14', 2,  250000, 'selesai', NULL),
    (7, 5, '2024-01-19', '2024-01-21', 2,  450000, 'aktif', 'Keperluan presentasi');

-- payments
INSERT INTO payments (rental_id, amount, method, status, paid_at, notes) VALUES
    (1, 900000, 'transfer', 'lunas', '2024-01-10 09:00:00', 'Transfer BCA'),
    (2, 550000, 'qris', 'lunas', '2024-01-15 10:30:00', NULL),
    (3, 700000, 'cash', 'lunas', '2024-01-18 08:00:00', 'Bayar tunai di tempat'),
    (4, 600000, 'transfer', 'lunas', '2024-01-05 11:00:00', NULL),
    (5, 500000, 'cash', 'gagal', NULL, 'Dibatalkan pelanggan'),
    (6, 250000, 'qris', 'lunas', '2024-01-12 14:00:00', NULL),
    (7, 450000, 'transfer', 'pending', NULL, 'Menunggu konfirmasi');
