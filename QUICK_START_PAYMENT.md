# ⚡ Quick Start Guide - Payment Integration

## Step 1: Get Midtrans Sandbox API Keys (5 minutes)

1. **Register Midtrans Account:**
   - Go to https://dashboard.sandbox.midtrans.com
   - Sign up with your email
   - Verify email

2. **Get API Keys:**
   - Login to dashboard
   - Click **Settings** → **Access Keys**
   - Copy **Server Key** and **Client Key**

## Step 2: Update `.env` File

Open `.env` in project root and find these lines:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY_HERE
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
MIDTRANS_IS_PRODUCTION=false
```

Replace with your actual keys from Midtrans:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-abc123xyz...
MIDTRANS_CLIENT_KEY=SB-Mid-client-def456uvw...
MIDTRANS_IS_PRODUCTION=false
```

## Step 3: Clear Laravel Cache

Run in terminal (di project root):

```bash
php artisan config:cache
```

## Step 4: Start Development Server

```bash
php artisan serve
```

Then open: http://localhost:8000

## Step 5: Test Payment Flow

1. **Go to Homepage:**
   - Click any event → "Lihat Detail"

2. **Go to Checkout:**
   - Click "Pesan Sekarang"

3. **Fill Checkout Form:**
   - Nama Lengkap: `John Doe`
   - Email: `john@example.com`
   - No. WhatsApp: `081234567890`
   - Jumlah Tiket: `1`

4. **Click "Bayar Sekarang":**
   - Snap popup akan terbuka

5. **Test Payment (use test card):**
   - Select "Bank Transfer"
   - Card Number: `4811111111111114`
   - CVV: `123`
   - Exp: `12/25`

6. **See Payment Status:**
   - Success page will show with transaction details

---

## 📧 Setup Email Testing (Optional)

Untuk test e-ticket email, setup Mailtrap:

1. **Go to:** https://mailtrap.io
2. **Sign up** (gratis)
3. **Get SMTP credentials** dari dashboard
4. **Update `.env`:**
   ```env
   MAIL_MAILER=mailtrap
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username_here
   MAIL_PASSWORD=your_password_here
   MAIL_ENCRYPTION=tls
   ```
5. **Clear cache:**
   ```bash
   php artisan config:cache
   ```

Sekarang setiap payment settlement akan otomatis mengirim e-ticket ke Mailtrap inbox.

---

## ✅ Test Payment Card Numbers

| Status | Card Number | CVV | Exp |
|---|---|---|---|
| ✅ Success | 4811111111111114 | 123 | 12/25 |
| ❌ Denied | 4911111111111113 | 123 | 12/25 |
| ⏳ Pending | 4911111111111115 | 123 | 12/25 |

---

## 🐛 Troubleshooting

### Error: "Snap token creation failed"
**Solution:**
- Check API keys di `.env` sudah benar
- Run: `php artisan config:cache`
- Restart server: `php artisan serve`

### Snap popup tidak muncul
**Solution:**
- Check browser console (F12) untuk error
- Pastikan client key di `.env` sudah benar
- Check internet connection

### Email tidak terkirim
**Solution:**
- Jika MAIL_MAILER=log: Cek `storage/logs/laravel.log`
- Jika MAIL_MAILER=mailtrap: Cek di Mailtrap inbox
- Run: `php artisan config:cache`

---

## 🔍 File Locations

**Important files to check:**

```
├── .env                                    (API Keys)
├── app/Http/Controllers/PaymentController.php
├── app/Services/PaymentService.php
├── app/Mail/SendETicket.php
├── app/Observers/TransactionObserver.php
├── config/midtrans.php
├── resources/views/checkout.blade.php
├── resources/views/payment-status.blade.php
└── resources/views/mails/eticket.blade.php
```

---

## 📞 Support

**Documentation:**
- Midtrans: https://docs.midtrans.com
- Laravel: https://laravel.com/docs

**Debug:**
- Check `storage/logs/laravel.log` for errors
- Use Midtrans dashboard transaction history
- Test webhook with https://webhook.site

---

## ✨ Summary

| Feature | Status | File |
|---|---|---|
| Guest Checkout | ✅ | checkout.blade.php |
| Midtrans Snap | ✅ | PaymentService.php |
| Payment Status | ✅ | payment-status.blade.php |
| Webhook Handler | ✅ | PaymentController@webhook |
| E-Ticket Email | ✅ | SendETicket.php |
| Order ID Generator | ✅ | PaymentController@processCheckout |

**🎉 Payment integration is 100% ready for testing!**

---

*Last Updated: May 22, 2026*
*Project: AmikomEventHub 3102*
