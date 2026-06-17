# Rencana: Memperbaiki Konfigurasi Oh My OpenAgent (oh-my-openagent.json)

**Disusun oleh:** Prometheus (Strategic Planning Consultant)
**Tanggal:** 2026-06-16
**Status:** Siap untuk Review & Eksekusi
**Tier:** Quick (perubahan pada 1 file konfigurasi)

---

## Ringkasan Masalah

Konfigurasi `oh-my-openagent.json` menggunakan model yang tidak valid (`opencode/gpt-5-nano`), sehingga OpenCode/OhMyOpenCode tidak dapat menjalankan tugasnya. Model `opencode/` bukan provider yang valid di sistem ini.

---

## Detail Masalah

| Agent/Category | Model Saat Ini | Status |
|----------------|---------------|--------|
| oracle | google/gemini-3.1-pro-preview | ✅ Valid |
| **explore** | **opencode/gpt-5-nano** | ❌ Tidak Valid |
| **multimodal-looker** | **opencode/gpt-5-nano** | ❌ Tidak Valid |
| prometheus | google/gemini-3.1-pro-preview | ✅ Valid |
| **metis** | **opencode/gpt-5-nano** | ❌ Tidak Valid |
| momus | google/gemini-3.1-pro-preview | ✅ Valid |
| **atlas** | **opencode/gpt-5-nano** | ❌ Tidak Valid |
| **sisyphus-junior** | **opencode/gpt-5-nano** | ❌ Tidak Valid |
| visual-engineering | google/gemini-3.1-pro-preview | ✅ Valid |
| ultrabrain | google/gemini-3.1-pro-preview | ✅ Valid |
| deep | google/gemini-3.1-pro-preview | ✅ Valid |
| artistry | google/gemini-3.1-pro-preview | ✅ Valid |
| **quick** | **google/gemini-3-flash-preview** | ⚠️ Valid tapi kurang optimal |
| **unspecified-low** | **google/gemini-3-flash-preview** | ⚠️ Valid tapi kurang optimal |
| **unspecified-high** | **google/gemini-3-flash-preview** | ⚠️ Valid tapi kurang optimal |
| **writing** | **google/gemini-3-flash-preview** | ⚠️ Valid tapi kurang optimal |

**Total:** 6 model tidak valid, 4 model kurang optimal

---

## Pilihan Solusi

### Solusi A (Recommended): Perbaiki Model Tidak Valid

Ganti semua model yang tidak valid dengan model yang benar.

| Model Salah | Model Benar |
|-------------|-------------|
| opencode/gpt-5-nano | openai/gpt-5.4-mini |

**Mengapa `openai/gpt-5.4-mini`?**
- Anda sudah memiliki OpenAI API key yang terkonfigurasi
- Model ini terdaftar di daftar model yang tersedia
- Hemat token untuk agent utility (explore, metis, sisyphus-junior)
- Performance cukup untuk task kategori quick

### Solusi B: Optimalisasi Model Categories

Ganti `google/gemini-3-flash-preview` pada categories dengan model yang lebih optimal:

| Category | Model Saat Ini | Model Alternatif |
|----------|---------------|------------------|
| quick | google/gemini-3-flash-preview | openai/gpt-5.4-mini |
| unspecified-low | google/gemini-3-flash-preview | openai/gpt-5.4-mini |
| unspecified-high | google/gemini-3-flash-preview | google/gemini-3.1-pro-preview |
| writing | google/gemini-3-flash-preview | google/gemini-3.1-pro-preview |

**Mengapa `openai/gpt-5.4-mini` untuk quick?**
- Lebih cepat dari gemini-3-flash-preview
- Token rate yang lebih rendah
- Tersedia lewat OpenAI yang sudah terkonfigurasi

---

## Implementasi

### Langkah 1: Backup Konfigurasi Saat Ini

```powershell
# Backup file yang akan diubah
Copy-Item "C:\Users\Hisyam\.config\opencode\oh-my-openagent.json" "C:\Users\Hisyam\.config\opencode\oh-my-openagent.json.backup"
```

### Langkah 2: Update Konfigurasi

Edit `C:\Users\Hisyam\.config\opencode\oh-my-openagent.json`:

**Bagian Agents:**
```json
"explore": {
  "model": "openai/gpt-5.4-mini"
},
"multimodal-looker": {
  "model": "openai/gpt-5.4-mini"
},
"metis": {
  "model": "openai/gpt-5.4-mini"
},
"atlas": {
  "model": "openai/gpt-5.4-mini"
},
"sisyphus-junior": {
  "model": "openai/gpt-5.4-mini"
}
```

**Bagian Categories (Opsional - untuk optimasi):**
```json
"quick": {
  "model": "openai/gpt-5.4-mini"
},
"unspecified-low": {
  "model": "openai/gpt-5.4-mini"
},
"unspecified-high": {
  "model": "google/gemini-3.1-pro-preview"
},
"writing": {
  "model": "google/gemini-3.1-pro-preview"
}
```

### Langkah 3: Validasi Konfigurasi

```powershell
# Cek apakah opencode masih bisa dijalankan
opencode --version

# Cek model yang tersedia
opencode models
```

---

## Verification Checklist

Setelah implementasi, pastikan:

- [ ] Semua model yang digunakan adalah model yang valid di daftar `opencode models`
- [ ] Tidak ada error saat menjalankan `opencode`
- [ ] Test dengan prompt sederhana untuk memastikan semua agent berfungsi
- [ ] Jalankan `bunx oh-my-openagent doctor` untuk validasi komprehensif

---

## File yang Terpengaruh

| File | Perubahan |
|------|-----------|
| `C:\Users\Hisyam\.config\opencode\oh-my-openagent.json` | Update model names |

---

## Risk Assessment

**Risiko:** Rendah
- Perubahan hanya pada konfigurasi, bukan kode
- Backup sudah disiapkan
- Model yang dipilih sudah verified di daftar model

**Rollback:**
Jika ada masalah, restore dari backup:
```powershell
Copy-Item "C:\Users\Hisyam\.config\opencode\oh-my-openagent.json.backup" "C:\Users\Hisyam\.config\opencode\oh-my-openagent.json"
```

---

## Catatan untuk Eksekusi

- Pastikan Anda sudah login ke OpenAI sebelum menggunakan model openai/*
- Jika ingin menggunakan model OpenRouter sebagai alternatif, ganti `openai/` dengan `openrouter/openai/`
- Untuk performa terbaik, gunakan model yang direkomendasikan untuk setiap kategori

---

**Estimasi Waktu:** 5-10 menit untuk eksekusi manual
**Kompleksitas:** Low (hanya edit teks pada file JSON)
