# 🚀 Payment Integration Implementation Guide

## ✅ Fitur yang Sudah Diimplementasikan

### 1. **Midtrans Payment Integration (Sandbox Mode)**
- ✅ SDK Midtrans PHP terinstall (`composer require midtrans/midtrans-php`)
- ✅ Konfigurasi di `config/midtrans.php`
- ✅ Environment variables di `.env`:
  ```
  MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY_HERE
  MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
  MIDTRANS_IS_PRODUCTION=false
  ```

### 2. **Guest Checkout Flow (No Login Required)**
- ✅ Form checkout dengan 3 field wajib:
  - Nama Lengkap
  - Email Aktif
  - No. WhatsApp
  - Jumlah Tiket

- ✅ Route: `GET /checkout/{event}` → PaymentController@checkout
- ✅ File: `resources/views/checkout.blade.php`

### 3. **Snap Payment Integration**
- ✅ Generate Snap Token di `PaymentService::createSnapToken()`
- ✅ Midtrans Snap Popup (Client-side)
- ✅ Order ID Generator: `ORDER-YYYYMMDDHHmmss-XXXXXX`

**Routes:**
```
POST /checkout/{event}/process → PaymentController@processCheckout
```

### 4. **Payment Status Handling**
- ✅ Real-time status update via webhook
- ✅ Status mapping:
  - `settlement` → Pembayaran Berhasil ✓
  - `pending` → Pembayaran Tertunda ⏳
  - `failed/deny/cancel/expire` → Pembayaran Gagal ✗

**Routes:**
```
GET /payment-status/{transaction} → PaymentController@paymentStatus
POST /webhook/midtrans → PaymentController@webhook (CSRF exempt)
```

### 5. **Email Ticketing (E-Ticket)**
- ✅ Auto-send e-ticket email ke customer
- ✅ Trigger: Ketika status transaksi berubah menjadi `settlement`
- ✅ Mail Template: `resources/views/mails/eticket.blade.php`
- ✅ Observer Pattern: `app/Observers/TransactionObserver.php`

**Konfigurasi Email:**
```
MAIL_MAILER=log (untuk testing lokal)
atau
MAIL_MAILER=mailtrap
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

---

## 📋 Database Structure

### Tabel: transactions
```sql
id, event_id, order_id, customer_name, customer_email, 
customer_phone, total_price, status, snap_token, timestamps
```

### Relasi:
- `Transaction::event()` → `Event::hasMany('transactions')`
- `Event::hasMany(Transaction::class)`

---

## 🔑 Setup Midtrans Sandbox

### Step 1: Daftar di Midtrans
1. Kunjungi: https://dashboard.sandbox.midtrans.com
2. Buat akun (gunakan email yang sama dengan project)
3. Login ke dashboard

### Step 2: Dapatkan API Keys
1. Masuk ke **Settings → Access Keys**
2. Copy:
   - **Server Key** → `MIDTRANS_SERVER_KEY`
   - **Client Key** → `MIDTRANS_CLIENT_KEY`

### Step 3: Update `.env`
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY_HERE
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
MIDTRANS_IS_PRODUCTION=false
```

### Step 4: Clear Cache
```bash
php artisan config:cache
```

---

## 🧪 Testing Payment Flow

### Test Card Numbers (Sandbox)
```
✓ Success Payment:
  Card: 4811111111111114
  CVV: 123
  Exp: 12/25

✗ Deny Payment:
  Card: 4911111111111113
  CVV: 123
  Exp: 12/25

⏳ Pending Payment:
  Card: 4911111111111115
  CVV: 123
  Exp: 12/25
```

### Testing Steps:
1. **Buka Homepage**: `http://localhost:8000/`
2. **Pilih Event** → Klik "Lihat Detail"
3. **Klik "Pesan Sekarang"** → Masuk ke halaman checkout
4. **Isi form checkout** dengan data dummy:
   - Nama: John Doe
   - Email: john@example.com
   - No. WhatsApp: 081234567890
   - Jumlah Tiket: 1
5. **Klik "Bayar Sekarang"** → Snap popup terbuka
6. **Pilih "Bank Transfer"** → Input test card
7. **Submit** → Status page ditampilkan
8. **Cek email** (Mailtrap/Log) untuk e-ticket

---

## 📧 Email Testing (Mailtrap Setup)

### 1. Setup Mailtrap Account
```
Kunjungi: https://mailtrap.io
Buat akun gratis
```

### 2. Dapatkan SMTP Credentials
```
Settings → API Tokens → Sending
Copy:
- Username
- Password
```

### 3. Update `.env`
```env
MAIL_MAILER=mailtrap
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@amikomeventhub.com"
MAIL_FROM_NAME="AmikomEventHub"
```

### 4. Clear Cache dan Test
```bash
php artisan config:cache
```

Setiap transaksi settlement akan otomatis mengirim e-ticket ke email customer.

---

## 🔍 Webhook Configuration (Production Only)

Ketika production, setup webhook di Midtrans Dashboard:
```
Settings → Webhook Configuration
URL: https://your-domain.com/webhook/midtrans
Select: ALL EVENTS
```

---

## 📁 File Structure

```
app/
├── Http/Controllers/
│   ├── PaymentController.php      ← NEW: Payment handling
│   └── EventController.php         (Updated)
├── Services/
│   └── PaymentService.php          ← NEW: Midtrans integration
├── Mail/
│   └── SendETicket.php             ← NEW: E-ticket email
├── Observers/
│   └── TransactionObserver.php      ← NEW: Email trigger
└── Models/
    └── Transaction.php             (Updated with fillable)

config/
└── midtrans.php                    ← NEW: Midtrans config

resources/views/
├── checkout.blade.php              (Updated with Snap)
├── payment-status.blade.php        ← NEW: Payment result
├── event-detail.blade.php          (Updated)
└── mails/
    └── eticket.blade.php           ← NEW: E-ticket template

routes/
└── web.php                         (Updated with payment routes)

.env                               (Updated with Midtrans keys)
```

---

## ✨ Environment Variables yang Diperlukan

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amikomhub_3102
DB_USERNAME=root
DB_PASSWORD=

# Mail (Choose one)
MAIL_MAILER=log                    # For local development
# or
MAIL_MAILER=mailtrap               # For testing with Mailtrap

# Midtrans (WAJIB untuk production)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
```

---

## 🚨 Important Notes

1. **API Keys**: Ganti placeholder keys di `.env` dengan real keys dari Midtrans
2. **HTTPS**: Webhook hanya berfungsi di HTTPS (production)
3. **Email**: Setup email sesuai method (Mailtrap untuk testing)
4. **Database**: Pastikan sudah run migration: `php artisan migrate`
5. **CSRF Token**: Webhook endpoint sudah exclude dari CSRF validation

---

## 🐛 Troubleshooting

### Error: "Snap token creation failed"
- Check: `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` di `.env`
- Run: `php artisan config:cache`

### Email tidak terkirim
- Jika `MAIL_MAILER=log`: Cek di `storage/logs/laravel.log`
- Jika `MAIL_MAILER=mailtrap`: Cek di Mailtrap inbox

### Payment status tidak terupdate
- Pastikan webhook sudah configured di Midtrans Dashboard (production)
- Di sandbox, cek di transaction history Midtrans

---

## 📞 Support

Untuk detail lebih lanjut:
- Midtrans Docs: https://docs.midtrans.com
- Laravel Mail: https://laravel.com/docs/mail
- Testing Webhook: Gunakan tools seperti Postman atau webhook.site

