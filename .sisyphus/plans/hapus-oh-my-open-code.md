# Rencana: Menghapus Oh My Open Code (opencode-ai)

**Disusun oleh:** Prometheus (Planning Consultant)
**Tanggal:** 2026-06-16
**Status:** Siap dieksekusi

---

## Ringkasan

Oh My Open Code (**opencode-ai@1.15.10**) terinstal sebagai **npm global package** di sistem Windows ini. Penghapusan mencakup 3 area: (1) paket npm global, (2) direktori konfigurasi, (3) direktori data.

---

## 1. Informasi Instalasi Saat Ini

| Komponen | Lokasi |
|----------|--------|
| **NPM Package** | `opencode-ai@1.15.10` (global) |
| **Binary/Command** | `C:\Users\Hisyam\AppData\Roaming\npm\opencode.ps1` |
| **Node Modules** | `C:\Users\Hisyam\AppData\Roaming\npm\node_modules\opencode-ai` |
| **Config Directory** | `C:\Users\Hisyam\.config\opencode\` |
| **Data Directory** | `C:\Users\Hisyam\.local\share\opencode\` |
| **Temp Directory** | `C:\Users\Hisyam\AppData\Local\Temp\opencode` |

---

## 2. Langkah Penghapusan

### Langkah 1: Hapus npm global package
```powershell
npm uninstall -g opencode-ai
```
**Verifikasi:** `Get-Command opencode -ErrorAction SilentlyContinue` harus mengembalikan kosong.

### Langkah 2: Backup (opsional) config/data jika diperlukan
Jika ada data tugas/sesi yang masih diperlukan, backup dulu:
```powershell
# Backup config jika diperlukan
Copy-Item -Recurse "$env:USERPROFILE\.config\opencode\tasks" ".\backup-opencode-tasks"
Copy-Item "$env:USERPROFILE\.local\share\opencode\opencode.db" ".\backup-opencode-db"
```

### Langkah 3: Hapus direktori konfigurasi
```powershell
Remove-Item -Recurse -Force "$env:USERPROFILE\.config\opencode"
```

### Langkah 4: Hapus direktori data
```powershell
Remove-Item -Recurse -Force "$env:USERPROFILE\.local\share\opencode"
```

### Langkah 5: Hapus direktori temp
```powershell
Remove-Item -Recurse -Force "$env:USERPROFILE\AppData\Local\Temp\opencode"
```

### Langkah 6: Hapus file-file terkait di global npm (jika tersisa)
```powershell
# Hapus shim/wrapper files
Remove-Item -Force "$env:USERPROFILE\AppData\Roaming\npm\opencode.ps1"
Remove-Item -Force "$env:USERPROFILE\AppData\Roaming\npm\opencode.cmd"
```

### Langkah 7 (Opsional): Hapus dari PATH environment
Periksa apakah PATH masih mengarah ke `%USERPROFILE%\AppData\Roaming\npm\`:
```powershell
# Cek PATH
$env:Path -split ";" | Select-String "npm"
```

> **Catatan:** Folder `C:\Users\Hisyam\AppData\Roaming\npm\` adalah folder npm global default. Jangan hapus folder ini jika Anda masih menggunakan npm untuk package global lainnya.

---

## 3. Verifikasi Penghapusan

Jalankan perintah-perintah berikut untuk memastikan Open Code sudah benar-benar hilang:

```powershell
# 1. Cek command
Get-Command opencode -ErrorAction SilentlyContinue

# 2. Cek npm global
npm list -g --depth=0 2>$null | Select-String "opencode"

# 3. Cek direktori
Test-Path "$env:USERPROFILE\.config\opencode"
Test-Path "$env:USERPROFILE\.local\share\opencode"
Test-Path "$env:USERPROFILE\AppData\Local\Temp\opencode"
```

Semua harus mengembalikan kosong atau `False`.

---

## 4. Rollback (jika ingin menginstal kembali)

```powershell
npm install -g opencode-ai
```

---

## 5. Catatan Penting

- Penghapusan ini **tidak akan memengaruhi project** `D:\project\banksampah-faperta` karena Open Code tidak menulis file ke direktori project (kecuali `.sisyphus/` yang sudah dibuat oleh sesi ini).
- Semua **history sesi, task logs, dan konfigurasi agent** akan hilang setelah penghapusan.
- Jika Anda ingin **hanya menghapus sementara**, cukup rename folder `.config\opencode` menjadi `.config\opencode-backup`.
