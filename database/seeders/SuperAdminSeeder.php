<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Or any default password
                'role' => 'super_admin',
                'status' => 'verified',
                'phone' => '081234567890',
                'address' => 'Kantor Pusat Bank Sampah',
            ]
        );
    }
}
