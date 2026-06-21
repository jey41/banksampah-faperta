# Standar Operasional Prosedur (SOP) — Skema Penjemputan Sampah
## Bank Sampah Faperta (BSFP UNMUL)

---

### 1. Ketentuan Umum & Waktu Pelayanan
1. **Jam Operasional Penjemputan:** Pelayanan penjemputan sampah dibatasi pada hari kerja operasional mulai pukul **08:00 s.d. 16:00 WITA**.
2. **Ketentuan Waktu Pengajuan (SLA):** Nasabah wajib mengajukan permohonan penjemputan sampah minimal **H-2 (2 hari operasional)** sebelum jadwal pengambilan yang diinginkan.
3. **Kategori Penjemputan:** Sampah yang dijemput dapat berupa kategori **Sampah Umum (Tabungan)** maupun **Sampah Donasi (Sedekah)**.

---

### 2. Alur Pengajuan oleh Nasabah (Nasabah Flow)
1. **Akses Fitur:** Nasabah masuk ke aplikasi web, membuka menu **Minta Jemput Sampah**.
2. **Penentuan Lokasi Penjemputan:**
   - Nasabah menandai titik koordinat penjemputan pada peta interaktif (**OpenFreeMap / MapLibre GL**).
   - Sistem akan secara otomatis menghitung jarak rute jalan raya dari Kantor Pusat Bank Sampah Faperta ke titik nasabah menggunakan **OSRM API** (dengan metode Haversine sebagai *fallback* apabila API terganggu).
3. **Pengisian Formulir:** Nasabah melengkapi estimasi berat sampah, deskripsi sampah, dan jadwal waktu pengambilan (minimal H-2).
4. **Submit Request:** Nasabah menekan tombol "Kirim Permintaan". Status permintaan awal diatur menjadi `pending`.

---

### 3. Alur Verifikasi & Penugasan (Admin & Petugas Flow)
1. **Verifikasi Permintaan:** Admin memeriksa daftar pengajuan penjemputan masuk di Dashboard Admin Filament.
2. **Persetujuan & Penugasan:**
   - Admin melakukan verifikasi kesesuaian lokasi dan estimasi volume sampah.
   - Admin menugaskan Petugas Lapangan untuk melakukan penjemputan pada tanggal yang telah disepakati.
   - Status permohonan berubah menjadi `approved` (Disetujui untuk dijemput).

---

### 4. Alur Penjemputan & Penimbangan Fisik (Field Operations)
1. **Perjalanan Penjemputan:** Petugas menuju ke lokasi nasabah dengan panduan rute jalan yang telah diverifikasi pada sistem.
2. **Pemeriksaan & Pemilihan Kategori:**
   - Petugas melakukan inspeksi fisik terhadap sampah yang akan disetorkan.
   - Sampah harus dipilah sesuai dengan kategori yang diajukan:
     - **Sampah Umum (Tabungan):** Hasil penimbangan dikonversi menjadi saldo rupiah yang masuk ke rekening tabungan nasabah.
     - **Sampah Donasi (Sedekah):** Hasil penimbangan dicatat sebagai volume donasi masuk dan disalurkan sebagai program sosial (tidak menambah saldo pribadi nasabah).
3. **Penimbangan Riil:** Petugas melakukan penimbangan berat bersih di tempat menggunakan timbangan digital/manual yang sah.

---

### 5. Pencatatan Transaksi & Fluktuasi Harga (Data Entry & Financials)
1. **Input Hasil Timbang:** Petugas menginput berat riil setiap kategori sampah ke dalam formulir setoran di Filament Admin.
2. **Kalkulasi & Snapshot Harga:**
   - Sistem secara otomatis menghitung total harga berdasarkan berat riil dikalikan dengan **harga beli yang berlaku pada hari H penimbangan** (`price_per_unit`).
   - Harga beli tersebut langsung di-*snapshot* (disimpan permanen) ke dalam tabel detail setoran (`deposit_items`), sehingga perubahan tarif harga sampah di masa depan tidak akan memengaruhi data nilai transaksi historis ini.
3. **Pemberian Saldo (Bagi Sampah Umum):** Setelah disubmit oleh petugas/admin, saldo nasabah akan otomatis bertambah sebesar total nilai setoran sampah umum tersebut.

---

### 6. Kontrol Kualitas & Mitigasi Risiko
- **Kondisi Cuaca Buruk/Kendala Teknis:** Jika terjadi kendala darurat (hujan lebat, kemacetan parah, atau armada rusak), petugas wajib menghubungi nasabah maksimal **2 jam sebelum jadwal** untuk mengatur ulang waktu penjemputan.
- **Ketidaksesuaian Jenis Sampah:** Jika sampah yang disetorkan ternyata tercampur dengan sampah basah/organik busuk, petugas berhak meminta nasabah memilah ulang terlebih dahulu atau menolak penjemputan demi menjaga kebersihan armada penampungan.
