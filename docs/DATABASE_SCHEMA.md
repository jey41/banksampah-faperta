# Database Schema Documentation

Complete database schema reference for Bank Sampah Faperta.

---

## 📊 Entity Relationship Overview

```
users (1) ─────< (N) deposits
              │
              └─────< (N) withdrawals
              │
              └─────< (N) mutations
              │
              └─────< (N) pickup_requests
              │
              └─────< (N) savings_targets
              │
              └─────< (N) user_badges

deposits (1) ─────< (N) deposit_items
         (1) ──────< (N) mutations (polymorphic)

deposit_items (N) ────> (1) trash_prices

withdrawals (1) ──────< (N) withdrawal_history
            (1) ──────< (N) mutations (polymorphic)
```

---

## 📋 Table Definitions

### **users**

User accounts for all roles (admin, petugas, nasabah).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(255) | NOT NULL | Full name |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE | Email address |
| `email_verified_at` | TIMESTAMP | NULLABLE | Email verification timestamp |
| `password` | VARCHAR(255) | NOT NULL | Hashed password |
| `role` | ENUM | NOT NULL, DEFAULT 'nasabah' | admin, petugas, nasabah |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | pending, verified, blocked |
| `phone` | VARCHAR(20) | NULLABLE | Contact number |
| `address` | TEXT | NULLABLE | Physical address |
| `saldo` | BIGINT | NOT NULL, DEFAULT 0 | Balance (in smallest currency unit) |
| `account_no` | VARCHAR(50) | NULLABLE, UNIQUE | Account number |
| `umur` | INTEGER | NULLABLE | Age |
| `gender` | ENUM | NULLABLE | male, female |
| `status_pekerjaan` | VARCHAR(100) | NULLABLE | Employment status |
| `universitas` | VARCHAR(255) | NULLABLE | University name |
| `fakultas` | VARCHAR(255) | NULLABLE | Faculty name |
| `pendidikan_terakhir` | VARCHAR(100) | NULLABLE | Highest education level |
| `remember_token` | VARCHAR(100) | NULLABLE | Laravel remember token |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`email`)
- UNIQUE KEY (`account_no`)
- INDEX (`role`, `status`)

---

### **trash_prices**

Pricing catalog for waste categories.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(255) | NOT NULL | Waste type name |
| `category` | ENUM | NOT NULL | plastik, kertas, logam, kaca, minyak_jelantah, lainnya |
| `price_buy` | BIGINT | NOT NULL | Purchase price from nasabah (per unit) |
| `price_sell` | BIGINT | NOT NULL | Selling price to factory (per unit) |
| `unit` | VARCHAR(10) | NOT NULL, DEFAULT 'kg' | Unit of measurement |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`category`)

**Business Rules:**
- `price_sell` should typically be higher than `price_buy` (profit margin)
- Categories are fixed enum values
- Default unit is 'kg' but can be 'L' for liquids (minyak_jelantah)

---

### **deposits**

Waste deposit transactions from nasabah.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `total_price` | BIGINT | NOT NULL, DEFAULT 0 | Total transaction value |
| `weight_total` | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Total weight in kg |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | pending, approved, rejected |
| `is_donation` | BOOLEAN | NOT NULL, DEFAULT false | If true, value not credited |
| `notes` | TEXT | NULLABLE | Additional notes |
| `validated_by` | BIGINT UNSIGNED | FK, NULLABLE | Admin/petugas who validated |
| `created_at` | TIMESTAMP | | Transaction timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE CASCADE
- `validated_by` REFERENCES `users(id)` ON DELETE SET NULL

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`status`)
- INDEX (`created_at`)

**Business Rules:**
- `total_price` = SUM(deposit_items.total_price)
- `weight_total` = SUM(deposit_items.weight)
- When `status` changes to 'approved' and `is_donation` = false:
  - User's `saldo` += `total_price`
  - Create mutation entry (type: 'kredit')
- When `is_donation` = true, no balance update occurs

---

### **deposit_items**

Individual waste items within a deposit transaction.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `deposit_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to deposits.id |
| `trash_price_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to trash_prices.id |
| `weight` | DECIMAL(10,2) | NOT NULL | Weight/volume of this item |
| `price_per_unit` | BIGINT | NOT NULL | Snapshot of price at transaction time |
| `total_price` | BIGINT | NOT NULL | weight × price_per_unit |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `deposit_id` REFERENCES `deposits(id)` ON DELETE CASCADE
- `trash_price_id` REFERENCES `trash_prices(id)` ON DELETE RESTRICT

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`deposit_id`)
- INDEX (`trash_price_id`)

**Business Rules:**
- `total_price` = `weight` × `price_per_unit`
- `price_per_unit` is snapshotted from `trash_prices.price_buy` at transaction time
- Cannot delete `trash_prices` if referenced by deposit_items (RESTRICT)

---

### **withdrawals**

Balance withdrawal requests from nasabah.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `amount` | BIGINT | NOT NULL | Withdrawal amount |
| `bank_name` | VARCHAR(100) | NOT NULL | Bank name |
| `account_number` | VARCHAR(50) | NOT NULL | Bank account number |
| `account_name` | VARCHAR(255) | NOT NULL | Account holder name |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | pending, approved, rejected |
| `notes` | TEXT | NULLABLE | Admin notes or rejection reason |
| `validated_by` | BIGINT UNSIGNED | FK, NULLABLE | Admin/petugas who validated |
| `created_at` | TIMESTAMP | | Request timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE CASCADE
- `validated_by` REFERENCES `users(id)` ON DELETE SET NULL

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`status`)
- INDEX (`created_at`)

**Business Rules:**
- Cannot withdraw more than user's current `saldo`
- When `status` changes to 'approved':
  - User's `saldo` -= `amount`
  - Create mutation entry (type: 'debit')
  - Log in withdrawal_history

---

### **withdrawal_history**

Audit trail for withdrawal status changes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `withdrawal_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to withdrawals.id |
| `old_status` | ENUM | NOT NULL | Previous status value |
| `new_status` | ENUM | NOT NULL | New status value |
| `changed_by` | BIGINT UNSIGNED | FK, NULLABLE | User who made the change |
| `changed_at` | TIMESTAMP | NOT NULL | When the change occurred |

**Foreign Keys:**
- `withdrawal_id` REFERENCES `withdrawals(id)` ON DELETE CASCADE
- `changed_by` REFERENCES `users(id)` ON DELETE SET NULL

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`withdrawal_id`)

---

### **mutations**

Double-entry ledger for all balance changes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `type` | ENUM | NOT NULL | debit, kredit |
| `amount` | BIGINT | NOT NULL | Transaction amount |
| `sourceable_type` | VARCHAR(255) | NULLABLE | Polymorphic type (Deposit/Withdrawal) |
| `sourceable_id` | BIGINT UNSIGNED | NULLABLE | Polymorphic ID |
| `balance_before` | BIGINT | NOT NULL | Balance before transaction |
| `balance_after` | BIGINT | NOT NULL | Balance after transaction |
| `created_at` | TIMESTAMP | | Transaction timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`sourceable_type`, `sourceable_id`)
- INDEX (`created_at`)

**Business Rules:**
- `type` = 'kredit': balance increase (deposits)
  - `balance_after` = `balance_before` + `amount`
- `type` = 'debit': balance decrease (withdrawals)
  - `balance_after` = `balance_before` - `amount`
- Polymorphic relationship: `sourceable` can be Deposit or Withdrawal model

---

### **articles**

Educational content and news articles.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `title` | VARCHAR(255) | NOT NULL | Article title |
| `slug` | VARCHAR(255) | NOT NULL, UNIQUE | URL-friendly slug |
| `content` | TEXT | NOT NULL | Article body (HTML/Markdown) |
| `image_url` | TEXT | NULLABLE | Featured image URL |
| `status` | ENUM | NOT NULL, DEFAULT 'draft' | draft, published |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- INDEX (`status`)

---

### **pickup_requests**

Scheduled waste collection requests from nasabah.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `pickup_date` | DATE | NOT NULL | Requested pickup date |
| `pickup_time` | TIME | NOT NULL | Requested pickup time |
| `address` | TEXT | NOT NULL | Pickup location address |
| `latitude` | DECIMAL(10,8) | NULLABLE | GPS latitude |
| `longitude` | DECIMAL(11,8) | NULLABLE | GPS longitude |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | pending, scheduled, completed, cancelled |
| `notes` | TEXT | NULLABLE | Additional notes |
| `created_at` | TIMESTAMP | | Request timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE CASCADE

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`status`)
- INDEX (`pickup_date`)

---

### **savings_targets**

Personal financial goals for nasabah.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `title` | VARCHAR(255) | NOT NULL | Goal description |
| `target_amount` | BIGINT | NOT NULL | Target balance amount |
| `is_achieved` | BOOLEAN | NOT NULL, DEFAULT false | Achievement status |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE CASCADE

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)

**Business Rules:**
- Check if `user.saldo >= target_amount` to mark as achieved
- Multiple targets allowed per user

---

### **user_badges**

Gamification achievements for users.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NOT NULL | Reference to users.id |
| `badge_key` | VARCHAR(100) | NOT NULL | Badge identifier slug |
| `unlocked_at` | TIMESTAMP | NULLABLE | When badge was earned |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE CASCADE

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`user_id`, `badge_key`)
- INDEX (`badge_key`)

**Badge Keys:**
- `first_deposit` - First successful deposit
- `consistent_contributor` - Regular deposits (TBD criteria)
- `eco_champion` - High volume contributor
- `goal_achiever` - Completed savings target
- More badges defined in application logic

---

### **activity_logs**

System-wide audit trail for admin actions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `user_id` | BIGINT UNSIGNED | FK, NULLABLE | User who performed action |
| `action` | VARCHAR(255) | NOT NULL | Action identifier |
| `description` | TEXT | NOT NULL | Human-readable description |
| `created_at` | TIMESTAMP | | Action timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Foreign Keys:**
- `user_id` REFERENCES `users(id)` ON DELETE SET NULL

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`action`)
- INDEX (`created_at`)

---

## 🔐 Data Integrity Rules

### Currency Handling
- All monetary values stored as **BIGINT** (smallest currency unit, e.g., cents)
- Display values should divide by 100 (assuming Rupiah)
- Example: `50000` stored = Rp 500.00 displayed

### Transaction Atomicity
- Deposit approval must be wrapped in database transaction:
  1. Update deposit status
  2. Update user saldo
  3. Create mutation record
  
- Withdrawal approval must be atomic:
  1. Verify sufficient balance
  2. Update withdrawal status
  3. Update user saldo
  4. Create mutation record
  5. Log in withdrawal_history

### Balance Verification
```sql
-- User balance should always equal mutation sum
SELECT 
    u.id,
    u.saldo as current_balance,
    COALESCE(SUM(CASE WHEN m.type = 'kredit' THEN m.amount ELSE -m.amount END), 0) as calculated_balance
FROM users u
LEFT JOIN mutations m ON m.user_id = u.id
GROUP BY u.id
HAVING current_balance != calculated_balance;
-- Should return no rows
```

---

## 📈 Common Queries

### Get User Dashboard Summary
```sql
SELECT 
    u.name,
    u.saldo,
    COUNT(DISTINCT d.id) as total_deposits,
    SUM(d.total_price) as total_earned,
    COUNT(DISTINCT w.id) as total_withdrawals
FROM users u
LEFT JOIN deposits d ON d.user_id = u.id AND d.status = 'approved'
LEFT JOIN withdrawals w ON w.user_id = u.id
WHERE u.id = :user_id
GROUP BY u.id;
```

### Get Pending Transactions
```sql
SELECT 
    'deposit' as type,
    d.id,
    u.name as user_name,
    d.total_price as amount,
    d.created_at
FROM deposits d
JOIN users u ON u.id = d.user_id
WHERE d.status = 'pending'
UNION ALL
SELECT 
    'withdrawal' as type,
    w.id,
    u.name as user_name,
    w.amount,
    w.created_at
FROM withdrawals w
JOIN users u ON u.id = w.user_id
WHERE w.status = 'pending'
ORDER BY created_at ASC;
```

### Calculate Deposit Statistics by Category
```sql
SELECT 
    tp.category,
    COUNT(di.id) as transaction_count,
    SUM(di.weight) as total_weight,
    SUM(di.total_price) as total_value
FROM deposit_items di
JOIN trash_prices tp ON tp.id = di.trash_price_id
JOIN deposits d ON d.id = di.deposit_id
WHERE d.status = 'approved'
  AND d.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY tp.category
ORDER BY total_value DESC;
```

---

*Last updated: 2026-07-06*
