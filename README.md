# Z-Airlines Booking System

Aplikasi booking penerbangan berbasis Laravel dengan alur persetujuan admin, pembayaran Midtrans Snap, PostgreSQL Supabase, reCAPTCHA v2, data maksimal lima penumpang, reservasi kursi per penerbangan, dan history perubahan status.

## Alur booking

1. User login dan memilih penerbangan.
2. User mengajukan booking untuk satu sampai lima penumpang.
3. Data nama, gender, tanggal lahir, jenis identitas, nomor identitas, dan kursi disimpan dalam satu transaksi database.
4. Jumlah kursi penerbangan langsung direservasi agar tidak terjadi overselling.
5. Admin menerima atau menolak request melalui dashboard.
6. Tombol pembayaran Midtrans hanya muncul setelah admin menerima booking.
7. Webhook Midtrans memverifikasi signature, nominal, dan status transaksi sebelum memperbarui database.
8. Semua perubahan booking dan pembayaran dicatat di `booking_status_histories`.

## Persiapan lokal

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

## Supabase

Gunakan PostgreSQL Session Pooler dari menu **Connect** di dashboard Supabase.

```env
DB_CONNECTION=pgsql
DB_URL=postgres://postgres.PROJECT_REF:PASSWORD@REGION.pooler.supabase.com:5432/postgres
DB_SSLMODE=require
```

Lalu jalankan:

```bash
php artisan config:clear
php artisan migrate
```

## Midtrans

Masukkan Server Key dan Client Key dari Midtrans Sandbox:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-...
MIDTRANS_CLIENT_KEY=SB-Mid-client-...
MIDTRANS_IS_PRODUCTION=false
```

Atur **Payment Notification URL** di Midtrans menjadi:

```text
https://domain-aplikasi.com/payments/midtrans/notification
```

Endpoint webhook harus dapat diakses publik melalui HTTPS dan tidak boleh berada di balik halaman login.

## Google reCAPTCHA v2

Buat key reCAPTCHA v2 checkbox, lalu isi:

```env
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...
```

Saat pengembangan tanpa key, biarkan `RECAPTCHA_ENABLED=false`.

## Status

### Booking

- `pending`: request menunggu admin
- `confirmed`: request diterima dan dapat dibayar
- `cancelled`: request ditolak atau dibatalkan

### Payment

- `pending`: belum lunas
- `paid`: settlement atau capture yang valid
- `failed`: deny, cancel, expire, atau failure

## Pemeriksaan sebelum production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan test
```

Pastikan `APP_DEBUG=false`, gunakan key Midtrans Production, aktifkan reCAPTCHA, gunakan HTTPS, dan batasi akses role admin.
