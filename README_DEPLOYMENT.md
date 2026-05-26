# 🚀 Railway Deployment - BUMDes Putra Samudra Patimban

## ✅ Apa yang Sudah Dilakukan?

Saya telah memperbaiki dan menyiapkan semua file untuk deployment Railway:

### 1. File `.env` Diperbaiki ✅
- `APP_DEBUG=false` (sebelumnya `true` - **CRITICAL FIX**)
- `LOG_LEVEL=error` (untuk production)
- Database Railway credentials sudah dikonfigurasi
- `SESSION_DOMAIN=.bumdesputrasamudra.my.id`
- `GOOGLE_REDIRECT_URL` sesuai domain production
- Supabase configuration ditambahkan

### 2. Dokumentasi Lengkap Dibuat ✅
- **RAILWAY_DEPLOYMENT.md** - Panduan lengkap deployment
- **DEPLOYMENT_CHECKLIST.md** - Checklist step-by-step
- **SUPABASE_SETUP.md** - Panduan setup Supabase
- **TODO_BEFORE_DEPLOY.md** - Daftar yang harus dilakukan
- **QUICK_REFERENCE.md** - Quick reference commands
- **DEPLOYMENT_SUMMARY.md** - Summary lengkap

### 3. Script Deployment Dibuat ✅
- **deploy.bat** - Script deploy untuk Windows
- **deploy.sh** - Script deploy untuk Linux/Mac

### 4. File Konfigurasi Railway ✅
- **Procfile** - Railway process configuration
- **nixpacks.toml** - Railway build configuration

---

## ⚠️ Yang HARUS Anda Lakukan Sekarang

### 🔴 CRITICAL - Wajib Dilakukan!

#### 1. Isi Supabase Keys
Buka https://app.supabase.com → Settings → API

```env
SUPABASE_KEY=[copy anon public key]
SUPABASE_SERVICE_KEY=[copy service_role key]
```

#### 2. Generate S3 Access Keys
Supabase Dashboard → Settings → Storage → Create S3 Access Key

```env
AWS_ACCESS_KEY_ID=[copy access key id]
AWS_SECRET_ACCESS_KEY=[copy secret access key]
```

#### 3. Buat Bucket di Supabase
Supabase Dashboard → Storage → Create bucket

- Name: `produk`
- Public: ✅ **CENTANG!**
- Size limit: 50 MB

#### 4. Test Upload Lokal
```bash
php artisan test:supabase-upload
```

Expected: ✅ Upload successful!

---

## 🚀 Cara Deploy

### Quick Deploy (Recommended)

```bash
# Windows
deploy.bat

# Linux/Mac
chmod +x deploy.sh
./deploy.sh
```

### Manual Deploy

1. **Push ke Git**
   ```bash
   git add .
   git commit -m "Prepare for Railway deployment"
   git push origin main
   ```

2. **Deploy via Railway CLI**
   ```bash
   railway up
   ```

3. **Run Migrations**
   ```bash
   railway run php artisan migrate --force
   ```

4. **Cache Configs**
   ```bash
   railway run php artisan config:cache
   railway run php artisan route:cache
   railway run php artisan view:cache
   ```

5. **Generate Storage Link**
   ```bash
   railway run php artisan storage:link
   ```

---

## 📚 Dokumentasi

### Baca Ini Dulu:
1. **TODO_BEFORE_DEPLOY.md** ← **MULAI DARI SINI!**
   - Daftar lengkap yang harus dilakukan
   - Cara mendapatkan Supabase keys
   - Cara membuat bucket
   - Cara test upload

2. **DEPLOYMENT_CHECKLIST.md**
   - Checklist step-by-step deployment
   - Testing checklist
   - Troubleshooting

### Referensi:
3. **RAILWAY_DEPLOYMENT.md** - Panduan lengkap deployment
4. **SUPABASE_SETUP.md** - Panduan setup Supabase
5. **QUICK_REFERENCE.md** - Quick reference commands
6. **DEPLOYMENT_SUMMARY.md** - Summary semua perubahan

---

## 🎯 Langkah Cepat

### 1. Isi Variabel (15 menit)
- [ ] Supabase keys
- [ ] S3 access keys
- [ ] Buat bucket `produk`

### 2. Test Lokal (5 menit)
```bash
php artisan test:supabase-upload
```

### 3. Deploy (10 menit)
```bash
deploy.bat  # atau ./deploy.sh
```

### 4. Test Production (10 menit)
- [ ] Homepage loading
- [ ] Login/Register
- [ ] Upload gambar
- [ ] Checkout

**Total: ~40 menit** ⏱️

---

## 🐛 Troubleshooting

### Error: "Access Denied" (Supabase)
→ S3 Access Keys salah atau belum di-generate

### Error: "Bucket not found"
→ Bucket `produk` belum dibuat di Supabase

### Error: "500 Internal Server Error"
```bash
railway logs
railway run php artisan config:clear
```

### Error: "Database connection failed"
→ Check `DB_SSLMODE=require` (sudah ada ✅)

**Troubleshooting lengkap:** Lihat `RAILWAY_DEPLOYMENT.md`

---

## 📞 Support

### Dokumentasi
- Railway: https://docs.railway.app
- Supabase: https://supabase.com/docs
- Laravel: https://laravel.com/docs

### Community
- Railway Discord: https://discord.gg/railway
- Supabase Discord: https://discord.supabase.com

---

## ✅ Checklist Cepat

### Sebelum Deploy
- [ ] `SUPABASE_KEY` sudah diisi
- [ ] `SUPABASE_SERVICE_KEY` sudah diisi
- [ ] `AWS_ACCESS_KEY_ID` sudah diisi
- [ ] `AWS_SECRET_ACCESS_KEY` sudah diisi
- [ ] Bucket `produk` sudah dibuat & public
- [ ] Test upload lokal berhasil

### Deploy
- [ ] Code sudah di-push ke Git
- [ ] Deploy script berhasil
- [ ] Migrations berhasil
- [ ] Configs cached

### After Deploy
- [ ] Aplikasi accessible
- [ ] Login/Register works
- [ ] Upload gambar works
- [ ] Payment works
- [ ] Email works

---

## 🎉 Ready to Deploy!

**Mulai dari:** `TODO_BEFORE_DEPLOY.md`

**Deploy dengan:** `deploy.bat` (Windows) atau `./deploy.sh` (Linux/Mac)

**Good luck! 🚀**

---

## 📁 File Structure

```
sistem-bumdes/
├── 📄 README_DEPLOYMENT.md          ← Anda di sini
├── 📄 TODO_BEFORE_DEPLOY.md         ← Mulai dari sini!
├── 📄 DEPLOYMENT_CHECKLIST.md
├── 📄 RAILWAY_DEPLOYMENT.md
├── 📄 SUPABASE_SETUP.md
├── 📄 QUICK_REFERENCE.md
├── 📄 DEPLOYMENT_SUMMARY.md
├── 📄 RAILWAY_ENV_VARIABLES.txt
├── 🔧 .env                          ← Sudah diperbaiki
├── 🚀 deploy.bat                    ← Deploy script (Windows)
├── 🚀 deploy.sh                     ← Deploy script (Linux/Mac)
├── ⚙️ Procfile
└── ⚙️ nixpacks.toml
```

---

**Next Step:** Buka `TODO_BEFORE_DEPLOY.md` dan ikuti instruksinya! 📖
