# 🚀 PosterGali `postergali-alpha` Batch System — Operational Guide

This guide covers the architecture, configuration, monitoring, manual execution, service control, and live debugging of the **`postergali-alpha`** automated batch system in **PosterGali**.

---

## 1. Overview: What Batch Jobs Run?

The system runs **one unified master batch command (`postergali-alpha`)** that executes two dedicated functions:

| Function | Task | Trigger Condition | Notification / Action |
|---|---|---|---|
| **Function 1 (f1)** | **Day-1 Before Expiry** | Poster expires within next 24 hours (`now() < expires_at <= now + 24h`) and unnotified | Reminder to renew/create new poster (`day_before_expiry`) |
| **Function 2 (f2)** | **Expiring Today / Expired** | Poster expiry date has arrived/passed (`expires_at <= now()`) and unnotified | Expiry alert (`on_expiry`) + status transitioned to `expired` |

> [!NOTE]
> Ad view milestone notifications (100, 200, 300+ views) are dispatched in real-time when users view ads via the show API (`PosterMilestoneNotificationService`).

---

## 2. Schedule & Cadence

- **Default Schedule:** Runs **everyday at 6:00 AM India Standard Time** (`0 6 * * *` in `Asia/Kolkata`)
- **Single Source of Truth (Constants):**
  - Defined on [`PostergaliAlphaBatchService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PostergaliAlphaBatchService.php):
    ```php
    public const BATCH_NAME       = 'postergali-alpha';
    public const DEFAULT_SCHEDULE = '0 6 * * *'; // Everyday at 6:00 AM IST
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata'; // Indian Standard Time (IST)
    ```
- **Customizable via `config/posters.php` or `.env`:**
  ```env
  POSTERGALI_ALPHA_SCHEDULE="0 6 * * *"
  POSTERGALI_ALPHA_TIMEZONE="Asia/Kolkata"
  POSTERGALI_ALPHA_ENABLED=true
  POSTER_EXPIRING_WINDOW_HOURS=24
  ```
- Registered in [`routes/console.php`](file:///c:/xampp/htdocs/postergali-web/routes/console.php):
  ```php
  $alphaSchedule = PostergaliAlphaBatchService::getSchedule();
  $alphaTimezone = PostergaliAlphaBatchService::getTimezone();

  if (PostergaliAlphaBatchService::isEnabled()) {
      Schedule::command('postergali-alpha')
          ->cron($alphaSchedule)
          ->timezone($alphaTimezone)
          ->withoutOverlapping()
          ->runInBackground();
  }
  ```

---

## 3. How to Run Manually

### A. Run Safely Without Modifying Data or Sending Push Notifications (Dry-Run)
Check what the batch would process right now:
```bash
php artisan postergali-alpha --dry-run
```

### B. Execute the Live Batch Immediately
To process and dispatch notifications right away:
```bash
php artisan postergali-alpha
```

---

## 4. How the Scheduled Runner Works (VPS Crontab & Daemon)

### On VPS (Linux / Ubuntu):
The Laravel scheduler is invoked every minute via Linux crontab:

```bash
# Check current crontab
crontab -l

# Edit crontab
crontab -e
```

Standard crontab entry:
```bash
* * * * * cd /var/www/postergali-web && php artisan schedule:run >> /dev/null 2>&1
```

Or when running the daemon in background:
```bash
nohup php artisan schedule:work > storage/logs/schedule.log 2>&1 &
```

---

## 5. How to Stop or Pause the Batch

Set the toggle in `.env`:
```env
POSTERGALI_ALPHA_ENABLED=false
```
Then clear config cache:
```bash
php artisan config:clear
```

---

## 6. How to Monitor in Admin Panel

Navigate to:
```
http://your-domain/admin/batch-monitor
```

### What is shown on the Batch Monitor:
1. **Engine & Status:**
   - **Batch Name:** `postergali-alpha`
   - **Status Badge:** Active (Green with pulsing dot) / Disabled
   - **Configured Schedule Time:** e.g. `Everyday at 6:00 AM IST`
   - **Next Scheduled Run:** Precise IST timestamp & live animated countdown timer (`⏳ in 10h 15m`)
   - **Last Batch Run:** Time in IST & relative time (e.g. `2 hours ago`), execution duration (`ms`), total sent
2. **Recent Runs History Table:**
   - Displays the last 15 batch runs saved in `batch_run_logs` table
   - Columns: `Execution Time (IST)`, `Duration`, `Day 1 Before (Jobs/Offers)`, `Expiring Today (Jobs/Offers)`, `Sent`, `Skipped`, `Status`

---

## 7. Database Tables Used

| Table | Role |
|---|---|
| `batch_run_logs` | Audit trail of every batch execution (`batch_name = 'postergali-alpha'`) |
| `jobs` | Job posters table (`expires_at`, `day_before_expiry_notified_at`, `expired_notified_at`, `status`) |
| `offers` | Offer posters table (`expires_at`, `day_before_expiry_notified_at`, `expired_notified_at`, `status`) |
| `notifications` | Stores `device_id`, `fcm_tocken`, `mobile` mappings |
| `customers` | Stores customer profiles and `fcm` tokens |
| `referrals` / `customer_credits` | Handles referral status and credits awarded with push notification on ad approval |
