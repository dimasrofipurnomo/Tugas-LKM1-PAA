<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        \App\Models\Role::create(['name' => 'admin']);
        \App\Models\Role::create(['name' => 'user']);

        // Seed users
        \App\Models\Customer::create([
            'name' => 'Admin 1',
            'email' => 'admin@email.com',
            'password' => bcrypt('admin123'),
            'phone' => '081111111111',
            'address' => 'Surabaya',
            'role_id' => 1,
        ]);

        \App\Models\Customer::create([
            'name' => 'Admin 2',
            'email' => 'admin2@email.com',
            'password' => bcrypt('admin123'),
            'phone' => '082222222222',
            'address' => 'Sidoarjo',
            'role_id' => 1,
        ]);

        \App\Models\Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('user123'),
            'phone' => '081234567890',
            'address' => 'Surabaya',
            'role_id' => 2,
        ]);

        \App\Models\Customer::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@email.com',
            'password' => bcrypt('user123'),
            'phone' => '082345678901',
            'address' => 'Surabaya',
            'role_id' => 2,
        ]);

        // Seed iphones
        \App\Models\Iphone::create([
            'model' => 'iPhone 15 Pro Max',
            'storage' => '256GB',
            'color' => 'Natural Titanium',
            'kondisi' => 'baru',
            'price' => 350000,
            'status' => 'tersedia',
        ]);

        \App\Models\Iphone::create([
            'model' => 'iPhone 15 Pro',
            'storage' => '128GB',
            'color' => 'Blue Titanium',
            'kondisi' => 'baru',
            'price' => 300000,
            'status' => 'disewa',
        ]);

        // Seed rentals
        \App\Models\Rental::create([
            'user_id' => 3,
            'iphone_id' => 2,
            'start_date' => '2024-01-10',
            'end_date' => '2024-01-13',
            'total_price' => 900000,
            'status' => 'selesai',
        ]);

        // Seed payments
        \App\Models\Payment::create([
            'rental_id' => 1,
            'amount' => 900000,
            'method' => 'transfer',
            'status' => 'lunas',
            'paid_at' => '2024-01-10 09:00:00',
        ]);
    }
}
