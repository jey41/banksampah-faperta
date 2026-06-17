# Bank Sampah Faperta ♻️

Sistem Informasi Manajemen Bank Sampah untuk Fakultas Pertanian (Faperta). Aplikasi ini dibangun untuk mempermudah proses pengelolaan sampah, pencatatan transaksi nasabah, dan edukasi lingkungan melalui platform digital yang modern dan responsif.

## 🚀 Fitur Utama

- **Dashboard Nasabah**: Ringkasan saldo, riwayat transaksi, dan metrik tabungan sampah.
- **Transaksi Digital**: Pencatatan setor sampah (deposit) dan tarik saldo (withdraw) yang transparan.
- **Katalog Harga Sampah**: Informasi harga tukar sampah yang selalu ter-update.
- **Portal Artikel Edukasi**: Kumpulan artikel untuk mengedukasi nasabah terkait lingkungan dan pengelolaan sampah.
- **Panel Admin Terintegrasi**: Manajemen pengguna, validasi transaksi, dan laporan komprehensif menggunakan Filament Admin.

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dikembangkan menggunakan *stack* teknologi modern:
- **Backend:** [Laravel 11](https://laravel.com/)
- **Frontend (Nasabah/Public):** [React.js](https://reactjs.org/) + [Inertia.js](https://inertiajs.com/)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Admin Panel:** [Filament PHP](https://filamentphp.com/)
- **Database:** MySQL / PostgreSQL

## ⚙️ Cara Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer Anda:

1. **Clone Repository**
   ```bash
   git clone https://github.com/jey41/banksampah-faperta.git
   cd banksampah-faperta
   ```

2. **Install Dependensi PHP (Composer)**
   ```bash
   composer install
   ```

3. **Install Dependensi Node.js (NPM)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Copy file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Migrasi Database & Seeding**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan Aplikasi**
   Buka 2 terminal terpisah dan jalankan perintah berikut:
   
   Terminal 1 (Menjalankan server Laravel):
   ```bash
   php artisan serve
   ```
   
   Terminal 2 (Menjalankan Vite untuk React):
   ```bash
   npm run dev
   ```

Aplikasi sekarang dapat diakses melalui `http://localhost:8000`.

## 🤝 Kontribusi
Jika Anda ingin berkontribusi pada project ini, silakan buat *Pull Request* atau buka *Issue* untuk diskusi lebih lanjut.

---
*Dibuat dengan ❤️ untuk lingkungan yang lebih bersih.*
