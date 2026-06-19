# Plan: Update Register Form with Demografi & Domain @bsfp.com

## Task 1: Register.jsx
**File:** `resources/js/Pages/Auth/Register.jsx`

Changes:
- Remove "Data Demografi" section label — all fields integrated
- Email: split input (username + @bsfp.com suffix), user only types username
- Add `required` to all fields (phone, address, umur, gender, status_pekerjaan, pendidikan_terakhir)
- Status pekerjaan: add `dosen`, `civitas_akademika`, keep existing options
- "Lainnya" → show text input for custom pekerjaan
- Dosen/Mahasiswa/Civitas → show universitas + fakultas conditionally
- Password visibility toggle (two fields, buttons always visible)
- Add `useState` for showPassword, showConfirmPassword, emailUsername

## Task 2: RegisteredUserController
**File:** `app/Http/Controllers/Auth/RegisteredUserController.php`

Changes:
- All demografi fields → required validation
- Add `pekerjaan_lainnya` to validation (nullable)
- Email: received as `username@bsfp.com` from frontend, keep existing unique check
- All validation rules updated

## Order
1. Task 1 first (Register.jsx)
2. Task 2 second (RegisteredUserController)
