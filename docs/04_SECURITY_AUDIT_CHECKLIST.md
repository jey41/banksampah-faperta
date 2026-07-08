# SECURITY AUDIT CHECKLIST — BANK SAMPAH FAPERTA

Dokumen ini digunakan untuk audit keamanan sistem Bank Sampah Faperta secara sistematis dan berlapis.

---

# 1. SECURITY MODEL OVERVIEW

Sistem menggunakan model keamanan:

- Authentication (siapa user)
- Authorization (apa yang boleh dilakukan user)
- Validation (apa yang boleh masuk ke sistem)
- Data Integrity (konsistensi data)
- Audit Trail (jejak aktivitas)

---

# 2. AUTHENTICATION SECURITY

## Checklist

- [ ] Login menggunakan session-based authentication (Laravel Breeze)
- [ ] Password di-hash menggunakan bcrypt/argon
- [ ] Email verification aktif sebelum akses penuh
- [ ] Session disimpan di secure storage (database/redis)
- [ ] Session timeout aktif

## Risiko yang dicegah

- credential theft reuse
- session hijacking
- unauthorized login

---

# 3. AUTHORIZATION SECURITY (RBAC)

## Role Model

- admin
- petugas
- nasabah

## Checklist

- [ ] Middleware role aktif di semua route protected
- [ ] Policy digunakan untuk semua model penting
- [ ] Gate digunakan untuk permission spesifik
- [ ] Nasabah tidak bisa akses CMS (/cms)
- [ ] Petugas memiliki limited admin access

## Critical Rule

> Tidak ada akses berdasarkan UI hiding. Semua harus enforced di backend.

---

# 4. BROKEN ACCESS CONTROL PREVENTION

## Checklist

- [ ] Tidak ada endpoint tanpa auth middleware
- [ ] Tidak ada IDOR (Insecure Direct Object Reference)
- [ ] User hanya bisa akses data miliknya sendiri
- [ ] Admin/petugas hanya via policy check

---

# 5. INPUT VALIDATION SECURITY

## Checklist

- [ ] Semua request divalidasi (FormRequest atau validate())
- [ ] Tidak ada raw input langsung ke query
- [ ] Type validation enforced
- [ ] Range validation untuk financial input (amount, weight)

## Financial Validation Rules

- amount > 0
- weight > 0
- withdrawal ≤ saldo user
- pickup distance ≤ 2km

---

# 6. SQL INJECTION PREVENTION

## Checklist

- [ ] Semua query menggunakan Eloquent / Query Builder
- [ ] Tidak ada raw SQL tanpa binding
- [ ] No dynamic query concatenation dari user input

---

# 7. XSS PREVENTION

## Checklist

- [ ] Blade escaping (`{{ }}`) digunakan default
- [ ] React auto-escape JSX aktif
- [ ] CKEditor sanitized output (admin CMS)
- [ ] No unsafe innerHTML tanpa sanitization

---

# 8. CSRF PROTECTION

## Checklist

- [ ] CSRF middleware aktif
- [ ] Semua POST/PUT/DELETE request memiliki token
- [ ] Form React/Inertia include CSRF header

---

# 9. FILE UPLOAD SECURITY

## Checklist

- [ ] File type validation (image/pdf only jika diperlukan)
- [ ] Max file size limit enforced
- [ ] File stored di storage terisolasi
- [ ] Filename tidak user-controlled
- [ ] Malware risk minimized

## Rules

Dilarang:

- executable upload
- unrestricted file types
- public direct overwrite

---

# 10. SESSION SECURITY

## Checklist

- [ ] Session driver database/redis
- [ ] HTTP-only cookie enabled
- [ ] Secure cookie in production
- [ ] SameSite=Lax/Strict
- [ ] Session regeneration after login

---

# 11. RATE LIMITING

## Checklist

- [ ] Login throttling aktif
- [ ] Email verification throttled (6/min)
- [ ] API-like endpoints protected (future-ready)

---

# 12. FINANCIAL SECURITY (CRITICAL)

## Rules wajib

- Semua transaksi menggunakan DB transaction
- lockForUpdate wajib untuk saldo user
- Tidak boleh update saldo langsung
- Semua perubahan saldo harus melalui mutation table

## Checklist

- [ ] Deposit approval atomic
- [ ] Withdrawal approval atomic
- [ ] Mutation record selalu dibuat
- [ ] Balance_before & balance_after tercatat
- [ ] Rollback jika gagal

---

# 13. AUDIT TRAIL SECURITY

## Checklist

- [ ] Activity log aktif untuk semua aksi penting
- [ ] Mutation table immutable
- [ ] Admin action tercatat
- [ ] Approval/rejection traceable

---

# 14. PRIVILEGE ESCALATION PREVENTION

## Checklist

- [ ] Nasabah tidak bisa upgrade role sendiri
- [ ] Role hanya bisa diubah admin
- [ ] Tidak ada endpoint update role publik
- [ ] Policy enforced untuk update user

---

# 15. ENVIRONMENT SECURITY

## Checklist

- [ ] APP_DEBUG = false in production
- [ ] APP_KEY secure
- [ ] DB credentials tidak hardcoded
- [ ] Sensitive env not exposed
- [ ] Storage permissions restricted

---

# 16. LOGGING & MONITORING

## Checklist

- [ ] Error logging aktif
- [ ] Activity log tersimpan
- [ ] Transaction failure logged
- [ ] Unauthorized access attempt logged

---

# 17. TOP SECURITY RISKS (PRIORITY)

## CRITICAL

- Direct saldo manipulation bypass mutation
- Broken access control (IDOR)
- Missing authorization on CMS routes

## HIGH

- File upload abuse
- Missing validation on financial input
- Session hijacking risk

## MEDIUM

- Missing rate limiting on non-critical routes
- Over-permissive role assignment logic

## LOW

- Minor XSS risk in markdown/article content

---

# 18. SECURITY PRINCIPLE

> Security is not a feature, it is a system constraint.

Semua perubahan wajib mempertimbangkan:

- authentication
- authorization
- auditability
- financial integrity
