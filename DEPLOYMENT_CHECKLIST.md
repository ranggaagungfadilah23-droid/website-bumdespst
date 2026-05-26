# ✅ Checklist Deployment Railway - BUMDes Putra Samudra Patimban

## 📋 Pre-Deployment Checklist

### 1. Konfigurasi Environment
- [ ] `APP_DEBUG=false` ✅
- [ ] `APP_ENV=production` ✅
- [ ] `APP_URL` sesuai domain production ✅
- [ ] `LOG_LEVEL=error` untuk production ✅
- [ ] Database Railway credentials sudah benar ✅
- [ ] `DB_SSLMODE=require` ✅
- [ ] `SESSION_DOMAIN=.bumdesputrasamudra.my.id` ✅

### 2. Supabase Configuration
- [ ] `SUPABASE_KEY` sudah diisi (anon public key)
- [ ] `SUPABASE_SERVICE_KEY` sudah diisi (service_role key)
- [ ] `AWS_ACCESS_KEY_ID` sudah benar
- [ ] `AWS_SECRET_ACCESS_KEY` sudah benar
- [ ] Bucket `produk` sudah dibuat di Supabase
- [ ] Bucket `produk` sudah public

### 3. Email Configuration
- [ ] Gmail App Password sudah dibuat
- [ ] `MAIL_PASSWORD` menggunakan App Password (bukan password Gmail)
- [ ] Test kirim email berhasil

### 4. Payment Gateway (Midtrans)
- [ ] Midtrans keys sudah benar
- [ ] `MIDTRANS_IS_PRODUCTION=false` untuk testing
- [ ] Webhook URL sudah dikonfigurasi di Midtrans Dashboard
- [ ] Test payment berhasil

### 5. Google OAuth
- [ ] Google OAuth Client ID sudah dibuat
- [ ] Authorized redirect URIs sudah ditambahkan:
  - `https://www.bumdesputrasamudra.my.id/auth/google/callback`
  - `https://bumdesputrasamudra.my.id/auth/google/callback`
- [ ] `GOOGLE_REDIRECT_URL` sesuai domain production ✅

### 6. Code & Repository
- [ ] Semua perubahan sudah di-commit
- [ ] Semua perubahan sudah di-push ke repository
- [ ] `.env` tidak ter-commit (ada di `.gitignore`)
- [ ] `composer.lock` sudah ter-commit
- [ ] `package-lock.json` sudah ter-commit

### 7. Database
- [ ] Migration files sudah siap
- [ ] Seeder files sudah siap (jika diperlukan)
- [ ] Backup database lokal sudah dibuat

---

## 🚀 Deployment Steps

### Step 1: Setup Railway Project
- [ ] Login ke Railway Dashboard
- [ ] Create New Project
- [ ] Deploy from GitHub repo
- [ ] Pilih repository `sistem-bumdes`
- [ ] Railway auto-detect Laravel

### Step 2: Add MySQL Database
- [ ] Add MySQL service di Railway
- [ ] Copy database credentials
- [ ] Update environment variables

### Step 3: Configure Environment Variables
Copy semua variabel berikut ke Railway Variables:

```
APP_NAME="BUMDes Putra Samudra Patimban"
APP_ENV=production
APP_KEY=base64:grpkdBMo9nVsNNko2xfnS5Rwu3YmGNo9HnybPz8XbqE=
APP_DEBUG=false
APP_URL=https://www.bumdesputrasamudra.my.id
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.bumdesputrasamudra.my.id
BROADCAST_CONNECTION=log
QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=bumdes_cache
SUPABASE_URL=https://twbvqgjedeapqszljzox.supabase.co
SUPABASE_KEY=[ISI_DENGAN_ANON_KEY]
SUPABASE_SERVICE_KEY=[ISI_DENGAN_SERVICE_KEY]
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=ac519ee93adfb4babacea0e10592fde9
AWS_SECRET_ACCESS_KEY=eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=produk
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/s3
AWS_URL=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/object/public
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=bumdesputrasamudrapatimban@gmail.com
MAIL_PASSWORD=knccjzwlgoefmxdk
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="no-reply@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
MIDTRANS_SERVER_KEY=Mid-server-upW0xGW_gRQ3ZpUHZn55oVFL
MIDTRANS_CLIENT_KEY=Mid-client-PTpKalmIgkHo-NGM
MIDTRANS_IS_PRODUCTION=false
GOOGLE_CLIENT_ID=789370408848-omfkpkmq5kkiq36c3eag1rs5u14187e6.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URL=https://www.bumdesputrasamudra.my.id/auth/google/callback
VITE_APP_NAME="${APP_NAME}"
```

**⚠️ JANGAN COPY `DB_*` variables** - Railway akan auto-generate!

- [ ] Semua variables sudah ditambahkan
- [ ] Double-check tidak ada typo

### Step 4: Deploy
- [ ] Klik "Deploy" di Railway
- [ ] Tunggu build selesai (~5-10 menit)
- [ ] Check logs untuk error

### Step 5: Run Migration
```bash
railway run php artisan migrate --force
```
- [ ] Migration berhasil
- [ ] Semua tabel sudah dibuat

### Step 6: Setup Custom Domain
- [ ] Add custom domain di Railway: `www.bumdesputrasamudra.my.id`
- [ ] Copy CNAME record
- [ ] Tambahkan CNAME di DNS provider
- [ ] Tunggu DNS propagation (~5-60 menit)
- [ ] Test domain sudah accessible

### Step 7: Post-Deployment Commands
```bash
# Cache configuration
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache

# Generate storage link
railway run php artisan storage:link

# Clear cache (jika diperlukan)
railway run php artisan cache:clear
```
- [ ] Semua commands berhasil dijalankan

---

## 🧪 Testing Checklist

### 1. Basic Functionality
- [ ] Homepage loading dengan benar
- [ ] CSS/JS assets loading
- [ ] Images loading dari Supabase

### 2. Authentication
- [ ] Register user baru berhasil
- [ ] Login berhasil
- [ ] Logout berhasil
- [ ] Google OAuth berhasil
- [ ] Email verification berhasil (jika enabled)

### 3. Customer Features
- [ ] Browse produk/jasa
- [ ] Search produk/jasa
- [ ] Add to cart
- [ ] Checkout
- [ ] Payment Midtrans berhasil
- [ ] Order history
- [ ] Review produk/jasa

### 4. Mitra Features
- [ ] Daftar sebagai mitra
- [ ] Upload produk/jasa
- [ ] Upload gambar produk (Supabase)
- [ ] Terima pesanan
- [ ] Update status pesanan
- [ ] Lihat pendapatan
- [ ] Download laporan

### 5. Admin Features
- [ ] Login admin
- [ ] Approve/reject mitra
- [ ] Lihat semua transaksi
- [ ] Monitoring keuangan
- [ ] Generate laporan

### 6. Email Notifications
- [ ] Email registrasi
- [ ] Email verifikasi mitra
- [ ] Email status pesanan
- [ ] Email bagi hasil

### 7. Scheduled Tasks
- [ ] Auto selesaikan pesanan (cron)
- [ ] Auto kirim bagi hasil (cron)

---

## 🐛 Common Issues & Solutions

### Issue: 500 Internal Server Error
**Solution:**
```bash
railway logs
railway run php artisan config:clear
railway run php artisan cache:clear
```

### Issue: Database Connection Failed
**Solution:**
- Check `DB_SSLMODE=require`
- Verify database credentials di Railway Variables

### Issue: Images Not Loading
**Solution:**
```bash
railway run php artisan storage:link
```
- Check Supabase credentials
- Verify bucket `produk` is public

### Issue: Session/CSRF Issues
**Solution:**
- Check `SESSION_DOMAIN=.bumdesputrasamudra.my.id`
- Clear browser cookies
- Verify `APP_URL` matches domain

### Issue: Google OAuth Error
**Solution:**
- Add redirect URI di Google Cloud Console
- Check `GOOGLE_REDIRECT_URL` matches

---

## 📊 Monitoring

### Daily Checks
- [ ] Check Railway logs untuk errors
- [ ] Monitor resource usage (CPU, Memory)
- [ ] Check database size
- [ ] Verify scheduled tasks running

### Weekly Checks
- [ ] Review error logs
- [ ] Check payment transactions
- [ ] Verify email delivery
- [ ] Database backup

### Monthly Checks
- [ ] Review Railway billing
- [ ] Update dependencies
- [ ] Security audit
- [ ] Performance optimization

---

## 🔐 Security Reminders

- [ ] `APP_DEBUG=false` di production
- [ ] `.env` tidak ter-commit ke Git
- [ ] Database credentials aman
- [ ] HTTPS enabled
- [ ] CSRF protection enabled
- [ ] Rate limiting configured
- [ ] File upload validation
- [ ] SQL injection protection (use Eloquent)
- [ ] XSS protection (use Blade `{{ }}`)

---

## 📞 Emergency Contacts

**Railway Issues:**
- Railway Status: https://status.railway.app
- Railway Discord: https://discord.gg/railway

**Supabase Issues:**
- Supabase Status: https://status.supabase.com
- Supabase Discord: https://discord.supabase.com

**Midtrans Issues:**
- Midtrans Support: support@midtrans.com
- Midtrans Docs: https://docs.midtrans.com

---

## ✅ Final Checklist

- [ ] Aplikasi sudah live di production
- [ ] Domain sudah accessible
- [ ] Semua fitur sudah di-test
- [ ] Email notifications working
- [ ] Payment gateway working
- [ ] Monitoring setup
- [ ] Backup strategy in place
- [ ] Documentation complete

---

**🎉 Deployment Complete!**

Production URL: https://www.bumdesputrasamudra.my.id

**Next Steps:**
1. Monitor logs selama 24 jam pertama
2. Test semua fitur secara menyeluruh
3. Setup monitoring alerts
4. Inform users aplikasi sudah live
5. Prepare rollback plan jika ada issues

**Good luck! 🚀**
