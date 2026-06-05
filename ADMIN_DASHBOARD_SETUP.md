# Admin Dashboard Setup Guide

## 📋 Overview

Complete admin dashboard with professional UI matching your wireframe. Features include:
- Admin login with email/password
- Dashboard with stats cards
- All Ads view (Jobs + Offers combined)
- Pending Ads (status: pending)
- Live Ads (status: approved)
- Expired Ads (status: rejected)
- Pricing Info (plans management)
- Responsive design with sidebar navigation

---

## 🚀 Quick Start

### 1. Run Migrations
```bash
php artisan migrate
```

This will create:
- `is_admin` column in users table

### 2. Seed Database
```bash
php artisan db:seed
```

This will create:
- Admin user: `admin@example.com` / `admin123`
- 9 sample jobs with various statuses
- 5 sample offers with various statuses
- 3 subscription plans

### 3. Access Dashboard
```
Login: http://localhost:8000/admin/login
Dashboard: http://localhost:8000/admin/dashboard
```

---

## 📁 Files Created/Modified

### New Controllers
- `app/Http/Controllers/AdminAuthController.php` - Complete admin CRUD

### New Views
- `resources/views/admin/login.blade.php` - Professional login page
- `resources/views/admin/dashboard.blade.php` - Main dashboard

### New Middleware
- `app/Http/Middleware/IsAdmin.php` - Authorization checks

### New Migrations
- `database/migrations/2026_06_05_000000_add_is_admin_to_users_table.php`

### New Seeders
- `database/seeders/AdminSeeder.php`
- `database/seeders/SampleDataSeeder.php`

### Modified Files
- `routes/web.php` - Admin routes
- `bootstrap/app.php` - Middleware registration
- `app/Models/User.php` - Added is_admin to fillable
- `database/seeders/DatabaseSeeder.php` - Added seeders

---

## 🛣️ Routes

```
GET  /admin/login              - Login page
POST /admin/login              - Process login
GET  /admin/dashboard          - Main dashboard (with all ads)
GET  /admin/all-ads            - All ads view
GET  /admin/pending-ads        - Pending verification ads
GET  /admin/live-ads           - Approved/Live ads
GET  /admin/expired-ads        - Rejected/Expired ads
GET  /admin/pricing-info       - Subscription plans
POST /admin/logout             - Logout
```

---

## 🔐 Authentication

### Default Admin Credentials
- **Email**: admin@example.com
- **Password**: admin123

### Adding New Admin Users

**Via Database:**
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin Name',
    'email' => 'newadmin@example.com',
    'password' => Hash::make('securepassword'),
    'is_admin' => true,
]);
```

**Via Artisan Tinker:**
```bash
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => Hash::make('pass'), 'is_admin' => true])
```

---

## 📊 Dashboard Features

### Stats Cards
Shows real-time counts:
- **Pending Verification**: Jobs + Offers with status='pending'
- **Live Ads**: Jobs + Offers with status='approved'
- **Expired Ads**: Jobs + Offers with status='rejected'

### Ad Listings
Each ad shows:
- **AD ID**: Formatted as JOB-001, OFF-001, etc.
- **Business Name**: From jobs/offers table
- **Phone Number**: Mobile number
- **City**: Location
- **Status Badge**: Color-coded (Yellow/Green/Red)
- **Date Posted**: Created date
- **View Button**: Ready for detail implementation

### Pricing Table
Displays all plans with:
- Plan ID
- Plan Title
- Duration
- Price

---

## 🎨 UI Customization

### Status Badge Colors
```css
Pending Verification → Yellow (#fff3cd)
Live/Approved       → Green (#d4edda)
Rejected/Expired    → Red (#f8d7da)
```

### Sidebar Navigation
Edit menu items in `resources/views/admin/dashboard.blade.php`:
```php
<a href="{{ route('admin.allAds') }}" class="menu-item">
    <span class="menu-icon">📋</span>
    All Ads
</a>
```

---

## 🔧 Advanced Configuration

### Data Mapping

The controller maps data from Job and Offer models:

```php
Job fields used:
- id → AD ID (formatted as JOB-001)
- business_name
- phone_number
- city
- status (pending/approved/rejected)
- created_at → date posted

Offer fields used:
- id → AD ID (formatted as OFF-001)
- business_name
- mobile_number
- city
- status (pending/approved/rejected)
- created_at → date posted
```

### Adding New Statuses

To add new ad statuses:

1. Update the controller methods or add new ones
2. Add CSS classes for the status badge:
```css
.status-newstatus {
    background-color: #colorcode;
    color: #textcolor;
}
```

---

## 📱 Responsive Design

Dashboard is fully responsive:
- **Desktop**: Full sidebar navigation
- **Tablet**: Sidebar visible with compact spacing
- **Mobile**: Sidebar toggles/collapses

---

## ⚠️ Troubleshooting

### "Unauthorized Access" Error
- Ensure user has `is_admin = true` in database
- Check user is logged in correctly

### Stats Not Updating
- Run `php artisan cache:clear` if using caching
- Verify job/offer records have correct status values

### Migration Error
- Ensure migration file is named correctly
- Run `php artisan migrate:reset` if needed, then `migrate` again

### Table Empty
- Run seeder: `php artisan db:seed`
- Or manually create job/offer records

---

## 🎯 Next Steps

1. **View Details Page**: Create ad detail view with full information
2. **Approve/Reject Actions**: Add status update functionality
3. **Search & Filter**: Add search box and filters
4. **Pagination**: Implement pagination for large datasets
5. **Export**: Add CSV/PDF export functionality
6. **Analytics**: Add charts and graphs
7. **Email Notifications**: Send alerts when ads are approved/rejected

---

## 📝 Example SQL Queries

### View Admin Users
```sql
SELECT * FROM users WHERE is_admin = 1;
```

### Count Ads by Status
```sql
SELECT status, COUNT(*) as count 
FROM jobs 
GROUP BY status;
```

### Get Pending Ads with Details
```sql
SELECT 'JOB' as type, id, business_name, phone_number as contact, city, status, created_at
FROM jobs WHERE status = 'pending'
UNION
SELECT 'OFFER' as type, id, business_name, mobile_number as contact, city, status, created_at
FROM offers WHERE status = 'pending'
ORDER BY created_at DESC;
```

---

## 🆘 Support

For issues or questions:
1. Check controller logic in `AdminAuthController.php`
2. Verify middleware in `bootstrap/app.php`
3. Review routes in `routes/web.php`
4. Check database schema with `php artisan migrate:status`
