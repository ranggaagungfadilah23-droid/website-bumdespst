# 🚀 Panduan Deployment Railway - BUMDes Putra Samudra Patimban

## 📋 Daftar Isi
1. [Persiapan](#persiapan)
2. [Konfigurasi Environment Variables](#konfigurasi-environment-variables)
3. [Setup Database](#setup-database)
4. [Setup Storage Supabase](#setup-storage-supabase)
5. [Deployment](#deployment)
6. [Post-Deployment](#post-deployment)
7. [Troubleshooting](#troubleshooting)

---

## 🔧 Persiapan

### Prerequisites
- Akun Railway (https://railway.app)
- Akun Supabase (https://supabase.com)
- Akun Google Cloud Console (untuk OAuth)
- Akun Midtrans (untuk payment gateway)
- Repository Git yang sudah di-push

### Checklist Sebelum Deploy
- [ ] Semua kode sudah di-commit dan push ke repository
- [ ] File `.env` sudah dikonfigurasi dengan benar
- [ ] Database migration sudah siap
- [ ] Supabase storage bucket sudah dibuat
- [ ] Domain sudah siap (bumdesputrasamudra.my.id)

---

## ⚙️ Konfigurasi Environment Variables

### 1. Variabel Aplikasi Utama

```env
# Aplikasi
APP_NAME="BUMDes Putra Samudra Patimban"
APP_ENV=production
APP_KEY=base64:grpkdBMo9nVsNNko2xfnS5Rwu3YmGNo9HnybPz8XbqE=
APP_DEBUG=false                                    # ⚠️ HARUS false di production!
APP_URL=https://www.bumdesputrasamudra.my.id

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12
```

**⚠️ PENTING:**
- `APP_DEBUG=false` - Jangan pernah `true` di production (security risk!)
- `APP_KEY` - Jangan pernah di-share atau commit ke Git
- `APP_URL` - Harus sesuai dengan domain production

### 2. Logging

```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error                                    # error/warning untuk production
```

**Tips:**
- Gunakan `LOG_LEVEL=error` di production untuk mengurangi log
- Gunakan `LOG_LEVEL=debug` hanya saat troubleshooting

### 3. Database Railway (MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=kodama.proxy.rlwy.net
DB_PORT=45150
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=aeIrnHCcieYzxtMFRhnnFthWdDrnqljf
DB_SSLMODE=require                                 # ⚠️ Wajib untuk Railway
```

**Cara Mendapatkan Credentials:**
1. Buka Railway Dashboard
2. Pilih project Anda
3. Klik service MySQL
4. Tab "Variables" akan menampilkan semua credentials
5. Copy nilai: `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

### 4. Supabase Storage (S3 Compatible)

```env
# Supabase Configuration
SUPABASE_URL=https://twbvqgjedeapqszljzox.supabase.co
SUPABASE_KEY=your_supabase_anon_key               # ⚠️ GANTI INI!
SUPABASE_SERVICE_KEY=your_supabase_service_key    # ⚠️ GANTI INI!

# Supabase Storage (S3 Compatible)
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=ac519ee93adfb4babacea0e10592fde9
AWS_SECRET_ACCESS_KEY=eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=produk
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/s3
AWS_URL=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/object/public
```

**Cara Mendapatkan Supabase Keys:**
1. Buka Supabase Dashboard (https://app.supabase.com)
2. Pilih project Anda
3. Settings → API
4. Copy:
   - `anon public` key → `SUPABASE_KEY`
   - `service_role` key → `SUPABASE_SERVICE_KEY`

**Cara Mendapatkan S3 Access Keys:**
1. Supabase Dashboard → Storage → Settings
2. Klik "Create S3 Access Key"
3. Copy `Access Key ID` dan `Secret Access Key`

### 5. Session & Cache

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.bumdesputrasamudra.my.id          # ⚠️ Perhatikan titik di depan!

BROADCAST_CONNECTION=log
QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=bumdes_cache
```

**⚠️ PENTING:**
- `SESSION_DOMAIN` harus dimulai dengan titik (`.`) untuk subdomain support
- Gunakan `database` driver untuk session di Railway (lebih reliable)

### 6. Email (Gmail SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=bumdesputrasamudrapatimban@gmail.com
MAIL_PASSWORD=knccjzwlgoefmxdk                    # App Password, bukan password Gmail!
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="no-reply@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Cara Membuat Gmail App Password:**
1. Buka Google Account → Security
2. Enable "2-Step Verification"
3. Search "App passwords"
4. Generate password untuk "Mail"
5. Copy 16-digit password → `MAIL_PASSWORD`

### 7. Payment Gateway (Midtrans)

```env
MIDTRANS_SERVER_KEY=Mid-server-upW0xGW_gRQ3ZpUHZn55oVFL
MIDTRANS_CLIENT_KEY=Mid-client-PTpKalmIgkHo-NGM
MIDTRANS_IS_PRODUCTION=false                       # ⚠️ Ubah ke true saat production!
```

**⚠️ PENTING:**
- Gunakan Sandbox keys untuk testing
- Ganti dengan Production keys saat go-live
- Set `MIDTRANS_IS_PRODUCTION=true` saat production

**Cara Mendapatkan Keys:**
1. Login ke Midtrans Dashboard
2. Settings → Access Keys
3. Copy Server Key dan Client Key
4. Untuk production, switch ke Production environment

### 8. Google OAuth

```env
GOOGLE_CLIENT_ID=789370408848-omfkpkmq5kkiq36c3eag1rs5u14187e6.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URL=https://www.bumdesputrasamudra.my.id/auth/google/callback
```

**Setup Google OAuth:**
1. Buka Google Cloud Console (https://console.cloud.google.com)
2. Pilih/buat project
3. APIs & Services → Credentials
4. Create OAuth 2.0 Client ID
5. Authorized redirect URIs: `https://www.bumdesputrasamudra.my.id/auth/google/callback`
6. Copy Client ID dan Client Secret

### 9. Vite

```env
VITE_APP_NAME="${APP_NAME}"
```

---

## 🗄️ Setup Database

### 1. Buat Database di Railway

```bash
# Railway akan otomatis membuat database MySQL
# Credentials akan tersedia di Variables tab
```

### 2. Jalankan Migration

Setelah deploy, jalankan migration via Railway CLI atau dashboard:

```bash
# Via Railway CLI
railway run php artisan migrate --force

# Atau via Railway Dashboard
# Settings → Deploy → Add Command
# Command: php artisan migrate --force
```

### 3. Seed Data (Opsional)

```bash
railway run php artisan db:seed --force
```

---

## 📦 Setup Storage Supabase

### 1. Buat Storage Bucket

1. Buka Supabase Dashboard
2. Storage → Create Bucket
3. Nama bucket: `produk`
4. Public bucket: ✅ (centang)
5. File size limit: 50MB
6. Allowed MIME types: `image/*`

### 2. Setup CORS Policy

```sql
-- Jalankan di Supabase SQL Editor
CREATE POLICY "Public Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'produk' );

CREATE POLICY "Authenticated Upload"
ON storage.objects FOR INSERT
WITH CHECK ( bucket_id = 'produk' );
```

### 3. Test Upload

```bash
# Test via artisan command
railway run php artisan test:supabase-upload
```

---

## 🚀 Deployment

### Metode 1: Deploy via Railway Dashboard

1. **Connect Repository**
   - Buka Railway Dashboard
   - New Project → Deploy from GitHub repo
   - Pilih repository `sistem-bumdes`
   - Railway akan auto-detect Laravel

2. **Configure Build**
   - Build Command: `composer install --optimize-autoloader --no-dev`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

3. **Add Environment Variables**
   - Settings → Variables
   - Copy semua variabel dari `.env` ke Railway
   - ⚠️ Jangan copy `DB_*` variables (sudah auto-generated)

4. **Deploy**
   - Klik "Deploy"
   - Tunggu build selesai (~5-10 menit)

### Metode 2: Deploy via Railway CLI

```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link project
railway link

# Deploy
railway up

# Set environment variables
railway variables set APP_DEBUG=false
railway variables set APP_ENV=production
# ... set semua variables lainnya
```

### 3. Setup Custom Domain

1. Railway Dashboard → Settings → Domains
2. Add Custom Domain: `www.bumdesputrasamudra.my.id`
3. Copy CNAME record
4. Tambahkan di DNS provider Anda:
   ```
   Type: CNAME
   Name: www
   Value: [railway-generated-domain]
   TTL: 3600
   ```
5. Tunggu DNS propagation (~5-60 menit)

---

## 🔄 Post-Deployment

### 1. Jalankan Migration

```bash
railway run php artisan migrate --force
```

### 2. Clear & Cache Config

```bash
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache
```

### 3. Generate Storage Link

```bash
railway run php artisan storage:link
```

### 4. Setup Scheduled Tasks (Cron Jobs)

Railway tidak support cron secara native. Gunakan external service:

**Opsi A: Railway Cron (Recommended)**
```bash
# Tambahkan service baru di Railway
# Command: php artisan schedule:run
# Cron: * * * * * (every minute)
```

**Opsi B: External Cron Service**
- Gunakan cron-job.org atau EasyCron
- URL: `https://www.bumdesputrasamudra.my.id/cron`
- Interval: Every minute

### 5. Test Aplikasi

Checklist testing:
- [ ] Homepage loading
- [ ] Login/Register
- [ ] Google OAuth
- [ ] Upload gambar produk
- [ ] Checkout & payment (Midtrans)
- [ ] Email notification
- [ ] Admin dashboard
- [ ] Mitra dashboard

---

## 🐛 Troubleshooting

### Error: "500 Internal Server Error"

**Solusi:**
```bash
# 1. Check logs
railway logs

# 2. Set APP_DEBUG=true temporarily
railway variables set APP_DEBUG=true

# 3. Clear cache
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan view:clear

# 4. Set APP_DEBUG=false kembali
railway variables set APP_DEBUG=false
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solusi:**
- Pastikan `DB_SSLMODE=require`
- Cek credentials database di Railway Variables
- Restart database service

### Error: "Storage: File not found"

**Solusi:**
```bash
# 1. Generate storage link
railway run php artisan storage:link

# 2. Check Supabase credentials
# Pastikan AWS_ACCESS_KEY_ID dan AWS_SECRET_ACCESS_KEY benar

# 3. Test upload
railway run php artisan test:supabase-upload
```

### Error: "Session store not set on request"

**Solusi:**
```bash
# 1. Pastikan session table exists
railway run php artisan session:table
railway run php artisan migrate --force

# 2. Clear config
railway run php artisan config:clear
```

### Error: "419 Page Expired" (CSRF Token Mismatch)

**Solusi:**
- Pastikan `SESSION_DOMAIN=.bumdesputrasamudra.my.id`
- Clear browser cookies
- Check `APP_URL` sesuai dengan domain

### Google OAuth Error: "redirect_uri_mismatch"

**Solusi:**
1. Buka Google Cloud Console
2. Credentials → Edit OAuth Client
3. Authorized redirect URIs:
   - `https://www.bumdesputrasamudra.my.id/auth/google/callback`
   - `https://bumdesputrasamudra.my.id/auth/google/callback` (tanpa www)
4. Save

### Midtrans Payment Failed

**Solusi:**
- Cek `MIDTRANS_IS_PRODUCTION` sesuai environment
- Pastikan menggunakan keys yang benar (Sandbox vs Production)
- Check Midtrans Dashboard untuk error details

---

## 📊 Monitoring & Maintenance

### 1. Check Logs

```bash
# Real-time logs
railway logs --follow

# Last 100 lines
railway logs --tail 100
```

### 2. Database Backup

```bash
# Export database
railway run mysqldump -u root -p railway > backup.sql

# Import database
railway run mysql -u root -p railway < backup.sql
```

### 3. Performance Monitoring

- Railway Dashboard → Metrics
- Monitor: CPU, Memory, Network
- Set up alerts untuk resource usage

### 4. Update Aplikasi

```bash
# 1. Push changes ke Git
git add .
git commit -m "Update feature"
git push origin main

# 2. Railway akan auto-deploy
# Atau manual trigger:
railway up
```

---

## 🔐 Security Checklist

- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production`
- [ ] Database credentials aman (tidak di-commit)
- [ ] `.env` tidak di-commit ke Git
- [ ] HTTPS enabled (Railway auto-provide)
- [ ] CSRF protection enabled
- [ ] SQL injection protection (gunakan Eloquent/Query Builder)
- [ ] XSS protection (gunakan Blade `{{ }}`)
- [ ] Rate limiting enabled
- [ ] Session secure (HTTPS only)
- [ ] File upload validation
- [ ] Email verification enabled

---

## 📞 Support

Jika ada masalah:
1. Check Railway logs: `railway logs`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Railway Discord: https://discord.gg/railway
4. Laravel Documentation: https://laravel.com/docs

---

## 📝 Catatan Penting

### Biaya Railway
- Free tier: $5 credit/month
- Estimasi usage:
  - Web service: ~$5-10/month
  - MySQL database: ~$5/month
  - Total: ~$10-15/month

### Backup Strategy
- Database: Backup otomatis setiap hari via Railway
- Files: Backup manual via Supabase Storage
- Code: Git repository

### Update Dependencies
```bash
# Update Composer packages
composer update

# Update NPM packages
npm update

# Deploy
git push origin main
```

---

**🎉 Selamat! Aplikasi BUMDes Putra Samudra Patimban sudah live di Railway!**

URL Production: https://www.bumdesputrasamudra.my.id
