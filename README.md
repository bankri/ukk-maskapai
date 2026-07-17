# Z-Airlines Booking System

Aplikasi booking penerbangan Laravel dengan approval admin, pembayaran Midtrans Snap, PostgreSQL Supabase, captcha login dan register, verifikasi email, filter booking, history status, serta rating perjalanan.

## Alur aplikasi

1. User mendaftar dan menyelesaikan reCAPTCHA.
2. Sistem mengirim email verifikasi melalui SMTP.
3. Setelah email terverifikasi, user dapat mengajukan booking untuk satu sampai lima penumpang.
4. Admin menerima atau menolak request booking.
5. Booking yang diterima dapat dibayar melalui Midtrans.
6. Webhook dan Get Status API Midtrans memperbarui pembayaran menjadi `paid` atau **Terbayar**.
7. Admin menandai perjalanan selesai setelah booking terbayar.
8. User dapat memberikan satu rating dan ulasan.
9. Rating terbaru tampil di landing page, sedangkan history booking pribadi tetap hanya terlihat oleh pemilik akun dan admin.

## Instalasi

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

Layout publik dan admin memakai asset Tailwind hasil build Vite. Jangan menambahkan kembali `cdn.tailwindcss.com`. Saat mengembangkan UI, jalankan:

```bash
npm run dev
```

## Supabase PostgreSQL

Gunakan Session Pooler Supabase:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.PROJECT_REF
DB_PASSWORD=YOUR_DATABASE_PASSWORD
DB_SSLMODE=require
```

Kemudian:

```bash
php artisan config:clear
php artisan migrate
```

## Gmail SMTP dengan App Password

Aktifkan 2-Step Verification pada akun Google, buat App Password, lalu isi:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD="YOUR_16_CHARACTER_APP_PASSWORD"
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Password yang dipakai adalah App Password, bukan password utama akun Google. Setelah mengubah `.env`:

```bash
php artisan config:clear
```

## Google reCAPTCHA v2

Daftarkan `localhost` untuk pengembangan dan domain asli untuk deployment:

```env
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...
```

Captcha diwajibkan pada form login dan register ketika konfigurasi di atas aktif.

## Midtrans

Untuk Sandbox, gunakan key dengan prefix `SB-Mid`:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-...
MIDTRANS_CLIENT_KEY=SB-Mid-client-...
MIDTRANS_IS_PRODUCTION=false
```

Atur Payment Notification URL:

```text
https://domain-aplikasi.com/payments/midtrans/notification
```

Pada localhost, webhook Midtrans biasanya tidak dapat menjangkau aplikasi. Karena itu halaman booking menyediakan tombol **Perbarui Status**, dan finish callback juga memanggil Get Status API Midtrans agar status dapat berubah menjadi **Terbayar**.

## Filter booking

Admin dan user dapat mencari berdasarkan:

- kode booking
- nama user atau penumpang
- email dan nomor identitas pada admin
- kota atau kode IATA
- status booking
- status pembayaran
- rentang tanggal keberangkatan

## Status perjalanan dan rating

Database tetap memakai status booking `pending`, `confirmed`, dan `cancelled`. Penyelesaian perjalanan dicatat pada `completed_at` agar kompatibel dengan PostgreSQL Supabase tanpa mengubah enum lama.

Rating hanya dapat dibuat apabila:

- booking diterima admin
- pembayaran berstatus `paid`
- admin telah menandai perjalanan selesai
- booking belum pernah diberi rating

## Pemeriksaan sebelum production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan test
```

Gunakan `APP_DEBUG=false`, HTTPS, key Midtrans Production, reCAPTCHA aktif, dan jangan commit file `.env` atau credential asli ke GitHub.
