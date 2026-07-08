# QA TEST PLAN — BANK SAMPAH FAPERTA

Dokumen ini berisi rencana pengujian sistem Bank Sampah Faperta untuk memastikan seluruh fitur, alur bisnis, dan integrasi berjalan sesuai spesifikasi.

---

# 1. QA OBJECTIVE

Tujuan pengujian:

- memastikan semua fitur bekerja sesuai business rule
- memastikan tidak ada regression
- memastikan keamanan & integritas data
- memastikan flow transaksi konsisten
- memastikan UI/UX stabil di semua role

---

# 2. TESTING SCOPE

## In Scope

- Authentication system
- Role-based access control
- Deposit flow
- Withdrawal flow
- Pickup system
- Mutation ledger
- Gamification system
- Admin CMS
- Public website
- Dashboard analytics

## Out of Scope

- third-party infrastructure
- external API (tidak ada API eksternal saat ini)

---

# 3. TEST TYPES

## 3.1 Functional Testing

- validasi fitur sesuai requirement

## 3.2 Negative Testing

- input invalid
- unauthorized access
- edge case failure

## 3.3 Integration Testing

- service layer + controller + DB
- event + listener flow

## 3.4 Regression Testing

- memastikan perubahan tidak merusak fitur lama

## 3.5 Security Testing

- auth bypass
- IDOR testing
- injection attempt
- file upload abuse

---

# 4. TEST SCENARIOS

---

## 4.1 AUTHENTICATION TEST

### Positive

- login dengan credential valid
- register user baru
- email verification success

### Negative

- login password salah
- akses tanpa login
- akses role mismatch

### Expected Result

- session created
- role enforced
- unauthorized request blocked

---

## 4.2 DEPOSIT TEST

### Positive Flow

1. create deposit (pending)
2. input item sampah
3. approve deposit
4. saldo bertambah (jika bukan donasi)
5. mutation created

### Negative Flow

- approve deposit tanpa weight
- negative weight input
- unauthorized approval attempt

### Edge Cases

- deposit dengan 0 item
- deposit sangat besar (stress test)

---

## 4.3 WITHDRAWAL TEST

### Positive

- request withdrawal valid
- approval admin
- saldo berkurang
- mutation debit created

### Negative

- withdraw > saldo
- withdraw tanpa bank info
- approval double submit

### Edge Cases

- concurrent withdrawal request
- withdrawal near zero balance

---

## 4.4 PICKUP TEST

### Positive

- request pickup valid
- distance ≤ 2km
- assignment petugas

### Negative

- distance > 2km
- missing location
- invalid time slot

---

## 4.5 MUTATION LEDGER TEST

### Critical Test

- setiap deposit menghasilkan credit mutation
- setiap withdrawal menghasilkan debit mutation

### Validation

- balance_before benar
- balance_after benar
- tidak ada mutation ganda

---

## 4.6 GAMIFICATION TEST

### Positive

- eco points bertambah setelah deposit
- badge unlock otomatis
- level naik sesuai threshold

### Edge Cases

- multiple rapid deposits
- duplicate badge prevention

---

## 4.7 ADMIN CMS TEST

### Positive

- admin login
- approve deposit
- approve withdrawal
- manage users

### Negative

- nasabah akses /cms
- petugas akses admin-only action

---

## 4.8 PUBLIC WEBSITE TEST

### Positive

- landing page load
- artikel tampil
- harga sampah tampil

### Negative

- broken route access
- invalid slug article

---

# 5. SECURITY TESTING

## 5.1 AUTHORIZATION TEST

- [ ] nasabah tidak bisa akses CMS
- [ ] petugas restricted actions
- [ ] admin full access only

---

## 5.2 IDOR TEST

- [ ] user tidak bisa akses data user lain
- [ ] mutation tidak bisa dimanipulasi
- [ ] deposit tidak bisa di-edit tanpa permission

---

## 5.3 INPUT INJECTION TEST

- [ ] SQL injection attempt blocked
- [ ] XSS payload sanitized
- [ ] file upload restricted

---

# 6. PERFORMANCE TEST

## Checklist

- [ ] dashboard load < 2s
- [ ] deposit list pagination aktif
- [ ] eager loading digunakan
- [ ] no N+1 query detected
- [ ] cache dashboard stats active

---

# 7. EDGE CASE TESTING

- user saldo 0 withdraw attempt
- concurrent deposit approval
- duplicate form submission
- network retry submission
- session expired mid transaction

---

# 8. REGRESSION TEST AREA

Setiap perubahan wajib test ulang:

- deposit flow
- withdrawal flow
- mutation system
- role access
- admin CMS
- gamification sync

---

# 9. ACCEPTANCE CRITERIA

Fitur dianggap valid jika:

- semua positive test pass
- semua negative test handled
- tidak ada security breach
- data konsisten di DB
- tidak ada duplicate mutation
- UI tidak crash di role manapun

---

# 10. CRITICAL FAILURE CONDITIONS

Jika terjadi:

- saldo tidak sinkron dengan mutation
- user bisa bypass role
- approval tanpa authorization
- duplicate financial record

→ fitur dianggap FAILED otomatis

---

# 11. QA PRINCIPLE

> Tidak ada fitur dianggap selesai sebelum lolos functional + security + regression test.
