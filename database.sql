-- ROLES
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

INSERT INTO roles (name) VALUES
('admin'),
('user');

-- USERS
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- IPHONES
CREATE TABLE iphones (
    id SERIAL PRIMARY KEY,
    model VARCHAR(100),
    storage VARCHAR(20),
    color VARCHAR(50),
    kondisi VARCHAR(20),
    price DECIMAL(10,2),
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- RENTALS
CREATE TABLE rentals (
    id SERIAL PRIMARY KEY,
    user_id INT, 
    iphone_id INT,
    start_date DATE,
    end_date DATE,
    total_price DECIMAL(12,2),
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (iphone_id) REFERENCES iphones(id)
);

-- PAYMENTS
CREATE TABLE payments (
    rental_id INT PRIMARY KEY,
    amount DECIMAL(12,2),
    method VARCHAR(20),
    status VARCHAR(20),
    paid_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (rental_id) REFERENCES rentals(id)
);

INSERT INTO users (name, email, password, phone, address, role_id) VALUES
('Admin 1', 'admin@email.com', 'admin123', '081111111111', 'Surabaya', 1),
('Admin 2', 'admin2@email.com', 'admin123', '082222222222', 'Sidoarjo', 1),
('Budi Santoso', 'budi@email.com', 'user123', '081234567890', 'Surabaya', 2),
('Siti Rahayu', 'siti@email.com', 'user123', '082345678901', 'Surabaya', 2),
('Andi Wijaya', 'andi@email.com', 'user123', '083456789012', 'Malang', 2),
('Dewi Lestari', 'dewi@email.com', 'user123', '084567890123', 'Surabaya', 2);

INSERT INTO iphones (model, storage, color, kondisi, price, status) VALUES
('iPhone 15 Pro Max', '256GB', 'Natural Titanium', 'baru', 350000, 'tersedia'),
('iPhone 15 Pro', '128GB', 'Blue Titanium', 'baru', 300000, 'disewa'),
('iPhone 15', '128GB', 'Pink', 'baru', 250000, 'tersedia'),
('iPhone 14 Pro Max', '256GB', 'Deep Purple', 'baik', 275000, 'disewa'),
('iPhone 14 Pro', '128GB', 'Gold', 'baik', 225000, 'tersedia'),
('iPhone 13', '128GB', 'Green', 'cukup', 150000, 'tersedia');

INSERT INTO rentals (user_id, iphone_id, start_date, end_date, total_price, status) VALUES
(3, 2, '2024-01-10', '2024-01-13', 900000, 'selesai'),
(4, 4, '2024-01-15', '2024-01-17', 550000, 'aktif'),
(5, 1, '2024-01-18', '2024-01-20', 700000, 'aktif'),
(6, 5, '2024-01-05', '2024-01-08', 600000, 'selesai'),
(3, 3, '2024-01-20', '2024-01-22', 500000, 'dibatalkan'),
(4, 6, '2024-01-12', '2024-01-14', 300000, 'selesai');

INSERT INTO payments (rental_id, amount, method, status, paid_at) VALUES
(1, 900000, 'transfer', 'lunas', '2024-01-10 09:00:00'),
(2, 550000, 'qris', 'lunas', '2024-01-15 10:30:00'),
(3, 700000, 'cash', 'lunas', '2024-01-18 08:00:00'),
(4, 600000, 'transfer', 'lunas', '2024-01-05 11:00:00'),
(5, 500000, 'cash', 'gagal', NULL),
(6, 300000, 'qris', 'lunas', '2024-01-12 14:00:00');

select * from users
select * from roles
select * from iphones
select * from rentals
select * from payments
