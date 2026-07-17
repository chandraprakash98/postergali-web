# Firebase FCM Notification Setup on VPS

## Common Issues & Solutions

### 1. **Firebase Service Account JSON Not on VPS** ⚠️ (MOST COMMON)

**Problem**: The `storage/app/firebase/firebase-service-account.json` file is missing on VPS

**Solution**:
```bash
# On your local machine, upload the Firebase service account file
scp storage/app/firebase/firebase-service-account.json root@your_vps_ip:/var/www/postergali-web/storage/app/firebase/

# Or via SFTP/FTP, manually upload the file

# On VPS, verify file exists
ls -la /var/www/postergali-web/storage/app/firebase/
```

### 2. **File Permissions Issue**

**Problem**: Firebase service account file can't be read by PHP

**Solution**:
```bash
# SSH into VPS
ssh root@your_vps_ip
cd /var/www/postergali-web

# Set correct permissions
chmod 644 storage/app/firebase/firebase-service-account.json
chown www-data:www-data storage/app/firebase/firebase-service-account.json

# Verify
ls -la storage/app/firebase/firebase-service-account.json
```

### 3. **Environment Variable Not Set**

**Problem**: `FIREBASE_CREDENTIALS` env variable not configured on VPS

**Solution**:
```bash
# Edit .env on VPS
nano /var/www/postergali-web/.env

# Add or verify this line exists:
FIREBASE_CREDENTIALS=storage/app/firebase/firebase-service-account.json

# Save (Ctrl+X, Y, Enter)

# Clear Laravel config cache
php artisan config:clear
php artisan cache:clear
```

### 4. **Network/Firewall Blocking Firebase API**

**Problem**: VPS firewall blocks outgoing HTTPS to Firebase servers

**Solution**:
```bash
# Test connectivity to Firebase
curl -I https://fcm.googleapis.com

# If it fails, allow outbound HTTPS
ufw allow out 443/tcp

# Verify rules
ufw status verbose
```

### 5. **Device ID Not Being Sent from Mobile**

**Problem**: Mobile app not sending device_id in requests

**Check in database**:
```bash
# SSH to VPS MySQL
mysql -u postergali -p postergali

# Check if device_id is stored
SELECT id, device_id, created_at FROM jobs LIMIT 5;
SELECT id, device_id, created_at FROM offers LIMIT 5;

# Check for NULL device_id
SELECT COUNT(*) as count FROM jobs WHERE device_id IS NULL;
SELECT COUNT(*) as count FROM offers WHERE device_id IS NULL;
```

**Solution**: Ensure mobile app includes `device_id` or `fcm_token` header in API requests

### 6. **Check Laravel Logs for Errors**

**On VPS**:
```bash
# Watch logs in real-time
tail -f /var/www/postergali-web/storage/logs/laravel.log

# Search for FCM errors
grep -i "fcm\|firebase" /var/www/postergali-web/storage/logs/laravel.log | tail -20

# Check for permission errors
grep -i "permission\|denied" /var/www/postergali-web/storage/logs/laravel.log | tail -20
```

### 7. **Test FCM Manually on VPS**

Create a test script:

```bash
# SSH to VPS
ssh root@your_vps_ip
cd /var/www/postergali-web

# Create test script
php artisan tinker

# In Tinker shell, run:
>>> use Kreait\Firebase\Factory;
>>> use Kreait\Firebase\Messaging\CloudMessage;
>>> use Kreait\Firebase\Messaging\Notification as FcmNotification;
>>> 
>>> $serviceAccountPath = base_path('storage/app/firebase/firebase-service-account.json');
>>> $factory = (new Factory())->withServiceAccount($serviceAccountPath);
>>> $messaging = $factory->createMessaging();
>>> 
>>> // Replace with a real device token from your mobile app
>>> $message = CloudMessage::withTarget('token', 'YOUR_DEVICE_TOKEN_HERE')
...     ->withNotification(FcmNotification::create('Test', 'This is a test notification'));
>>> 
>>> $messaging->send($message);
>>> 
>>> // You should see the token ID if successful
>>> exit
```

If this works, it means Firebase is configured correctly, but issue is elsewhere.

---

## Complete VPS Firebase Setup Checklist

### ✅ Step 1: Prepare Firebase Service Account Locally
```bash
# On your local machine
# 1. Go to Firebase Console: https://console.firebase.google.com
# 2. Select your project
# 3. Go to Project Settings > Service Accounts
# 4. Click "Generate New Private Key"
# 5. This downloads: postergali-xxx.json
# 6. Save it in: storage/app/firebase/firebase-service-account.json
```

### ✅ Step 2: Upload to VPS
```bash
# From your local machine
scp storage/app/firebase/firebase-service-account.json root@your_vps_ip:/var/www/postergali-web/storage/app/firebase/
```

### ✅ Step 3: Configure on VPS
```bash
# SSH to VPS
ssh root@your_vps_ip

# Navigate to project
cd /var/www/postergali-web

# Set permissions
chmod 644 storage/app/firebase/firebase-service-account.json
chown www-data:www-data storage/app/firebase/firebase-service-account.json

# Update .env
nano .env
# Make sure this exists:
# FIREBASE_CREDENTIALS=storage/app/firebase/firebase-service-account.json

# Clear cache
php artisan config:clear
php artisan cache:clear

# Restart PHP-FPM
systemctl restart php8.2-fpm
```

### ✅ Step 4: Create Artisan Command for Testing
```bash
# Create a test command
php artisan make:command SendTestNotification

# Edit: app/Console/Commands/SendTestNotification.php
```

Add this code:
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class SendTestNotification extends Command
{
    protected $signature = 'test:notification {token : The FCM device token}';
    protected $description = 'Send a test FCM notification';

    public function handle()
    {
        try {
            $token = $this->argument('token');
            
            $firebaseCredentials = env('FIREBASE_CREDENTIALS', 'storage/app/firebase/firebase-service-account.json');
            $serviceAccountPath = base_path($firebaseCredentials);
            
            if (!file_exists($serviceAccountPath)) {
                $this->error("Firebase credentials not found at: {$serviceAccountPath}");
                return 1;
            }
            
            $factory = (new Factory())->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(FcmNotification::create(
                    'Test Notification ✓',
                    'If you see this, FCM is working!'
                ))
                ->withData(['test' => 'true']);

            $result = $messaging->send($message);
            
            $this->info("✅ Notification sent successfully!");
            $this->info("Message ID: {$result}");
            
            return 0;
        } catch (\Throwable $e) {
            $this->error("❌ Failed to send notification: " . $e->getMessage());
            return 1;
        }
    }
}
```

Run test:
```bash
# On VPS, replace with real device token from mobile app
php artisan test:notification "your_device_token_here"
```

---

## Mobile App Requirements

### For iOS/Android to receive notifications:

1. **Device ID Must Be Set**:
   ```javascript
   // In mobile app, after getting FCM token
   const deviceToken = await getDeviceToken(); // From FCM/Firebase
   
   // Send in API request headers
   headers: {
       'Authorization': 'Bearer ' + authToken,
       'device_id': deviceToken,  // ← IMPORTANT
   }
   ```

2. **Permissions Required**:
   - Android: `android.permission.POST_NOTIFICATIONS`
   - iOS: User must grant notification permission in app

3. **App Must Be Running or in Background**:
   - Foreground: Direct notification display
   - Background: System tray notification (requires proper FCM implementation)
   - Killed: May not receive (depends on device)

---

## Debugging Checklist

Before contacting support, verify:

- [ ] Firebase service account JSON uploaded to VPS
- [ ] File permissions: `644` and owner `www-data:www-data`
- [ ] `.env` has `FIREBASE_CREDENTIALS` variable set
- [ ] Laravel logs show no Firebase errors
- [ ] Can connect to fcm.googleapis.com from VPS
- [ ] Mobile app sending device_id in requests
- [ ] Device tokens stored in database (not NULL)
- [ ] Mobile app has notification permissions granted
- [ ] FCM test command works with valid token

---

## If Still Not Working

1. **Check Laravel logs**:
   ```bash
   tail -50 /var/www/postergali-web/storage/logs/laravel.log
   ```
   Share the error message

2. **Verify credentials file**:
   ```bash
   cat /var/www/postergali-web/storage/app/firebase/firebase-service-account.json | head -5
   ```

3. **Test PHP Firebase library**:
   ```bash
   cd /var/www/postergali-web
   php -r "require 'vendor/autoload.php'; echo 'Composer OK';"
   ```

4. **Verify device token format**:
   - Should be long string (152+ characters)
   - No spaces or special characters
