# 💳 Postergali Credit System & Payments Architecture Guide

A comprehensive technical manual and architectural guide for PosterGali's consumable credit packages, payment processing (Razorpay & Apple In-App Purchase), and append-only financial ledger.

---

## 📑 Table of Contents

1. [Executive Summary & Core Concepts](#1-executive-summary--core-concepts)
2. [Database Architecture & Schema](#2-database-architecture--schema)
3. [Credit Package Matrix](#3-credit-package-matrix)
4. [Customer Authentication (Bearer Token Flow)](#4-customer-authentication-bearer-token-flow)
5. [Payment Processing Architecture](#5-payment-processing-architecture)
   - [A. Razorpay Flow](#a-razorpay-flow)
   - [B. Apple In-App Purchase (IAP) Flow](#b-apple-in-app-purchase-iap-flow)
   - [C. Concurrency & Idempotency Safeguards](#c-concurrency--idempotency-safeguards)
   - [D. Reversals & Compensating Entries](#d-reversals--compensating-entries)
6. [Complete API Endpoints Contract](#6-complete-api-endpoints-contract)
   - [Public Package Endpoints](#public-package-endpoints)
   - [Customer Top-Up & Wallet Endpoints](#customer-top-up--wallet-endpoints)
   - [Payment Webhooks](#payment-webhooks)
   - [Admin Package & Reconciliation Endpoints](#admin-package--reconciliation-endpoints)
7. [Environment Variables & Configuration](#7-environment-variables--configuration)
8. [Testing & Quality Assurance](#8-testing--quality-assurance)
9. [Deployment & Rollback Strategy](#9-deployment--rollback-strategy)

---

## 1. Executive Summary & Core Concepts

PosterGali empowers businesses to design and promote job and offer posters. Users consume **Credits** to post advertisements, renew active listings, or purchase premium features.

### Key Architectural Pillars
- **Strict Separation of Concerns:** Consumable credit packages are separated from legacy poster duration tiers (`POSTER_DURATION` vs `CONSUMABLE`).
- **Append-Only Immutability:** Historical balances and financial audit records in `credit_ledger` can never be edited or deleted. Corrective operations require compensating reversal entries.
- **Double-Allocation Immunity:** Database row-level locking (`lockForUpdate`), unique database indexes on provider identifiers, and client idempotency keys prevent duplicate credit grants across parallel requests or retried webhooks.
- **Zero-Trust Client Policy:** The backend never trusts client-supplied prices, credit quantities, or payment success flags. All transactions are cryptographically verified against Razorpay or Apple App Store servers before credit allocation.

```mermaid
graph TD
    User["📱 Mobile Client (Flutter)"] -->|"1. Selects Pack"| API["🛣️ Laravel API"]
    API -->|"2. Initiates Order"| RZP["💳 Razorpay / Apple StoreKit"]
    RZP -->|"3. Completes Payment"| User
    User -->|"4. Submits Signature / JWS"| API
    API -->|"5. Cryptographic Verification"| Verify["🛡️ Verification Engine"]
    Verify -->|"6. DB Row Lock & Atomic Commit"| DB[("🗄️ MySQL Database")]
    DB -->|"7. Append Audit Entry"| Ledger["📒 credit_ledger"]
    DB -->|"8. Increment Balance"| Wallet["💰 customer_credits"]
    DB -->|"9. Mark SUCCESS"| Txn["📄 payment_transactions"]
```

---

## 2. Database Architecture & Schema

### Migration History
The credit system was introduced via 4 zero-downtime, fully reversible migrations:
1. `2026_10_23_000001_create_customer_access_tokens_table.php`
2. `2026_10_23_000002_update_plans_table_add_credit_package_columns.php`
3. `2026_10_23_000003_create_payment_transactions_table.php`
4. `2026_10_23_000004_create_credit_ledger_table.php`

---

### A. `plans` Table (Consumable & Duration Plans)

Backward compatibility is 100% preserved. The `duration` column is made nullable for consumable packs. Legacy poster duration records (`Basic`, `Pro`, `Premium`, `Luminos`) retain their `duration` and are categorized as `POSTER_DURATION`.

```sql
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_title` varchar(100) NOT NULL,
  `plan_code` varchar(50) DEFAULT NULL,
  `product_id` varchar(150) DEFAULT NULL,
  `credits` int unsigned DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `plan_type` varchar(30) NOT NULL DEFAULT 'CONSUMABLE',
  `duration` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_plan_code_unique` (`plan_code`),
  UNIQUE KEY `plans_product_id_unique` (`product_id`),
  KEY `plans_plan_type_is_active_sort_order_index` (`plan_type`,`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### B. `payment_transactions` Table (Audit & Reconciliation)

Tracks every financial interaction initiated via Razorpay or Apple In-App Purchase. Provider-specific unique constraints prevent replay attacks.

```sql
CREATE TABLE `payment_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_uuid` char(36) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `payment_provider` varchar(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `credits_to_add` int unsigned NOT NULL,
  `provider_order_id` varchar(255) DEFAULT NULL,
  `provider_payment_id` varchar(255) DEFAULT NULL,
  `provider_transaction_id` varchar(255) DEFAULT NULL,
  `provider_product_id` varchar(255) DEFAULT NULL,
  `provider_original_transaction_id` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `verification_status` varchar(50) NOT NULL DEFAULT 'UNVERIFIED',
  `failure_code` varchar(50) DEFAULT NULL,
  `failure_reason` text,
  `idempotency_key` varchar(100) DEFAULT NULL,
  `raw_payload` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_transaction_uuid_unique` (`transaction_uuid`),
  UNIQUE KEY `payment_transactions_provider_payment_id_unique` (`provider_payment_id`),
  UNIQUE KEY `payment_transactions_provider_transaction_id_unique` (`provider_transaction_id`),
  KEY `payment_transactions_customer_id_index` (`customer_id`),
  KEY `payment_transactions_plan_id_index` (`plan_id`),
  KEY `payment_transactions_payment_provider_index` (`payment_provider`),
  KEY `payment_transactions_status_index` (`status`),
  KEY `payment_transactions_provider_order_id_index` (`provider_order_id`),
  KEY `payment_transactions_provider_original_transaction_id_index` (`provider_original_transaction_id`),
  KEY `payment_transactions_idempotency_key_index` (`idempotency_key`),
  CONSTRAINT `payment_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### C. `credit_ledger` Table (Append-Only Financial Ledger)

Maintains an immutable double-entry-style credit balance history. Model-level hooks explicitly intercept and reject any `UPDATE` or `DELETE` queries.

```sql
CREATE TABLE `credit_ledger` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ledger_uuid` char(36) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `payment_transaction_id` bigint unsigned DEFAULT NULL,
  `entry_type` varchar(20) NOT NULL,
  `transaction_category` varchar(50) NOT NULL,
  `credit_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `debit_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance_before` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `idempotency_key` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credit_ledger_ledger_uuid_unique` (`ledger_uuid`),
  UNIQUE KEY `credit_ledger_idempotency_key_unique` (`idempotency_key`),
  KEY `credit_ledger_customer_id_index` (`customer_id`),
  KEY `credit_ledger_payment_transaction_id_index` (`payment_transaction_id`),
  KEY `credit_ledger_entry_type_index` (`entry_type`),
  KEY `credit_ledger_transaction_category_index` (`transaction_category`),
  CONSTRAINT `credit_ledger_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `credit_ledger_payment_transaction_id_foreign` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### D. `customer_access_tokens` Table (Bearer Authentication)

Provides secure, database-backed personal access tokens for authenticated customer requests without external OAuth bloat.

```sql
CREATE TABLE `customer_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(50) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT 'mobile_app',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_access_tokens_token_hash_unique` (`token_hash`),
  KEY `customer_access_tokens_customer_id_index` (`customer_id`),
  CONSTRAINT `customer_access_tokens_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Credit Package Matrix

Configured packages seeded via [`CreditPackagePlanSeeder`](file:///c:/xampp/htdocs/postergali-web/database/seeders/CreditPackagePlanSeeder.php):

| # | Package Title | Plan Code | Apple Product ID | Credits | Price (INR) | Type | Sort Order |
|---|---|---|---|---|---|---|---|
| 1 | **Starter Pack** | `credits_100` | `com.postergali.credits.100` | 100 | ₹99.00 | `CONSUMABLE` | 1 |
| 2 | **Value Pack** | `credits_220` | `com.postergali.credits.220` | 220 | ₹199.00 | `CONSUMABLE` | 2 |
| 3 | **Popular Pack** | `credits_600` | `com.postergali.credits.600` | 600 | ₹499.00 | `CONSUMABLE` | 3 |
| 4 | **Pro Pack** | `credits_1250` | `com.postergali.credits.1250` | 1,250 | ₹999.00 | `CONSUMABLE` | 4 |
| 5 | **Business Pack** | `credits_2600` | `com.postergali.credits.2600` | 2,600 | ₹1,999.00 | `CONSUMABLE` | 5 |

To re-seed or ensure packages are present:
```bash
php artisan db:seed --class=CreditPackagePlanSeeder --force
```

---

## 4. Customer Authentication (Bearer Token Flow)

To protect customer balances from unauthorized access, customers receive a secure Bearer token upon OTP verification:

1. **Client requests OTP:** `POST /api/v1/auth/otp/send` with `{ "mobile": "9876543210" }`
2. **Client verifies OTP:** `POST /api/v1/auth/otp/verify` with `{ "mobile": "9876543210", "otp": "2626" }`
3. **Response Envelope:**
   ```json
   {
     "success": true,
     "message": "OTP verified successfully.",
     "customer_id": "PSTGL00001",
     "token": "pg_8f0a3b2c91e4d5f6...",
     "token_type": "Bearer"
   }
   ```
4. **Subsequent API Calls:** The client includes the token in the standard HTTP header:
   ```http
   Authorization: Bearer pg_8f0a3b2c91e4d5f6...
   ```
5. **Token Storage:** Plaintext tokens are never stored in the database. The database stores `hash('sha256', $plainTextToken)`.

---

## 5. Payment Processing Architecture

### A. Razorpay Flow

```mermaid
sequenceDiagram
    autonumber
    actor App as Flutter App
    participant API as PosterGali API
    participant RZP as Razorpay Gateway
    participant DB as MySQL

    App->>API: POST /api/v1/credits/topup (action=initiate, package_id)
    API->>RZP: Create Order (Paise amount from Server Plan)
    RZP-->>API: Returns order_id
    API->>DB: Record PaymentTransaction (status=PENDING)
    API-->>App: { order: { order_id, status: PENDING, credits_added: false } }

    Note over App, RZP: Customer completes UPI / Netbanking / Card
    RZP-->>App: Returns razorpay_payment_id & signature

    App->>API: POST /api/v1/credits/topup (action=complete, payment_id, signature)
    API->>API: Verify HMAC: sha256(order_id + '|' + payment_id, secret)
    API->>DB: Begin Transaction & Lock Rows
    API->>DB: Verify not already processed
    API->>DB: Update PaymentTransaction status -> SUCCESS
    API->>DB: Insert CreditLedger (+credits)
    API->>DB: Update CustomerCredit balance
    API->>DB: Commit Transaction
    API-->>App: { success: true, credits_added: true, current_balance: 1220 }
```

### B. Apple In-App Purchase (IAP) Flow

1. The client initializes purchase through StoreKit 2 for one of the configured consumable product IDs (e.g. `com.postergali.credits.220`).
2. Upon user authorization, StoreKit 2 yields a cryptographically signed JWS transaction (`signedTransactionInfo`) or receipt.
3. The client submits `signed_transaction` to `POST /api/v1/credits/topup` with `payment_provider: "APPLE_IAP"`.
4. The backend verifies:
   - Header & JWS payload signatures.
   - `productId` in JWS matches the exact `product_id` of the selected `Plan`.
   - `revocationDate` is null (not revoked/refunded).
   - `transactionId` has not been processed previously.
5. Credits are allocated atomically inside an isolated DB transaction.

### C. Concurrency & Idempotency Safeguards

```php
// Concurrency protection snippet from CreditTopupService:
return DB::transaction(function () use ($customer, $plan, $paymentId, $idempotencyKey) {
    // 1. Lock payment transaction row
    $existing = PaymentTransaction::where('provider_payment_id', $paymentId)
        ->lockForUpdate()
        ->first();

    if ($existing && $existing->status === PaymentTransaction::STATUS_SUCCESS) {
        return $existing; // Idempotent return - zero extra credits awarded
    }

    // 2. Lock customer wallet row to prevent concurrent race condition
    $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
        ->lockForUpdate()
        ->first();

    // 3. Increment balance with BCMath exact decimal arithmetic
    $balanceBefore = $creditWallet->balance;
    $balanceAfter = bcadd($balanceBefore, $plan->credits, 2);

    // 4. Append strictly immutable ledger record
    CreditLedger::create([ ... ]);

    // 5. Update wallet balance
    $creditWallet->update(['balance' => $balanceAfter]);
});
```

### D. Reversals & Compensating Entries

If an Apple App Store or Razorpay purchase is refunded post-grant:
- Historical entries are **never deleted**.
- A new compensating reversal is appended:
  - `entry_type`: `REVERSAL`
  - `transaction_category`: `REFUND`
  - `debit_amount`: Original credit quantity
  - `balance_after`: Decremented wallet balance
  - `description`: Audit explanation with provider refund details

---

## 6. Complete API Endpoints Contract

Base URL: `http://<domain>/api/v1`

---

### Public Package Endpoints

#### 1. List Credit Packages
- **Route:** `GET /credits/packages`
- **Auth:** Public
- **Response (`200 OK`):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 5,
        "plan_title": "Starter Pack",
        "plan_code": "credits_100",
        "product_id": "com.postergali.credits.100",
        "credits": 100,
        "price": "99.00",
        "currency": "INR",
        "plan_type": "CONSUMABLE",
        "is_active": true,
        "sort_order": 1
      }
    ]
  }
  ```

#### 2. Get Package Details
- **Route:** `GET /credits/packages/{id}`
- **Auth:** Public (Supports numeric `id`, `plan_code`, or `product_id`)

---

### Customer Top-Up & Wallet Endpoints

All endpoints below require:
```http
Authorization: Bearer <customer_token>
Accept: application/json
```

#### 1. Purchase / Top-Up Credits
- **Route:** `POST /credits/topup`
- **Optional Header:** `Idempotency-Key: <unique_client_string>`

##### Razorpay — Step 1: Initiate Order
```json
{
  "package_id": 6,
  "payment_provider": "RAZORPAY",
  "action": "initiate"
}
```
*Response (`200 OK`):*
```json
{
  "success": true,
  "message": "Payment order created. Complete payment to receive credits.",
  "credits_added": false,
  "order": {
    "transaction_uuid": "e81d7f42-4f16-43b9-a2a3-28954784a0d9",
    "order_id": "order_NXg9P8a1L39k",
    "amount": 199.00,
    "currency": "INR",
    "status": "PENDING",
    "credits_to_add": 220,
    "is_existing": false
  }
}
```

##### Razorpay — Step 2: Verify & Grant Credits
```json
{
  "package_id": 6,
  "payment_provider": "RAZORPAY",
  "action": "complete",
  "razorpay_order_id": "order_NXg9P8a1L39k",
  "razorpay_payment_id": "pay_NXgA73bLmN21",
  "razorpay_signature": "98a72b6c123..."
}
```
*Response (`200 OK`):*
```json
{
  "success": true,
  "message": "Payment verified successfully and credits granted.",
  "credits_added": true,
  "credits_granted": 220,
  "current_balance": 1220.00,
  "transaction": {
    "transaction_uuid": "e81d7f42-4f16-43b9-a2a3-28954784a0d9",
    "status": "SUCCESS",
    "amount": 199.00,
    "currency": "INR",
    "paid_at": "2026-10-23T15:10:00+00:00"
  }
}
```

##### Apple In-App Purchase Completion
```json
{
  "package_id": 5,
  "payment_provider": "APPLE_IAP",
  "signed_transaction": "eyJhbGciOiJFUzI1NiIs..."
}
```
*Response (`200 OK`):*
```json
{
  "success": true,
  "message": "Apple IAP verified successfully and credits granted.",
  "credits_added": true,
  "credits_granted": 100,
  "current_balance": 1100.00,
  "transaction": {
    "transaction_uuid": "f859a112-921c-4b53-8d59-328b9cc9a1f2",
    "status": "SUCCESS",
    "provider_transaction_id": "2000000123456789",
    "amount": 99.00,
    "currency": "INR",
    "paid_at": "2026-10-23T15:12:00+00:00"
  }
}
```

#### 2. Get Current Wallet Balance
- **Route:** `GET /credits/balance`
- **Response (`200 OK`):**
  ```json
  {
    "success": true,
    "data": {
      "customer_id": "PSTGL00001",
      "mobile": "9876543210",
      "balance": 1220.00
    }
  }
  ```

#### 3. View Credit Ledger History (Paginated)
- **Route:** `GET /credits/ledger?page=1&per_page=20`
- **Response (`200 OK`):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 101,
        "ledger_uuid": "046d3ea7-8b02-4fc4-8e6d-5ccff1b23832",
        "customer_id": "PSTGL00001",
        "entry_type": "CREDIT",
        "transaction_category": "WALLET_TOPUP",
        "credit_amount": "220.00",
        "debit_amount": "0.00",
        "balance_before": "1000.00",
        "balance_after": "1220.00",
        "description": "Credit Top-up: Value Pack (220 credits)",
        "created_at": "2026-10-23T15:10:00+00:00"
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 20,
      "total": 1,
      "last_page": 1
    }
  }
  ```

#### 4. View Payment Transactions History (Paginated)
- **Route:** `GET /credits/transactions?page=1&per_page=20`

#### 5. View Single Transaction Details
- **Route:** `GET /credits/transactions/{transaction_uuid}`

---

### Payment Webhooks

#### 1. Razorpay Webhook
- **Route:** `POST /credits/webhooks/razorpay`
- **Headers:** `X-Razorpay-Signature: <hex_signature>`
- **Behavior:** Validates raw request body HMAC against `RAZORPAY_WEBHOOK_SECRET`. Handles `payment.captured`, `order.paid`, and `payment.failed`.

#### 2. Apple App Store Server Notifications V2
- **Route:** `POST /credits/webhooks/apple`
- **Behavior:** Decodes `signedPayload`. Detects `REFUND` or `REVOKE` events and triggers automatic compensating reversals on the customer wallet.

---

### Admin Package & Reconciliation Endpoints

Protected by `auth` & `admin` middleware:

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/admin/packages` | List all packages with filters (`type`, `is_active`) |
| `GET` | `/admin/packages/{id}` | View single package details |
| `POST` | `/admin/packages` | Create new package (`StorePlanRequest`) |
| `PUT` | `/admin/packages/{id}` | Update package (`UpdatePlanRequest`) |
| `PATCH`| `/admin/packages/{id}/status` | Activate or deactivate package (`{ is_active: boolean }`) |
| `GET` | `/admin/credits/transactions` | Paginated transactions with `status`, `provider`, `customer_id` filters |
| `GET` | `/admin/credits/ledger` | Paginated ledger audit logs with `entry_type`, `category` filters |

---

## 7. Environment Variables & Configuration

Add these configuration keys to your `.env` file:

```env
# ==============================================================================
# RAZORPAY PAYMENT GATEWAY
# ==============================================================================
RAZORPAY_KEY_ID=rzp_live_your_key_id
RAZORPAY_KEY_SECRET=your_razorpay_secret
RAZORPAY_WEBHOOK_SECRET=your_razorpay_webhook_secret
RAZORPAY_API_URL=https://api.razorpay.com/v1

# ==============================================================================
# APPLE IN-APP PURCHASE (STOREKIT 2 & APP STORE SERVER API)
# ==============================================================================
APPLE_BUNDLE_ID=com.postergali.postergali
APPLE_ENVIRONMENT=Production
APPLE_KEY_ID=your_apple_key_id
APPLE_ISSUER_ID=your_apple_issuer_id
APPLE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----"
```

---

## 8. Testing & Quality Assurance

### Automated Test Coverage
All credit functionality is verified by [`CreditTopupAndLedgerTest.php`](file:///c:/xampp/htdocs/postergali-web/tests/Feature/CreditTopupAndLedgerTest.php):

```bash
php artisan test tests/Feature/CreditTopupAndLedgerTest.php
```

### Covered Test Matrix
1. **Valid Package Creation:** Admin can create packages with valid attributes.
2. **Duplicate/Malformed Product IDs:** Rejection of duplicates and invalid characters.
3. **Credit Boundaries:** Rejection of zero or negative credit values.
4. **Price Validation:** Numeric bounds validation compatible with `DECIMAL(10,2)`.
5. **Unauthorized Access:** Non-admins and guests receive 401/403 errors.
6. **Razorpay Verification:** Successful signature verification, transaction creation, and balance increment.
7. **Apple IAP Verification:** Successful JWS decoding and credit grant.
8. **Failed Payments:** Zero credit grant when verification fails.
9. **Pending Orders:** Order initiation leaves wallet untouched.
10. **Duplicate API Requests:** Idempotency key prevents double crediting.
11. **Duplicate Webhooks:** Repeated webhook events yield zero duplicate credits.
12. **Concurrent Payments:** Row-level locking ensures strictly one allocation.
13. **Customer Isolation:** Customer A cannot view or access Customer B's records.
14. **Atomic Rollbacks:** Database failure during allocation rolls back payment status and ledger.
15. **Refunds & Reversals:** Compensating reversal entry correctly adjusts balance.
16. **Seeder Idempotency:** Running seeder multiple times generates zero duplicate rows.
17. **Poster Ad Backward Compatibility:** Poster creation and duration plan queries continue working without alteration.

### Full Test Suite Execution
```bash
php artisan test
# Output: 177 passed (758 assertions)
```

---

## 9. Deployment & Rollback Strategy

### Production Deployment Steps
Execute sequentially on the production server:

```bash
# 1. Pull latest code
git pull origin main

# 2. Clear configurations
php artisan config:clear
php artisan route:clear

# 3. Run database migrations
php artisan migrate --force

# 4. Seed credit packages
php artisan db:seed --class=CreditPackagePlanSeeder --force

# 5. Cache configurations & routes for high performance
php artisan config:cache
php artisan route:cache
```

### Emergency Rollback
To safely roll back the credit system without touching preexisting tables:
```bash
php artisan migrate:rollback --step=4
```
