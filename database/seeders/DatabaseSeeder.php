<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TrashPrice;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\Withdrawal;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin Bank Sampah',
            'email' => 'admin@bsfpunmul.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'verified',
            'phone' => '08123456789',
            'address' => 'Kantor Pusat Bank Sampah Digital, Bogor',
            'saldo' => 0,
            'account_no' => 'BS-00001',
        ]);

        $petugas = User::create([
            'name' => 'Petugas Mamat',
            'email' => 'petugas@bsfpunmul.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'status' => 'verified',
            'phone' => '08122334455',
            'address' => 'Gudang Penampungan Bogor Tengah',
            'saldo' => 0,
            'account_no' => 'BS-00002',
        ]);

        $nasabah = User::create([
            'name' => 'Budi Raharjo',
            'email' => 'nasabah@bsfpunmul.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'status' => 'verified',
            'phone' => '08987654321',
            'address' => 'Jln. Faperta No. 12, Kota Bogor',
            'saldo' => 450000,
            'account_no' => 'BS-10003',
        ]);

        // Extra pending nasabah for testing verification
        User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@bsfpunmul.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'status' => 'pending',
            'phone' => '087711223344',
            'address' => 'Perumahan Pakuan Indah Blok C2, Bogor',
            'saldo' => 0,
            'account_no' => 'BS-10004',
        ]);

        // 2. Seed Trash Prices
        $t1 = TrashPrice::create([
            'name' => 'Plastik PET (Botol Bening)',
            'category' => 'plastik',
            'category_type' => 'umum',
            'price_buy' => 4000,
            'price_sell' => 6000,
            'unit' => 'kg',
            'carbon_factor' => 2.15,
        ]);

        $t2 = TrashPrice::create([
            'name' => 'Plastik Campuran (Berwarna)',
            'category' => 'plastik',
            'category_type' => 'umum',
            'price_buy' => 2000,
            'price_sell' => 3500,
            'unit' => 'kg',
            'carbon_factor' => 1.50,
        ]);

        $t3 = TrashPrice::create([
            'name' => 'Kardus Bekas',
            'category' => 'kertas',
            'category_type' => 'umum',
            'price_buy' => 3000,
            'price_sell' => 4500,
            'unit' => 'kg',
            'carbon_factor' => 0.67,
        ]);

        $t4 = TrashPrice::create([
            'name' => 'Kertas HVS/Dokumen',
            'category' => 'kertas',
            'category_type' => 'umum',
            'price_buy' => 2500,
            'price_sell' => 4000,
            'unit' => 'kg',
            'carbon_factor' => 0.94,
        ]);

        $t5 = TrashPrice::create([
            'name' => 'Minyak Jelantah',
            'category' => 'minyak_jelantah',
            'category_type' => 'umum',
            'price_buy' => 7500,
            'price_sell' => 11000,
            'unit' => 'L',
            'carbon_factor' => 1.50,
        ]);

        $t6 = TrashPrice::create([
            'name' => 'Besi Tua / Logam Campur',
            'category' => 'logam',
            'category_type' => 'umum',
            'price_buy' => 5000,
            'price_sell' => 8000,
            'unit' => 'kg',
            'carbon_factor' => 1.40,
        ]);

        $t7 = TrashPrice::create([
            'name' => 'Botol Kaca Bening',
            'category' => 'kaca',
            'category_type' => 'umum',
            'price_buy' => 1000,
            'price_sell' => 2000,
            'unit' => 'kg',
            'carbon_factor' => 0.31,
        ]);

        // 3. Seed Articles
        Article::create([
            'title' => 'Panduan Memilah Sampah Rumah Tangga dengan Benar',
            'slug' => 'panduan-memilah-sampah-rumah-tangga-dengan-benar',
            'content' => 'Memilah sampah dari rumah adalah langkah awal yang sangat penting untuk menyelamatkan lingkungan. Kita bisa mulai dengan memisahkan sampah menjadi tiga kategori utama: sampah organik (sisa makanan, daun kering), sampah anorganik yang dapat didaur ulang (plastik, kertas, logam, kaca), dan sampah residu (popok sekali pakai, tissue bekas). Dengan memisahkan sampah anorganik yang bersih, kita dapat menyetorkannya ke Bank Sampah Digital terdekat untuk diubah menjadi pundi-pundi tabungan yang bermanfaat. Pastikan botol plastik dibilas terlebih dahulu sebelum disetorkan agar nilai jualnya optimal.',
            'image_path' => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&q=80&w=800',
            'status' => 'published',
        ]);

        Article::create([
            'title' => 'Manfaat Menabung Minyak Jelantah di Bank Sampah',
            'slug' => 'manfaat-menabung-minyak-jelantah-di-bank-sampah',
            'content' => 'Jangan buang minyak goreng bekas (jelantah) Anda ke wastafel! Membuang minyak jelantah langsung ke saluran air dapat menyebabkan penyumbatan pipa dan mencemari sumber air tanah serta ekosistem perairan. Sebagai solusinya, Anda dapat mengumpulkan minyak jelantah tersebut ke dalam wadah tertutup seperti botol bekas, lalu menyetorkannya ke Bank Sampah Digital. Minyak jelantah yang terkumpul akan diolah menjadi biodiesel sebagai bahan bakar ramah lingkungan. Selain menjaga kelestarian lingkungan, menyetor minyak jelantah juga memberikan nilai ekonomi yang cukup tinggi per liternya dibanding jenis sampah lainnya.',
            'image_path' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&q=80&w=800',
            'status' => 'published',
        ]);

        Article::create([
            'title' => 'Cara Membuat Eco-Enzyme Praktis di Rumah',
            'slug' => 'cara-membuat-eco-enzyme-praktis-di-rumah',
            'content' => 'Eco-enzyme adalah cairan serbaguna yang dihasilkan dari fermentasi sampah organik basah (sisa kulit buah dan sayuran), gula (gula merah atau molase), dan air. Cairan ini sangat bermanfaat sebagai pembersih lantai alami, disinfektan, hingga pupuk organik cair untuk tanaman. Perbandingannya adalah 1 bagian gula, 3 bagian sampah kulit buah/sayur, dan 10 bagian air. Campurkan seluruh bahan ke dalam botol plastik, lalu fermentasikan selama 3 bulan. Pastikan membuka tutup botol secara berkala di awal minggu pertama untuk membuang gas hasil fermentasi.',
            'image_path' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800',
            'status' => 'published',
        ]);

        // 4. Seed Deposits and Items for Nasabah
        // Deposit 1 (Approved)
        $dep1 = Deposit::create([
            'user_id' => $nasabah->id,
            'total_price' => 40000,
            'weight_total' => 6.50,
            'status' => 'approved',
            'donation_category' => 'umum',
            'notes' => 'Setoran diserahkan langsung di loket utama.',
            'validated_by' => $petugas->id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        DepositItem::create([
            'deposit_id' => $dep1->id,
            'trash_price_id' => $t1->id, // Plastik PET
            'weight' => 2.50,
            'price_per_unit' => $t1->price_buy,
            'total_price' => 10000,
            'total_carbon' => 2.50 * 2.15,
            'created_at' => now()->subDays(5),
        ]);

        DepositItem::create([
            'deposit_id' => $dep1->id,
            'trash_price_id' => $t3->id, // Kardus Bekas
            'weight' => 10.00,
            'price_per_unit' => $t3->price_buy,
            'total_price' => 30000,
            'total_carbon' => 10.00 * 0.67,
            'created_at' => now()->subDays(5),
        ]);

        // Deposit 2 (Approved)
        $dep2 = Deposit::create([
            'user_id' => $nasabah->id,
            'total_price' => 15000,
            'weight_total' => 2.00,
            'status' => 'approved',
            'donation_category' => 'umum',
            'notes' => 'Minyak jelantah kualitas baik.',
            'validated_by' => $petugas->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        DepositItem::create([
            'deposit_id' => $dep2->id,
            'trash_price_id' => $t5->id, // Minyak Jelantah
            'weight' => 2.00,
            'price_per_unit' => $t5->price_buy,
            'total_price' => 15000,
            'total_carbon' => 2.00 * 1.50,
            'created_at' => now()->subDays(2),
        ]);

        // Deposit 3 (Pending) - To test Admin validation
        $dep3 = Deposit::create([
            'user_id' => $nasabah->id,
            'total_price' => 21000, // Estimated
            'weight_total' => 5.00, // Estimated
            'status' => 'pending',
            'donation_category' => 'umum',
            'notes' => 'Pengajuan setoran plastik botol.',
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);

        DepositItem::create([
            'deposit_id' => $dep3->id,
            'trash_price_id' => $t1->id,
            'weight' => 3.00,
            'price_per_unit' => $t1->price_buy,
            'total_price' => 12000,
            'total_carbon' => 3.00 * 2.15,
            'created_at' => now()->subHours(5),
        ]);

        DepositItem::create([
            'deposit_id' => $dep3->id,
            'trash_price_id' => $t3->id,
            'weight' => 3.00,
            'price_per_unit' => $t3->price_buy,
            'total_price' => 9000,
            'total_carbon' => 3.00 * 0.67,
            'created_at' => now()->subHours(5),
        ]);

        // 5. Seed Withdrawals
        // Withdrawal 1 (Approved)
        Withdrawal::create([
            'user_id' => $nasabah->id,
            'amount' => 100000,
            'withdrawal_method' => 'transfer_bank',
            'admin_fee' => 2500,
            'bank_name' => 'Bank Mandiri',
            'bank_type' => 'mandiri',
            'account_number' => '1330099887766',
            'account_name' => 'Budi Raharjo',
            'status' => 'approved',
            'notes' => 'Pencairan saldo disetujui, bukti transfer terlampir.',
            'validated_by' => $admin->id,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        // Withdrawal 2 (Pending) - To test Admin validation
        Withdrawal::create([
            'user_id' => $nasabah->id,
            'amount' => 50000,
            'withdrawal_method' => 'tunai',
            'admin_fee' => 0,
            'bank_name' => 'GOPAY',
            'bank_type' => 'lainnya',
            'account_number' => '08987654321',
            'account_name' => 'Budi Raharjo',
            'status' => 'pending',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);
    }
}
