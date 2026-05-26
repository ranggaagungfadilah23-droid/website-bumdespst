# 🚀 Quick Reference - Railway Deployment

## 📋 Checklist Cepat

### ✅ Yang Sudah Dikonfigurasi
- [x] Database Railway credentials
- [x] APP_DEBUG=false
- [x] APP_ENV=production
- [x] SESSION_DOMAIN sesuai domain
- [x] GOOGLE_REDIRECT_URL sesuai domain
- [x] LOG_LEVEL=error untuk production

### ⚠️ Yang Perlu Diisi Manual

#### 1. Supabase Keys (WAJIB!)
```env
SUPABASE_KEY=your_supabase_anon_key
SUPABASE_SERVICE_KEY=your_supabase_service_key
```

**Cara mendapatkan:**
1. Buka https://app.supabase.com
2. Pilih project → Settings → API
3. Copy `anon public` key → `SUPABASE_KEY`
4. Copy `service_role` key → `SUPABASE_SERVICE_KEY`

#### 2. S3 Access Keys (WAJIB!)
```env
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
```

**Cara mendapatkan:**
1. Supabase Dashboard → Settings → Storage
2. Klik "Create S3 Access Key"
3. Copy Access Key ID dan Secret Access Key

---

## 🚀 Deploy Commands

### Deploy ke Railway
```bash
# Windows
deploy.bat

# Linux/Mac
./deploy.sh
```

### Manual Deploy
```bash
# 1. Push code
git add .
git commit -m "Deploy to production"
git push origin main

# 2. Deploy via Railway CLI
railway up

# 3. Run migrations
railway run php artisan migrate --force

# 4. Cache configs
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache

# 5. Generate storage link
railway run php artisan storage:link
```

---

## 🐛 Troubleshooting Commands

### View Logs
```bash
# Real-time logs
railway logs --follow

# Last 100 lines
railway logs --tail 100
```

### Clear Cache
```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan view:clear
railway run php artisan route:clear
```

### Restart Application
```bash
railway restart
```

### Check Status
```bash
railway status
```

### Run Artisan Commands
```bash
railway run php artisan [command]
```

---

## 📊 Important URLs

### Production
- **Website:** https://www.bumdesputrasamudra.my.id
- **Railway Dashboard:** https://railway.app/dashboard
- **Supabase Dashboard:** https://app.supabase.com

### Development
- **Local:** http://localhost:8000
- **Repository:** [Your Git Repository URL]

---

## 🔑 Credentials Locations

### Railway
- Dashboard → Project → Settings → Variables

### Supabase
- Dashboard → Settings → API (untuk keys)
- Dashboard → Settings → Storage (untuk S3 keys)

### Google OAuth
- Google Cloud Console → APIs & Services → Credentials

### Midtrans
- Midtrans Dashboard → Settings → Access Keys

---

## 📞 Quick Support

### Railway Issues
- Status: https://status.railway.app
- Discord: https://discord.gg/railway
- Docs: https://docs.railway.app

### Supabase Issues
- Status: https://status.supabase.com
- Discord: https://discord.supabase.com
- Docs: https://supabase.com/docs

### Laravel Issues
- Docs: https://laravel.com/docs
- Forum: https://laracasts.com/discuss

---

## ⚡ Common Tasks

### Update Environment Variable
```bash
railway variables set KEY=VALUE
```

### View Environment Variables
```bash
railway variables
```

### Run Migration
```bash
railway run php artisan migrate --force
```

### Rollback Migration
```bash
railway run php artisan migrate:rollback --force
```

### Create Database Backup
```bash
railway run mysqldump -u root -p railway > backup.sql
```

### Restore Database
```bash
railway run mysql -u root -p railway < backup.sql
```

---

## 🔐 Security Reminders

- ✅ APP_DEBUG=false
- ✅ APP_ENV=production
- ✅ .env tidak di-commit
- ✅ HTTPS enabled
- ✅ CSRF protection enabled
- ✅ SQL injection protection (use Eloquent)
- ✅ XSS protection (use Blade `{{ }}`)

---

## 📝 Files Created

1. **RAILWAY_DEPLOYMENT.md** - Panduan lengkap deployment
2. **DEPLOYMENT_CHECKLIST.md** - Checklist step-by-step
3. **SUPABASE_SETUP.md** - Panduan setup Supabase
4. **RAILWAY_ENV_VARIABLES.txt** - Template environment variables
5. **deploy.bat** - Script deploy untuk Windows
6. **deploy.sh** - Script deploy untuk Linux/Mac
7. **Procfile** - Railway process file
8. **nixpacks.toml** - Railway build configuration
9. **QUICK_REFERENCE.md** - Quick reference guide (file ini)

---

## 🎯 Next Steps

1. **Isi Supabase Keys** di `.env`
2. **Isi S3 Access Keys** di `.env`
3. **Buat bucket `produk`** di Supabase (set public)
4. **Test upload** dengan `php artisan test:supabase-upload`
5. **Deploy** dengan `deploy.bat` atau `deploy.sh`
6. **Test aplikasi** di production
7. **Monitor logs** selama 24 jam pertama

---

**🎉 Good luck with your deployment!**
