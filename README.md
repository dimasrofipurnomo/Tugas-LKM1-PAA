# Sewa iPhone API

REST API untuk sistem manajemen Sewa iPhone berbasis Laravel. Mencakup pengelolaan user, unit iPhone, transaksi sewa, dan pembayaran.

---

## Deskripsi Project

Sistem ini digunakan untuk mengelola bisnis sewa iPhone secara digital.  
Setiap user memiliki akun untuk melakukan transaksi penyewaan iPhone.

Domain: Sistem Sewa iPhone  
Relasi antar tabel:
- users → rentals ← iphones  
- rentals → payments  
- roles → users  

---

## Teknologi yang Digunakan

| Komponen             | Teknologi  |
|----------------------|------------|
| Bahasa               | PHP 8.2    |
| Framework            | Laravel 13 |
| Database             | PostgreSQL |
| API Testing          | Postman    |
| software environment | Laragon    |

---

## Struktur Folder

```

sewa-iphone/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── UserController.php
│   │       ├── IphoneController.php
│   │       ├── RentalController.php
│   │       └── PaymentController.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── Iphone.php
│       ├── Rental.php
│       └── Payment.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── database.sql
├── .env.example
└── README.md

````

---

## Langkah Instalasi & Cara Menjalankan

### Prasyarat
- PHP >= 8.2  
- Composer  
- PostgreSQL  
- Laravel 13 
- Laragon 

---

### 1. Clone Repository
```bash
git clone https://github.com/dimasrofipurnomo/Tugas-LKM1-PAA.git
cd Tugas-LKM1-PAA/sewa-iphone
````

---

### 2. Install Dependencies

```bash
composer install
```

---

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sewa_iphone_db
DB_USERNAME=postgres
DB_PASSWORD=
```

---

### 4. Import Database

#### Via Terminal:

```bash
psql -U postgres -c "CREATE DATABASE sewa_iphone_db;"
psql -U postgres -d sewa_iphone_db -f database.sql
```

---

### 5. Jalankan Server

```bash
php artisan serve
```

API berjalan di:

```
http://localhost:8000/api
```

---

## Cara Import Database

Database berisi:

* roles (hak akses)
* users (akun + customer)
* iphones (data unit)
* rentals (transaksi)
* payments (pembayaran)

Gunakan:

```bash
psql -U postgres -d sewa_iphone_db -f database.sql
```

---

## Daftar Endpoint

### Users

| Method | URL             | Keterangan       |
| ------ | --------------- | ---------------- |
| GET    | /api/users      | Ambil semua user |
| GET    | /api/users/{id} | Detail user      |
| POST   | /api/users      | Tambah user      |
| PUT    | /api/users/{id} | Update user      |
| DELETE | /api/users/{id} | Soft delete user |

---

### iPhones

| Method | URL               | Keterangan         |
| ------ | ----------------- | ------------------ |
| GET    | /api/iphones      | Ambil semua iPhone |
| GET    | /api/iphones/{id} | Detail iPhone      |
| POST   | /api/iphones      | Tambah iPhone      |
| PUT    | /api/iphones/{id} | Update iPhone      |
| DELETE | /api/iphones/{id} | Soft delete        |

---

### Rentals

| Method | URL                      | Keterangan         |
| ------ | ------------------------ | ------------------ |
| GET    | /api/rentals             | Ambil semua rental |
| GET    | /api/rentals/{id}        | Detail rental      |
| POST   | /api/rentals             | Buat transaksi     |
| PATCH  | /api/rentals/{id}/status | Update status      |
| DELETE | /api/rentals/{id}        | Hapus data         |

---

### Payments

| Method | URL                        | Keterangan             |
| ------ | -------------------------- | ---------------------- |
| GET    | /api/payments              | Ambil semua pembayaran |
| GET    | /api/payments/{id}         | Detail pembayaran      |
| POST   | /api/payments              | Tambah pembayaran      |
| PATCH  | /api/payments/{id}/confirm | Konfirmasi pembayaran  |
| DELETE | /api/payments/{id}         | Hapus data             |

---

## Contoh Request & Response

### POST /api/iphones

#### Request:

```json
{
  "model": "iPhone 15",
  "storage": "128GB",
  "color": "Black",
  "kondisi": "baru",
  "price": 300000
}
```

---

#### Response:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "model": "iPhone 15",
    "storage": "128GB",
    "price": "300000.00"
  }
}
```

---

## Keamanan

* Menggunakan Eloquent ORM (mencegah SQL Injection)
* Validasi input di setiap endpoint
* Soft delete menggunakan `deleted_at`

---

## Video Presentasi

Link video:

```
https://youtu.be/Iy0XK5HGKHo
```

---