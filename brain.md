# 🧠 PosterGali Web — Project Brain & Architecture Knowledge Base

> **Single Source of Truth for the PosterGali Backend**  
> *Keep this document up-to-date whenever new domain logic, models, services, APIs, or architectural decisions are added.*

---

## 📌 1. Project Overview & Core Mission

**PosterGali** is a hyperlocal advertising and discovery platform designed for local businesses, job seekers, and consumers.
- **Job Posters**: Local employers publish hiring posters (roles, salary, location, job type).
- **Offer Posters**: Merchants publish retail/service deals, discounts, and promotional posters with media.
- **Hyperlocal Geo-Discovery**: Users discover nearby jobs and offers within adjustable radii (0–100 km) using GPS coordinates and spatial indexing.
- **Posting Protection**: Limits abuse by enforcing a daily cap on how many posters a single phone number can publish per day.
- **Credit & Payment Wallet**: Customers receive complimentary credits upon registration and can pay for ad plans via Full UPI, Full Referral Credit, or Split (Semi) payment.

---

## 🛠️ 2. Technology Stack & Runtime Environment

| Component | Technology / Version | Notes |
|---|---|---|
| **Framework** | Laravel 12.x | PHP 8.2+ |
| **Database (Production)** | MySQL 8.x (or MariaDB) | InnoDB, spatial composite indexes on `(latitude, longitude)` |
| **Database (Testing)** | SQLite in-memory (`:memory:`) | Fast, zero-dependency automated testing with `RefreshDatabase` |
| **PDF Generation** | `barryvdh/laravel-dompdf` (DomPDF 3.x) | Generates official customer invoices with SVG/HTML styling |
| **Push Notifications** | `kreait/firebase-php` 7.4+ | Firebase Cloud Messaging (FCM) integration |
| **Admin UI & Web** | Blade Templates + Tailwind CSS / Vite | Admin dashboard, ad approval, plan management, invoice views |
| **Testing Framework** | PHPUnit 11.5.x | Feature & Unit test suites, 80+ tests passing |

---

## 🏛️ 3. Architecture & Code Organization

PosterGali adheres to a clean layered service architecture:

```
[Flutter App / Web Client]
           │
           ▼
   [routes/api.php]
           │
           ▼
  [API Controllers] (JobController, OfferController, CustomerController, InvoiceController, ...)
           │
     ┌─────┴────────────────────────────────────────────────┐
     ▼                                                      ▼
[Services]                                            [Data Transfer Objects (DTOs)]
 ├── PosterPostingLimitService (Daily phone limit)     └── PaymentData (Strict decimal parsing)
 ├── LocationService (Haversine & bounding box)
 ├── FilterService (Query normalization & filtering)
 ├── PaymentService (Exact bcmath credit/UPI rules)
 └── InvoiceService (DomPDF generation)
     │
     ▼
[Eloquent Models] (Job, Offer, Customer, CustomerCredit, Payment, Plan, Referral, User)
     │
     ▼
[Database (MySQL / SQLite)]
```

### Key Directories
- `app/Http/Controllers/API/`: Clean RESTful JSON controllers for mobile client endpoints.
- `app/Http/Controllers/AdminAuthController.php`: Web-based admin authentication and dashboard controls.
- `app/Services/`: Stateless, testable domain services encapsulating business rules.
- `app/DTOs/`: Structured type-safe data objects (e.g. `PaymentData`).
- `app/Models/`: Eloquent models with query scopes (`scopeNearby`, `scopeActive`, `scopeWithCommonFields`).
- `config/`: App configuration including `config/posters.php` (daily posting limits).
- `database/migrations/`: Structured chronological schema definitions.
- `resources/views/`: Blade views for admin dashboard and PDF invoice templates.
- `tests/Feature/`: Comprehensive automated test suites.

---

## ⚙️ 4. Core Business Rules & Domain Logic

### A. Daily Poster Limit per Phone Number
To prevent spam, abuse, and automated flooding, the platform enforces a configurable daily limit on the number of posters created using a single phone number.

- **Service**: [`App\Services\PosterPostingLimitService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PosterPostingLimitService.php)
- **Configuration**: [`config/posters.php`](file:///c:/xampp/htdocs/postergali-web/config/posters.php)
  - `MAX_POSTERS_PER_DAY_PER_PHONE` (default: `2`)
  - `POSTER_DAILY_LIMIT_MODE` (default: `'combined'` — sum of jobs and offers posted today cannot exceed the limit; option: `'per_type'`)
  - `POSTER_DAILY_LIMIT_MESSAGE` (customizable message with `:limit` placeholder)
- **Behavior**:
  - Setting the limit to `0` or negative disables the check (unlimited).
  - Phone numbers are matched resiliently: raw input, stripped non-digits, space removal, international prefixes (`+91`, `91`, `+971`), leading zeroes, and last-10-digit matching.
  - Exceeding the limit throws a standard `ValidationException` (HTTP 422) with the configured error message.

### B. Payment & Customer Credit Wallet
Customers have an account tied to their mobile number (`Customer`) and a wallet balance (`CustomerCredit`).
- **Initial Welcome Credit**: New customers receive **1,000 complimentary credits** upon first check-in (`GET /api/v1/customers/check`).
- **Payment Types** (`PaymentData` / `PaymentService`):
  1. `FULL_UPI`: `razorpay_amount` must equal `total_amount`; `credit_amount = 0.00`.
  2. `FULL_CREDIT`: `credit_amount` must equal `total_amount`; `razorpay_amount = 0.00`.
  3. `SEMI`: Both `razorpay_amount > 0` and `credit_amount > 0`, and their exact sum equals `total_amount`.
- **Exact Decimal Arithmetic**: All monetary calculations use `bcmath` (`bcadd`, `bcsub`, `bccomp`) with 2 decimal places to avoid floating-point inaccuracies.
- **Credit Balance Deduction**: Performed inside a database transaction with `lockForUpdate()` preventing race conditions or negative balances.

### C. Geolocation & Spatial Distance Search
- **Service**: [`App\Services\LocationService`](file:///c:/xampp/htdocs/postergali-web/app/Services/LocationService.php)
- **Bounding Box Optimization**: Uses minimum/maximum latitude and longitude boundaries (`withBoundingBox`) so queries utilize composite spatial database indexes before running mathematical formulas.
- **Haversine Formula**: Calculates actual spherical distance in kilometers:
  $$\text{Distance} = 6371 \times 2 \times \arcsin\left(\sqrt{\sin^2(\Delta\text{lat}/2) + \cos(\text{lat}_1)\cos(\text{lat}_2)\sin^2(\Delta\text{lng}/2)}\right)$$
- **Distance Range Filters**: Supports min/max radius search (e.g., within 5 km, or ring searches like 5–10 km).

### D. Poster Approval & Expiry Lifecycle
1. When created via API, posters (`Job` and `Offer`) have `status = 'pending'`.
2. Admin logs into `/admin/dashboard`, reviews pending ads, and can:
   - **Approve**: Sets `status = 'approved'`, `approved_at = now()`, calculates `expires_at = approved_at + plan_duration` (e.g. 30 days, 1 day, 7 days).
   - **Reject**: Sets `status = 'rejected'`, records admin comment (`status_comment` / `status_note`).
3. Public listing/search endpoints enforce `active()` scope:
   `approved_at IS NOT NULL AND (expires_at IS NULL OR expires_at > now())`.

### E. Customer Invoices
- Customers can download formal GST/tax invoices for their ad payments via `/api/v1/customers/invoice` or `/api/v1/payments/invoice`.
- Rendered via DomPDF from `resources/views/invoices/customer_invoice.blade.php`.

### F. Three Distinct Notification Scenarios

#### 1. Ad View Milestones (100, 200, 300...)
- **Trigger**: Real-time upon viewing a poster (`JobController::show` and `OfferController::show`).
- **Service**: [`App\Services\PosterMilestoneNotificationService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PosterMilestoneNotificationService.php)
- **Tracking**: `last_view_milestone_notified` column on `jobs` and `offers` prevents duplicate notifications between milestones.
- **Config**: `config('posters.view_milestones.thresholds')` (Default: `[100, 200, 300, 400, 500, 1000]`).
- **Notification Text**:
  - Title: `🎉 Views Milestone Crossed! | व्यूज माइलस्टोन सूचना`
  - English: `🚀 Great news! Your poster ":business_name" has crossed :views views on PosterGali! Keep tracking your responses.`
  - Hindi: `🚀 खुशखबरी! PosterGali पर आपके पोस्टर ":business_name" ने :views व्यूज पूरे कर लिए हैं! नए ग्राहकों से जुड़े रहें।`

#### 2. On Poster Expiry (Using Batch)
- **Trigger**: Automated 15-min batch (`php artisan posters:check-expiry`).
- **Service**: [`App\Services\PosterExpiryNotificationService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PosterExpiryNotificationService.php)
- **Detection**: `expires_at <= now()`, `expired_notified_at IS NULL`, `status != 'rejected'`.
- **Tracking & Transition**: Stamps `expired_notified_at = now()`, automatically marks `status = 'expired'`.
- **Notification Text**:
  - Title: `⚠️ Poster Expired | पोस्टर समाप्त हो गया`
  - English: `⚠️ Your poster ":business_name" has expired today. Create a new poster on PosterGali and keep reaching more people online.`
  - Hindi: `⚠️ आपके पोस्टर ":business_name" की अवधि आज समाप्त हो गई है। नया पोस्टर बनाएं और PosterGali पर अधिक लोगों तक पहुंचना जारी रखें।`

#### 3. Day Before Poster Expiry (Using Batch)
- **Trigger**: Automated 15-min batch (`php artisan posters:check-expiry`).
- **Service**: [`App\Services\PosterExpiryNotificationService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PosterExpiryNotificationService.php)
- **Detection**: `expires_at > now() && expires_at <= now()->addHours(24)`, `day_before_expiry_notified_at IS NULL`.
- **Tracking**: Stamps `day_before_expiry_notified_at = now()`.
- **Notification Text**:
  - Title: `⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है`
  - English: `⏰ Reminder: Your poster ":business_name" expires tomorrow! Create a new poster on PosterGali to keep your business live without interruption.`
  - Hindi: `⏰ सूचना: आपका पोस्टर ":business_name" कल समाप्त हो रहा है! अपना प्रचार बिना रुके जारी रखने के लिए PosterGali पर नया पोस्टर बनाएं।`

---

## 🗄️ 5. Database Schema & Models Map

| Model | Table | Primary Purpose | Key Fields |
|---|---|---|---|
| `Job` | `jobs` | Hiring / recruitment posters | `id`, `temp_id`, `business_name`, `job_role`, `job_type`, `salary`, `phone_number`, `latitude`, `longitude`, `city`, `plan_id`, `status`, `approved_at`, `expires_at`, `view_count`, `last_view_milestone_notified`, `day_before_expiry_notified_at`, `expired_notified_at` |
| `Offer` | `offers` | Business promotions & discounts | `id`, `temp_id`, `business_name`, `offer_details`, `offer_type`, `media` (JSON), `mobile_number`, `latitude`, `longitude`, `city`, `plan_id`, `status`, `approved_at`, `expires_at`, `view_count`, `last_view_milestone_notified`, `day_before_expiry_notified_at`, `expired_notified_at` |
| `Customer` | `customers` | Mobile app users & wallet holders | `customer_id` (`PSTGL...`), `mobile`, `fcm` (push token) |
| `CustomerCredit` | `customer_credits` | Credit balance for discounts/posting | `customer_id`, `balance` (decimal 10,2) |
| `Payment` | `payments` | Audit trail of all poster payments | `transaction_id`, `customer_id`, `item_type` (`job`/`offer`), `job_or_offer_id`, `payment_type` (`FULL_UPI`, `SEMI`, `FULL_CREDIT`), `total_amount`, `razorpay_amount`, `credit_amount`, `payment_status` |
| `Plan` | `plans` | Subscription / listing tiers | `id`, `name`, `duration` (e.g. `'30 days'`), `price` |
| `Referral` | `referrals` | User referral tracking | `referrer_phone`, `referred_phone`, `status` |
| `AdSubcategory` | `ad_subcategories`| Category taxonomy mappings | `name`, `parent_category`, `is_active` |
| `User` | `users` | Admin dashboard users | `name`, `email`, `password`, `is_admin` |

---

## 📡 6. Complete API Catalog (`/api/v1`)

### Jobs
- `GET /api/v1/jobs`: Get active nearby jobs within radius.
  - Query params: `latitude` (req), `longitude` (req), `radius` (def: 5), `sub_categories`, `job_type`, `salary`, `is_expiry`, `page`, `per_page`.
- `GET /api/v1/jobs/search`: Search jobs by `device_id`, `phone_number`, or location.
- `POST /api/v1/jobs`: Create new job poster (enforces daily limit per `phone_number`, processes payment).
- `GET /api/v1/jobs/{id}`: View single job details (increments `view_count`).
- `PUT /api/v1/jobs/{id}`: Update job.
- `DELETE /api/v1/jobs/{id}`: Delete job.

### Offers
- `GET /api/v1/offers`: Get active nearby offers within radius.
  - Query params: `latitude` (req), `longitude` (req), `radius`/`distance`, `min_distance`, `max_distance`, `sub_categories`, `offer_type`, `is_expiry`, `page`, `per_page`.
- `GET /api/v1/offers/search`: Search offers by `device_id`, `mobile_number`, or location.
- `POST /api/v1/offers`: Create new offer poster (enforces daily limit per `mobile_number`, handles image/video uploads, processes payment).
- `GET /api/v1/offers/{id}`: View single offer details (increments `view_count`).
- `PUT /api/v1/offers/{id}`: Update offer.
- `DELETE /api/v1/offers/{id}`: Delete offer.

### Customers & Credits
- `GET /api/v1/customers/check`: Verify mobile number, auto-register customer, issue 1000 welcome credits, update FCM token.
- `GET /api/v1/customers/poster-ads` (and aliases `customers/my-posters`, `my-posters`): Fetch and filter customer's jobs and offers. Supports identical filters to Jobs & Offers APIs (`sub_categories`, `is_expiry`, `job_type`, `salary`, `offer_type`, `status`, `type`, distance/location, etc.).
- `GET /api/v1/customers/{customerId}/balance`: Fetch current credit wallet balance.
- `GET/POST /api/v1/customers/invoice` & `/api/v1/payments/invoice`: Generate and download PDF tax invoice.

### Categories & Plans
- `GET /api/v1/categories`: List categories and subcategories.
- `GET /api/v1/plans`: List active ad listing plans and pricing.

---

## 🔒 7. Standard API Response Structure

### Success Response
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 45,
    "per_page": 50,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Validation Error (422 Unprocessable Content)
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "phone_number": [
      "Daily poster limit reached. You can only post up to 2 posters per day."
    ]
  }
}
```

### Resource Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found."
}
```

---

## 🌐 8. Configuration & Environment Variables

| Variable | Default | Purpose |
|---|---|---|
| `MAX_POSTERS_PER_DAY_PER_PHONE` | `2` | Maximum posters a phone number can create in a single calendar day (0 = disabled) |
| `POSTER_DAILY_LIMIT_MODE` | `combined` | Limit mode: `combined` (jobs + offers) or `per_type` |
| `POSTER_DAILY_LIMIT_MESSAGE` | *Standard limit text* | Template for validation error on limit reach |
| `POSTER_EXPIRY_CRON_SCHEDULE` | `0 6 * * *` | Cron schedule expression for the poster expiry notification batch (everyday at 6:00 AM) |
| `POSTER_EXPIRY_TIMEZONE` | `Asia/Kolkata` | Timezone for the scheduled batch execution (Indian Standard Time) |
| `POSTER_EXPIRING_WINDOW_HOURS` | `24` | Lookahead window in hours to detect posters expiring today |
| `POSTER_EXPIRY_NOTIFICATION_ENABLED` | `true` | Master switch to enable/disable automated expiry notifications |
| `DB_CONNECTION` | `mysql` | Database driver (`sqlite` in phpunit testing) |
| `DB_DATABASE` | `postergali` | Database name |
| `FIREBASE_CREDENTIALS` | `storage/app/firebase/...` | Path to Firebase service account JSON |

---

## 🧪 9. Running Batch Commands & Tests

### Automated Batch Job
```bash
# Run poster expiry checker manually
php artisan posters:check-expiry

# Test in dry-run mode (no notifications sent, no DB modified)
php artisan posters:check-expiry --dry-run

# View registered schedules
php artisan schedule:list
```

### Automated Tests
The test suite runs with SQLite in-memory and `RefreshDatabase`. No live MySQL database is needed to run tests.

```bash
# Run all automated tests
php vendor/phpunit/phpunit/phpunit

# Run specific feature tests
php vendor/phpunit/phpunit/phpunit tests/Feature/PosterExpiryBatchTest.php
php vendor/phpunit/phpunit/phpunit tests/Feature/PosterDailyLimitTest.php
php vendor/phpunit/phpunit/phpunit tests/Feature/PaymentModelAndBusinessRulesTest.php
php vendor/phpunit/phpunit/phpunit tests/Feature/PaymentCreditFlowTest.php
php vendor/phpunit/phpunit/phpunit tests/Feature/JobAndOfferFiltersComprehensiveTest.php
```
php vendor/phpunit/phpunit/phpunit tests/Feature/JobAndOfferFiltersComprehensiveTest.php
```
