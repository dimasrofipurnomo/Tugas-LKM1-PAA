## Informasi Mahasiswa
Nama     : Dimas Rofi' Purnomo 
NIM      :242410102062     
Kelas    :PAA (A)          


## Sewa iPhone API

REST API untuk sistem manajemen **Sewa iPhone** berbasis Laravel. Mencakup pengelolaan pelanggan, unit iPhone, transaksi sewa, dan pembayaran.

---

## Deskripsi Project

Sistem ini memudahkan pengelolaan bisnis sewa iPhone secara digital. Pelanggan bisa menyewa unit iPhone berdasarkan ketersediaan, dengan pencatatan durasi sewa, harga otomatis, dan manajemen pembayaran.

**Domain:** Sistem Sewa iPhone  
**Relasi antar tabel:** `customers` → `rentals` ← `iphones`, `rentals` → `payments`

---

## Teknologi yang Digunakan

| Komponen    | Teknologi     |
|-------------|---------------|
| Bahasa      | PHP 8.2       |
| Framework   | Laravel 13    |
| Database    | MySQL 8.0     |
| API Testing | Postman       |

---

## Struktur Folder

```
sewa-iphone/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── CustomerController.php # CRUD pelanggan
│   │       ├── IphoneController.php # CRUD unit iPhone
│   │       ├── RentalController.php # CRUD transaksi sewa
│   │       └── PaymentController.php # CRUD pembayaran
│   └── Models/
│       ├── Customer.php
│       ├── Iphone.php
│       ├── Rental.php
│       └── Payment.php
├── database/
│   ├── migrations/ # File migrasi tabel
│   └── seeders/ # File seeder data awal
├── routes/
│   └── api.php # Definisi semua endpoint
├── database.sql # DDL + sample data (siap import)
├── .env.example # Template konfigurasi
└── README.md
```

---

## Langkah Instalasi & Cara Menjalankan

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL 8.0
- Laravel 13

### 1. Clone Repository

```bash
git clone https://github.com/dimasrofipurnomo/Tugas-LKM1-PAA.git
cd Tugas-LKM1-PAA/sewa-iphone
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sewa_iphone_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Import Database

```bash
# Buat database dulu
mysql -u root -p -e "CREATE DATABASE sewa_iphone_db;"

# Import file SQL
mysql -u root -p sewa_iphone_db < database.sql
```

Atau buka file `database.sql` di phpMyAdmin dan klik **Import**.

### 5. Aktifkan API Routes 

```bash
php artisan install:api
php artisan migrate
```

### 6. Jalankan Server

```bash
php artisan serve
```

API akan berjalan di: `http://localhost:8000/api`

---

## Cara Import Database

**Via phpMyAdmin (cara mudah):**
1. Buka phpMyAdmin → buat database baru bernama `sewa_iphone_db`
2. Klik database tersebut → tab **Import**
3. Pilih file `database.sql` → klik **Import**

**Via Terminal:**
```bash
mysql -u root -p sewa_iphone_db < database.sql
```

Tabel yang akan dibuat:
- `customers` — Data pelanggan (soft delete)
- `iphones` — Unit iPhone yang tersedia (soft delete)
- `rentals` — Transaksi penyewaan
- `payments` — Data pembayaran

---

## Daftar Endpoint

### Customers (Pelanggan)

| Method | URL | Keterangan |
|--------|-----|------------|
| GET    | `/api/customers` | Ambil semua pelanggan (support `?search=` & `?per_page=`) |
| GET    | `/api/customers/{id}` | Detail pelanggan + riwayat rental |
| POST   | `/api/customers` | Tambah pelanggan baru |
| PUT    | `/api/customers/{id}` | Update data pelanggan |
| DELETE | `/api/customers/{id}` | Hapus pelanggan (soft delete) |

### 📱 iPhones (Unit iPhone)

| Method | URL | Keterangan |
|--------|-----|------------|
| GET    | `/api/iphones` | Ambil semua iPhone (filter `?status=` & `?condition=`) |
| GET    | `/api/iphones/{id}` | Detail iPhone |
| POST   | `/api/iphones` | Tambah unit iPhone baru |
| PUT    | `/api/iphones/{id}` | Update data iPhone |
| DELETE | `/api/iphones/{id}` | Hapus iPhone (soft delete) |

**Status iPhone:** `tersedia` | `disewa` | `maintenance`  
**Kondisi iPhone:** `baru` | `baik` | `cukup`

### Rentals (Transaksi Sewa)

| Method | URL | Keterangan |
|--------|-----|------------|
| GET    | `/api/rentals` | Ambil semua rental (filter `?status=`) |
| GET    | `/api/rentals/{id}` | Detail rental + customer + iphone + payment |
| POST   | `/api/rentals` | Buat transaksi sewa baru |
| PATCH  | `/api/rentals/{id}/status` | Update status rental |
| DELETE | `/api/rentals/{id}` | Hapus rental (hanya yg selesai/dibatalkan) |

**Status Rental:** `aktif` → `selesai` / `dibatalkan`

### Payments (Pembayaran)

| Method | URL | Keterangan |
|--------|-----|------------|
| GET    | `/api/payments` | Ambil semua pembayaran (filter `?status=`) |
| GET    | `/api/payments/{id}` | Detail pembayaran |
| POST   | `/api/payments` | Buat data pembayaran baru |
| PATCH  | `/api/payments/{id}/confirm` | Konfirmasi pembayaran (ubah ke lunas) |
| DELETE | `/api/payments/{id}` | Hapus data pembayaran (hanya yg pending/gagal) |

**Status Payment:** `pending` | `lunas` | `gagal`  
**Metode Payment:** `cash` | `transfer` | `qris`

---

## Contoh Request & Response

### POST /api/rentals — Buat Sewa Baru

**Request Body:**
```json
{
  "customer_id": 1,
  "iphone_id": 3,
  "start_date": "2024-02-01",
  "end_date": "2024-02-04",
  "payment_method": "transfer",
  "notes": "Untuk keperluan bisnis"
}
```

**Response (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 8,
    "customer_id": 1,
    "iphone_id": 3,
    "start_date": "2024-02-01",
    "end_date": "2024-02-04",
    "duration_days": 3,
    "total_price": "750000.00",
    "status": "aktif",
    "notes": "Untuk keperluan bisnis",
    "customer": { "id": 1, "name": "Budi Santoso" },
    "iphone": { "id": 3, "model": "iPhone 15", "daily_price": "250000.00" },
    "payment": { "id": 8, "amount": "750000.00", "status": "pending" }
  }
}
```

### Response Error (404 Not Found):
```json
{
  "status": "error",
  "message": "iPhone dengan ID 99 tidak ditemukan"
}
```

### Response Error (409 Conflict):
```json
{
  "status": "error",
  "message": "iPhone iPhone 15 Pro sedang disewa, tidak bisa disewa"
}
```

---

## Keamanan

Semua query database menggunakan **Eloquent ORM** dengan **parameter binding** otomatis dari Laravel untuk mencegah SQL Injection. Validasi input dilakukan menggunakan `Validator::make()` di setiap endpoint.

---

## Video Presentasi

> Link video: **[Tambahkan link YouTube di sini setelah upload]**

---
