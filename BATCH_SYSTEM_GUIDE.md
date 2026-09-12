# 🚀 PosterGali Batch Notification System — Operational & Architecture Guide

This guide covers the architecture, configuration, monitoring, manual execution, service control, and live debugging of the automated batch jobs in **PosterGali**.

---

## 1. Overview: What Batch Jobs Run?

The system runs **one unified master batch command** that handles 3 notification tasks in a single run:

| Task | Trigger Condition | Notification / Action | Config Key |
|---|---|---|---|
| **1. Views Milestone** | Poster reaches `>= 100`, `>= 500` views (configurable) | Congratulatory push notification to owner (`view_milestone`) | `POSTER_VIEW_MILESTONE_THRESHOLDS` |
| **2. Day-Before-Expiry** | Poster expires within next 24 hours (`expires_at <= now + 24h`) | Reminder to renew/create new poster (`day_before_expiry`) | `POSTER_EXPIRING_WINDOW_HOURS` |
| **3. On-Expiry** | Poster has expired (`expires_at <= now()`) | Expiry alert (`on_expiry`) + status updated to `expired` | `POSTER_EXPIRY_NOTIFICATION_ENABLED` |

---

## 2. Schedule & Cadence

- **Default Schedule:** Runs **everyday at 6:00 AM India Standard Time** (`0 6 * * *` in `Asia/Kolkata`)
- **Single Source of Truth (Constants):**
  - Defined on [`PosterExpiryNotificationService`](file:///c:/xampp/htdocs/postergali-web/app/Services/PosterExpiryNotificationService.php):
    ```php
    public const DEFAULT_SCHEDULE = '0 6 * * *'; // Everyday at 6:00 AM IST
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata'; // Indian Standard Time (IST)
    ```
- **Configurable in one place via `config/posters.php` or `.env`:**
  ```env
  POSTER_EXPIRY_CRON_SCHEDULE="0 6 * * *"
  POSTER_EXPIRY_TIMEZONE="Asia/Kolkata"
  POSTER_EXPIRING_WINDOW_HOURS=24
  POSTER_EXPIRY_NOTIFICATION_ENABLED=true
  POSTER_VIEW_MILESTONE_THRESHOLDS="100,200,500"
  ```
- Registered centrally in [`routes/console.php`](file:///c:/xampp/htdocs/postergali-web/routes/console.php):
  ```php
  $expirySchedule = PosterExpiryNotificationService::getSchedule();
  $expiryTimezone = PosterExpiryNotificationService::getTimezone();

  Schedule::command('posters:check-expiry')
      ->cron($expirySchedule)
      ->timezone($expiryTimezone)
      ->withoutOverlapping()
      ->runInBackground();
  ```

---

## 3. How to Run Manually

### A. Run Safely Without Sending Notifications or Modifying Data (Dry-Run)
Use this command to check what the batch would do right now:
```bash
php artisan posters:check-expiry --dry-run
```

### B. Execute the Live Batch Immediately
To manually process and dispatch notifications right away:
```bash
php artisan posters:check-expiry
```

### C. Test FCM Token & View Milestone Directly
To verify Firebase connectivity and test views milestone for a specific poster:
```bash
# Test FCM credentials and direct push to a token:
php artisan test:fcm <YOUR_DEVICE_TOKEN>

# Set views to 100 on Job #12 and test milestone trigger:
php artisan test:fcm <YOUR_DEVICE_TOKEN> --link-job=12 --views=100

# Check all pending milestones across the DB:
php artisan test:fcm --check-pending
```

---

## 4. How the Scheduled Runner Works (Crontab & Daemon)

### On VPS (Linux / Ubuntu):
The Laravel scheduler is invoked every minute via Linux crontab:

```bash
# Check current crontab
crontab -l

# Edit crontab
crontab -e
```

The standard crontab entry:
```bash
* * * * * cd /var/www/postergali-web && php artisan schedule:run >> /dev/null 2>&1
```

Alternatively, if running the worker daemon in background using `schedule:work`:
```bash
# Start the scheduler daemon in background
nohup php artisan schedule:work > storage/logs/schedule.log 2>&1 &
```

---

## 5. How to Stop or Pause the Batch

### Method 1: Instant Pause via `.env` (No code change needed)
Set the toggle to `false` in `.env`:
```env
POSTER_EXPIRY_NOTIFICATION_ENABLED=false
```
Then clear config cache:
```bash
php artisan config:clear
```
The scheduled job will record `disabled` in the logs and exit immediately without sending anything.

### Method 2: Stop the Background Process / Cron
```bash
# If running schedule:work daemon, find the PID:
ps aux | grep "artisan schedule"

# Kill the process:
kill -9 <PID>

# If using systemd (e.g. laravel-worker.service):
sudo systemctl stop laravel-worker
```

---

## 6. How to View Live Logs

### A. View General Laravel Logs in Real Time
```bash
tail -f storage/logs/laravel.log
```

### B. Filter Specifically for Batch & FCM Entries
```bash
tail -f storage/logs/laravel.log | grep -E "FCM|Milestone|Batch"
```

### C. View the Last 50 Log Lines
```bash
tail -n 50 storage/logs/laravel.log
```

### D. View Scheduler Log (If running schedule:work with log redirection)
```bash
tail -f storage/logs/schedule.log
```

---

## 7. How to Monitor in Admin Panel

Navigate to:
```
http://your-server-domain/admin/batch-monitor
```

### What you see on the Batch Monitor:
1. **Status Bar:**
   - **System Status:** Active (Green) / Inactive
   - **Schedule:** Everyday at 6:00 AM IST (`0 6 * * *`)
   - **Last Batch Run:** Time in Indian Standard Time (IST) & Relative Time (e.g. `1 min ago`)
   - **Next Scheduled Run:** Precise IST timestamp & live animated countdown timer (`in 01:45`)
   - **Current Server Time:** Synced in IST (`Asia/Kolkata`)

2. **Batch Run History Table:**
   - Displays the last 50 batch runs saved in `batch_run_logs` table
   - Columns: `Run Time (IST)`, `Duration (ms)`, `Day Before (Jobs/Offers)`, `On Expiry (Jobs/Offers)`, `Notifications Sent`, `Skipped (No Token)`, `Status`

3. **Auto-Refresh:**
   - The status bar polls `/admin/batch-status` every 15 seconds to update the countdown and status smoothly without reloading the entire page.

---

## 8. Database Tables Used

| Table | Role |
|---|---|
| `batch_run_logs` | Audit trail of every batch execution (duration, sent count, skipped count, timestamp) |
| `jobs` | Job posters table (`view_count`, `last_view_milestone_notified`, `expires_at`, `status`) |
| `offers` | Offer posters table (`view_count`, `last_view_milestone_notified`, `expires_at`, `status`) |
| `notifications` | Stores `device_id`, `fcm_tocken`, `mobile` mappings |
| `customers` | Stores customer profiles and `fcm` tokens |

---

## 9. Troubleshooting Quick Checklist

| Issue | Resolution |
|---|---|
| **Batch status shows 500 error** | Run `php artisan route:clear && php artisan view:clear && php artisan config:clear` on VPS |
| **Push notifications not reaching device** | Check if `storage/app/firebase/firebase-service-account.json` exists and is readable |
| **Views milestone not triggering** | Verify `view_count >= 100` and `last_view_milestone_notified < 100` in the database |
| **Token missing warning** | Ensure user phone or device ID is registered in `customers` or `notifications` table |
