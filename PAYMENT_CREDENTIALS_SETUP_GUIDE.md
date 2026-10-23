# 🔐 PosterGali Payment Gateway & Credentials Setup Guide

A complete, step-by-step walkthrough for locating, generating, and configuring all required credentials for **Razorpay** and **Apple In-App Purchase (IAP)** in PosterGali.

---

## 📋 Quick Setup Checklist

| Service | Environment Variable | Required For | Where to Obtain |
|---|---|---|---|
| **Razorpay** | `RAZORPAY_KEY_ID` | API authentication & Checkout | [Razorpay Dashboard &rarr; API Keys](https://dashboard.razorpay.com/#/app/keys) |
| **Razorpay** | `RAZORPAY_KEY_SECRET` | Cryptographic signature verification | [Razorpay Dashboard &rarr; API Keys](https://dashboard.razorpay.com/#/app/keys) |
| **Razorpay** | `RAZORPAY_WEBHOOK_SECRET` | Verifying async webhook payloads | [Razorpay Dashboard &rarr; Webhooks](https://dashboard.razorpay.com/#/app/webhooks) |
| **Apple IAP** | `APPLE_BUNDLE_ID` | Validating purchase origin | [Apple Developer &rarr; Identifiers](https://developer.apple.com/account/resources/identifiers/list) |
| **Apple IAP** | `APPLE_ENVIRONMENT` | Sandbox vs. Live Store routing | Set to `Sandbox` or `Production` |
| **Apple IAP** | `APPLE_ISSUER_ID` | App Store Server API authentication | [App Store Connect &rarr; Users & Access &rarr; Integrations](https://appstoreconnect.apple.com/access/users) |
| **Apple IAP** | `APPLE_KEY_ID` | App Store Server API key identifier | [App Store Connect &rarr; Users & Access &rarr; Integrations](https://appstoreconnect.apple.com/access/users) |
| **Apple IAP** | `APPLE_PRIVATE_KEY` | JWS generation & Apple server verification | Downloaded `.p8` file from App Store Connect |

---

## 1. Razorpay Setup Guide (Android & Web Payments)

Razorpay handles Indian domestic payments via UPI, Credit/Debit Cards, Net Banking, and Wallets.

```mermaid
graph LR
    A["⚙️ Razorpay Dashboard"] -->|"Settings → API Keys"| B["Key ID & Secret"]
    A -->|"Settings → Webhooks"| C["Webhook Secret & URL"]
    B --> D["📄 .env File"]
    C --> D
```

### Step 1.1: Generate API Key ID & Secret

1. Log in to the **[Razorpay Dashboard](https://dashboard.razorpay.com/)**.
2. Select your environment using the toggle at the top navigation bar:
   - **Test Mode:** For staging and mobile development.
   - **Live Mode:** For production real-money payments.
3. In the left-hand menu, navigate to:
   > **Settings** (or bottom-left gear icon) &rarr; **API Keys**
4. Click **Generate Key** (or **Regenerate Key** if you already have an existing key).
5. A popup dialog will display your credentials:
   - **Key ID:** Starts with `rzp_test_...` or `rzp_live_...`.
     ```env
     RAZORPAY_KEY_ID=rzp_live_xxxxxxxxxxxxxxxx
     ```
   - **Key Secret:** A 24-character secret key.
     ```env
     RAZORPAY_KEY_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx
     ```
6. > [!WARNING]
   > The **Key Secret** is shown **only once**. Copy and store it immediately in your secure password vault.

---

### Step 1.2: Configure Webhook & Webhook Secret

Webhooks ensure that if a customer completes payment via their UPI app (PhonePe, Google Pay, Paytm) but closes the app before redirecting back, the credits are still credited automatically.

1. In the Razorpay sidebar, go to:
   > **Settings** &rarr; **Webhooks**
2. Click **+ Add New Webhook**.
3. In the **Webhook URL** field, enter your production or staging API endpoint:
   ```
   https://your-domain.com/api/v1/credits/webhooks/razorpay
   ```
4. In the **Secret** field, enter a secure random secret string of your choice (e.g. 24–32 alphanumeric characters):
   - Example: `PosterGaliSecretRzpWebhook2026`
   - Copy this value into your `.env`:
     ```env
     RAZORPAY_WEBHOOK_SECRET=PosterGaliSecretRzpWebhook2026
     ```
5. In the **Active Events** list, check the following events:
   - `payment.captured` *(Triggered when payment is successfully debited)*
   - `payment.failed` *(Triggered when payment fails)*
   - `order.paid` *(Triggered when an order is completely paid)*
6. Set **Alert Email** to your engineering contact.
7. Click **Create Webhook**.

---

## 2. Apple In-App Purchase Setup Guide (iOS)

Apple requires digital consumable credits on iOS to use Apple In-App Purchase (StoreKit 2).

```mermaid
graph LR
    A["🍏 App Store Connect"] -->|"Users & Access → Integrations"| B["App Store Server API Key (.p8)"]
    A -->|"App Information"| C["Server Notifications V2"]
    D["🍏 Apple Developer Portal"] -->|"Certificates & Identifiers"| E["Bundle ID"]
    B --> F["📄 .env File"]
    C --> F
    E --> F
```

---

### Step 2.1: Verify Bundle Identifier

1. Open the **[Apple Developer Portal &rarr; Identifiers](https://developer.apple.com/account/resources/identifiers/list)**.
2. Select your App ID.
3. Locate the **Bundle ID** (e.g. `com.postergali.postergali`).
4. Set this in `.env`:
   ```env
   APPLE_BUNDLE_ID=com.postergali.postergali
   ```

---

### Step 2.2: Generate App Store Server API Key (`.p8`)

The App Store Server API key authorizes your backend to cryptographically verify customer receipts, track transaction status, and handle refunds with Apple.

1. Log in to **[App Store Connect](https://appstoreconnect.apple.com/)**.
2. Click on **Users and Access** from the top menu.
3. Select the **Integrations** tab (or **Keys** tab).
4. In the left sidebar, under **Key Type**, click **In-App Purchase** (or **App Store Server API**).
5. Click the **+** (Generate In-App Purchase Key) button:
   - **Key Name:** Enter a recognizable name, e.g., `PosterGali Backend Production`.
   - Click **Generate**.
6. Retrieve your credentials from the table:
   - **Issuer ID:** Located near the top of the page (a UUID formatted string, e.g., `57246542-96fe-1a63-e053-0824d011072a`).
     ```env
     APPLE_ISSUER_ID=57246542-96fe-1a63-e053-0824d011072a
     ```
   - **Key ID:** Listed in the table row for your newly created key (a 10-character alphanumeric string, e.g., `2X9R427ADR`).
     ```env
     APPLE_KEY_ID=2X9R427ADR
     ```
7. **Download the Private Key:**
   - In that same table row, click **Download API Key** (or `Download In-App Purchase Key`).
   - The file will be named like `SubscriptionKey_2X9R427ADR.p8`.
   - > [!IMPORTANT]
     > Apple permits downloading this `.p8` file **only once**. Save a secure backup copy.

---

### Step 2.3: Format `APPLE_PRIVATE_KEY` in `.env`

Open the downloaded `.p8` file in any text editor (VS Code, Notepad, or Vim). It will look like this:

```text
-----BEGIN PRIVATE KEY-----
MIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQgEXAMPLEKEYDATA12
34567890abcdefghijklmnopqrstuvwxyz+ABCDEFGH/IJKLMNOPQRSTUVWXYZ01
23456789==
-----END PRIVATE KEY-----
```

In your `.env` file, wrap the key in double quotes and replace newlines with `\n`, or paste it as a multi-line block:

```env
APPLE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nMIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQgEXAMPLEKEYDATA12\n34567890abcdefghijklmnopqrstuvwxyz+ABCDEFGH/IJKLMNOPQRSTUVWXYZ01\n23456789==\n-----END PRIVATE KEY-----"
```

---

### Step 2.4: Configure App Store Server Notifications V2 (Webhook)

When a customer requests an App Store refund, Apple automatically notifies this webhook so the server can append a compensating reversal and prevent credit fraud.

1. In **App Store Connect**, open **My Apps** &rarr; select **PosterGali**.
2. In the left-hand navigation, click **App Information**.
3. Scroll down to the **App Store Server Notifications** section.
4. Click **Set Up URL**:
   - **Production Server URL:**
     ```
     https://your-domain.com/api/v1/credits/webhooks/apple
     ```
   - **Sandbox Server URL:**
     ```
     https://staging.your-domain.com/api/v1/credits/webhooks/apple
     ```
   - **Notification Version:** Select **Version 2 Notifications**.
5. Click **Save**.

---

### Step 2.5: Verify Consumable In-App Purchase Products

In **App Store Connect &rarr; PosterGali &rarr; Subscriptions & In-App Purchases**, verify that the following 5 Consumable Products are created and match your database configuration:

| Product Reference Name | Product ID | Type | Configured Price |
|---|---|---|---|
| Starter Pack | `com.postergali.credits.100` | Consumable | Tier 1 (₹99.00) |
| Value Pack | `com.postergali.credits.220` | Consumable | Tier 2 (₹199.00) |
| Popular Pack | `com.postergali.credits.600` | Consumable | Tier 5 (₹499.00) |
| Pro Pack | `com.postergali.credits.1250` | Consumable | Tier 10 (₹999.00) |
| Business Pack | `com.postergali.credits.2600` | Consumable | Tier 20 (₹1999.00) |

---

## 3. Environment Variables Reference File

Copy and paste this section into your [`.env`](file:///c:/xampp/htdocs/postergali-web/.env) file:

```env
# ==============================================================================
# RAZORPAY PAYMENT GATEWAY
# ==============================================================================
# Key ID from Razorpay Dashboard -> Settings -> API Keys (rzp_test_... or rzp_live_...)
RAZORPAY_KEY_ID=rzp_live_your_actual_key_id

# Key Secret from Razorpay Dashboard -> Settings -> API Keys
RAZORPAY_KEY_SECRET=your_actual_razorpay_secret

# Webhook Secret configured under Razorpay Dashboard -> Settings -> Webhooks
RAZORPAY_WEBHOOK_SECRET=your_custom_webhook_secret_string

# Razorpay API Base URL (default: https://api.razorpay.com/v1)
RAZORPAY_API_URL=https://api.razorpay.com/v1

# ==============================================================================
# APPLE IN-APP PURCHASE (STOREKIT 2 & APP STORE SERVER API)
# ==============================================================================
# iOS Bundle Identifier configured in Xcode and Apple Developer Portal
APPLE_BUNDLE_ID=com.postergali.postergali

# Environment mode: 'Sandbox' for testing/TestFlight, 'Production' for App Store
APPLE_ENVIRONMENT=Production

# 10-character Key ID from App Store Connect -> Users & Access -> Integrations
APPLE_KEY_ID=2X9R427ADR

# Issuer ID (UUID) from App Store Connect -> Users & Access -> Integrations
APPLE_ISSUER_ID=57246542-96fe-1a63-e053-0824d011072a

# Complete contents of downloaded SubscriptionKey_*.p8 file
APPLE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nMIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQg...\n-----END PRIVATE KEY-----"
```

---

## 4. Applying and Testing Configuration

### Step 4.1: Refresh Laravel Configuration Cache
Whenever you edit `.env`, you must rebuild Laravel's cached configuration:
```bash
php artisan config:cache
```

### Step 4.2: Verify Registered Endpoints
Confirm that the credit routes are active:
```bash
php artisan route:list --path=credits
```
Expected output:
```text
  GET|HEAD   api/v1/credits/balance ............ API\CreditController@balance
  GET|HEAD   api/v1/credits/ledger ............. API\CreditController@ledger
  GET|HEAD   api/v1/credits/packages .......... API\CreditController@packages
  POST       api/v1/credits/topup .............. API\CreditController@topup
  GET|HEAD   api/v1/credits/transactions ....... API\CreditController@transactions
  POST       api/v1/credits/webhooks/apple ..... API\CreditController@appleWebhook
  POST       api/v1/credits/webhooks/razorpay .. API\CreditController@razorpayWebhook
```

### Step 4.3: Execute Verification Test Suite
Run the automated test suite to ensure the system is operational:
```bash
php artisan test tests/Feature/CreditTopupAndLedgerTest.php
```

---

## 5. Security & Operational Best Practices

1. **Never Commit Secrets:** Confirm that `.env` is listed in your [`.gitignore`](file:///c:/xampp/htdocs/postergali-web/.gitignore). Never commit `.env` or `.p8` files to version control.
2. **Server-Side Price Authority:** The mobile client only sends `package_id` or `plan_code`. All amounts are resolved on the server from the `plans` table to prevent price-tampering.
3. **Webhook Verification:** Both Razorpay and Apple webhooks verify cryptographic signatures against the raw HTTP payload body before performing any database transactions.
4. **Idempotency Header:** Mobile clients should send the `Idempotency-Key` header with top-up requests to prevent duplicate charges in cases of network timeouts.
