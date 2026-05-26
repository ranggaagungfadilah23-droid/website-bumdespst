# ⚠️ TODO SEBELUM DEPLOY - WAJIB DILAKUKAN!

## 🚨 Variabel yang HARUS Diisi

File `.env` Anda masih memiliki beberapa variabel yang perlu diisi sebelum deploy ke Railway.

---

## 1. 🔑 Supabase Keys (CRITICAL!)

### Status: ❌ BELUM DIISI

```env
SUPABASE_KEY=your_supabase_anon_key              # ⚠️ GANTI INI!
SUPABASE_SERVICE_KEY=your_supabase_service_key   # ⚠️ GANTI INI!
```

### Cara Mendapatkan:

#### Step 1: Login ke Supabase
1. Buka browser
2. Pergi ke: https://app.supabase.com
3. Login dengan akun Anda

#### Step 2: Pilih Project
1. Klik project: **twbvqgjedeapqszljzox**
2. Atau project dengan URL: `https://twbvqgjedeapqszljzox.supabase.co`

#### Step 3: Buka Settings
1. Klik icon **⚙️ Settings** di sidebar kiri bawah
2. Klik **API** di menu Settings

#### Step 4: Copy Keys
Anda akan melihat section **Project API keys** dengan 2 keys:

##### A. anon public Key
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InR3YnZxZ2plZGVhcHFzemxqem94Iiwicm9sZSI6ImFub24iLCJpYXQiOjE2ODk1NzY4MDAsImV4cCI6MjAwNTE1MjgwMH0...
```
- **Label:** `anon` `public`
- **Warna:** Hijau
- Klik icon **📋 Copy** di sebelah kanan
- Paste ke `.env` sebagai `SUPABASE_KEY`

##### B. service_role secret Key
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InR3YnZxZ2plZGVhcHFzemxqem94Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTY4OTU3NjgwMCwiZXhwIjoyMDA1MTUyODAwfQ...
```
- **Label:** `service_role` `secret`
- **Warna:** Merah
- ⚠️ **SANGAT RAHASIA!** Jangan share ke siapapun!
- Klik icon **📋 Copy** di sebelah kanan
- Paste ke `.env` sebagai `SUPABASE_SERVICE_KEY`

#### Step 5: Update .env
Buka file `.env` dan ganti:

```env
# SEBELUM (SALAH):
SUPABASE_KEY=your_supabase_anon_key
SUPABASE_SERVICE_KEY=your_supabase_service_key

# SESUDAH (BENAR):
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InR3YnZxZ2plZGVhcHFzemxqem94Iiwicm9sZSI6ImFub24iLCJpYXQiOjE2ODk1NzY4MDAsImV4cCI6MjAwNTE1MjgwMH0...
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InR3YnZxZ2plZGVhcHFzemxqem94Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTY4OTU3NjgwMCwiZXhwIjoyMDA1MTUyODAwfQ...
```

✅ **Checklist:**
- [ ] `SUPABASE_KEY` sudah diisi dengan anon public key
- [ ] `SUPABASE_SERVICE_KEY` sudah diisi dengan service_role key
- [ ] Keys dimulai dengan `eyJ...`
- [ ] Keys sangat panjang (~200+ karakter)

---

## 2. 🔐 S3 Access Keys (CRITICAL!)

### Status: ⚠️ PERLU DICEK

```env
AWS_ACCESS_KEY_ID=ac519ee93adfb4babacea0e10592fde9
AWS_SECRET_ACCESS_KEY=eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
```

### Apakah Keys di Atas Sudah Benar?

**Jika BELUM pernah generate S3 Access Keys**, keys di atas kemungkinan **SALAH** atau **TIDAK VALID**.

### Cara Generate S3 Access Keys:

#### Step 1: Buka Supabase Storage Settings
1. Supabase Dashboard (https://app.supabase.com)
2. Pilih project: **twbvqgjedeapqszljzox**
3. Klik **⚙️ Settings** di sidebar kiri bawah
4. Klik **Storage** di menu Settings

#### Step 2: Generate S3 Access Key
1. Scroll ke section **S3 Access Keys**
2. Klik tombol **Create S3 Access Key**

#### Step 3: Isi Form
1. **Description:** `Laravel BUMDes Production`
2. **Permissions:**
   - ✅ **Read** (centang)
   - ✅ **Write** (centang)
   - ✅ **Delete** (centang)
3. Klik **Create**

#### Step 4: Copy Keys
Setelah dibuat, akan muncul popup dengan 2 keys:

```
Access Key ID: ac519ee93adfb4babacea0e10592fde9
Secret Access Key: eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
```

⚠️ **PENTING:**
- **Secret Access Key HANYA DITAMPILKAN SEKALI!**
- Copy dan simpan dengan aman
- Jika hilang, harus generate ulang

#### Step 5: Update .env
```env
AWS_ACCESS_KEY_ID=ac519ee93adfb4babacea0e10592fde9
AWS_SECRET_ACCESS_KEY=eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
```

✅ **Checklist:**
- [ ] S3 Access Keys sudah di-generate di Supabase
- [ ] `AWS_ACCESS_KEY_ID` sudah diisi
- [ ] `AWS_SECRET_ACCESS_KEY` sudah diisi
- [ ] Keys sudah di-copy dengan benar

---

## 3. 📦 Buat Bucket di Supabase (CRITICAL!)

### Status: ❌ BELUM DIBUAT (kemungkinan)

Bucket `produk` harus dibuat di Supabase Storage.

### Cara Membuat Bucket:

#### Step 1: Buka Storage
1. Supabase Dashboard (https://app.supabase.com)
2. Pilih project: **twbvqgjedeapqszljzox**
3. Klik **Storage** di sidebar kiri

#### Step 2: Create Bucket
1. Klik tombol **Create a new bucket** (atau **New bucket**)
2. Isi form:
   - **Name:** `produk` (harus persis seperti ini!)
   - **Public bucket:** ✅ **CENTANG INI!** (sangat penting!)
   - **File size limit:** `52428800` (50 MB)
   - **Allowed MIME types:** `image/*` (atau kosongkan)
3. Klik **Create bucket**

#### Step 3: Verifikasi
- Bucket `produk` sekarang muncul di list
- Ada icon 🌐 di sebelah nama bucket (menandakan public)
- Jika tidak ada icon 🌐, bucket masih private!

#### Step 4: Set Public (Jika Belum)
Jika bucket tidak ada icon 🌐:
1. Klik bucket `produk`
2. Klik **Settings** (icon ⚙️)
3. Toggle **Public bucket** menjadi **ON**
4. Klik **Save**

✅ **Checklist:**
- [ ] Bucket `produk` sudah dibuat
- [ ] Bucket `produk` sudah di-set **public** (ada icon 🌐)
- [ ] File size limit: 50 MB
- [ ] Allowed MIME types: `image/*`

---

## 4. 🧪 Test Upload Lokal (RECOMMENDED)

Setelah semua keys diisi dan bucket dibuat, test upload di lokal dulu sebelum deploy.

### Step 1: Buat Test Command
File sudah ada di: `app/Console/Commands/TestSupabaseUpload.php`

Jika belum ada, buat dengan:
```bash
php artisan make:command TestSupabaseUpload
```

### Step 2: Run Test
```bash
php artisan test:supabase-upload
```

### Expected Output (SUKSES):
```
Testing Supabase Storage upload...
✅ Upload successful!
File: test-1234567890.txt
URL: https://twbvqgjedeapqszljzox.supabase.co/storage/v1/object/public/produk/test-1234567890.txt
✅ File exists in storage
✅ Test file deleted
```

### Jika Error:
- **"Access Denied"** → S3 Access Keys salah atau permissions kurang
- **"Bucket not found"** → Bucket `produk` belum dibuat
- **"File not accessible"** → Bucket masih private (bukan public)
- **"Invalid endpoint"** → `AWS_ENDPOINT` salah di `.env`

✅ **Checklist:**
- [ ] Test upload berhasil
- [ ] File URL accessible
- [ ] File bisa dihapus

---

## 5. 🔗 Setup Google OAuth (IMPORTANT)

### Status: ⚠️ PERLU DICEK

Google OAuth redirect URL sudah disesuaikan di `.env`:
```env
GOOGLE_REDIRECT_URL=https://www.bumdesputrasamudra.my.id/auth/google/callback
```

Tapi Anda perlu menambahkan URL ini di Google Cloud Console.

### Cara Setup:

#### Step 1: Buka Google Cloud Console
1. Pergi ke: https://console.cloud.google.com
2. Login dengan akun Google Anda

#### Step 2: Pilih Project
1. Klik dropdown project di atas
2. Pilih project yang sesuai dengan `GOOGLE_CLIENT_ID` Anda

#### Step 3: Buka Credentials
1. Menu kiri → **APIs & Services**
2. Klik **Credentials**

#### Step 4: Edit OAuth Client
1. Cari OAuth 2.0 Client ID dengan ID: `789370408848-omfkpkmq5kkiq36c3eag1rs5u14187e6.apps.googleusercontent.com`
2. Klik icon **✏️ Edit**

#### Step 5: Add Redirect URIs
Di section **Authorized redirect URIs**, tambahkan:
```
https://www.bumdesputrasamudra.my.id/auth/google/callback
https://bumdesputrasamudra.my.id/auth/google/callback
```

⚠️ **PENTING:**
- Tambahkan KEDUA URL (dengan `www` dan tanpa `www`)
- Tidak ada trailing slash `/` di akhir
- Harus `https://` (bukan `http://`)

#### Step 6: Save
Klik **Save** di bawah

✅ **Checklist:**
- [ ] Redirect URIs sudah ditambahkan di Google Cloud Console
- [ ] Ada 2 URLs (dengan `www` dan tanpa `www`)
- [ ] Menggunakan `https://`

---

## 6. 💳 Midtrans Configuration (OPTIONAL)

### Status: ⚠️ PERLU DICEK

Saat ini menggunakan **Sandbox** keys:
```env
MIDTRANS_IS_PRODUCTION=false
```

### Untuk Testing (Sandbox)
- Keys sudah benar
- Tidak perlu diubah
- Gunakan test credit cards dari Midtrans

### Untuk Production (Live)
Jika sudah siap go-live:

1. **Ganti Keys:**
   ```env
   MIDTRANS_SERVER_KEY=[Production Server Key]
   MIDTRANS_CLIENT_KEY=[Production Client Key]
   MIDTRANS_IS_PRODUCTION=true
   ```

2. **Setup Webhook:**
   - Midtrans Dashboard → Settings → Configuration
   - **Payment Notification URL:** `https://www.bumdesputrasamudra.my.id/midtrans/webhook`
   - **Finish Redirect URL:** `https://www.bumdesputrasamudra.my.id/checkout/finish`
   - **Error Redirect URL:** `https://www.bumdesputrasamudra.my.id/checkout/error`

✅ **Checklist:**
- [ ] Menggunakan Sandbox keys untuk testing
- [ ] Atau sudah ganti ke Production keys (jika live)
- [ ] Webhook URLs sudah dikonfigurasi (jika production)

---

## 📋 Final Checklist Sebelum Deploy

### Critical (WAJIB!)
- [ ] ✅ `APP_DEBUG=false` (sudah diperbaiki)
- [ ] ✅ `APP_ENV=production` (sudah benar)
- [ ] ✅ Database Railway credentials (sudah dikonfigurasi)
- [ ] ❌ `SUPABASE_KEY` sudah diisi
- [ ] ❌ `SUPABASE_SERVICE_KEY` sudah diisi
- [ ] ⚠️ `AWS_ACCESS_KEY_ID` sudah benar
- [ ] ⚠️ `AWS_SECRET_ACCESS_KEY` sudah benar
- [ ] ❌ Bucket `produk` sudah dibuat di Supabase
- [ ] ❌ Bucket `produk` sudah public
- [ ] ❌ Test upload lokal berhasil

### Important
- [ ] ⚠️ Google OAuth redirect URIs sudah ditambahkan
- [ ] ✅ Gmail App Password sudah benar
- [ ] ✅ Midtrans keys sudah benar

### Nice to Have
- [ ] Midtrans webhook sudah dikonfigurasi (jika production)
- [ ] Domain DNS sudah siap

---

## 🚀 Setelah Semua Selesai

### 1. Commit Changes
```bash
git add .env
git commit -m "Update environment variables for Railway deployment"
git push origin main
```

⚠️ **TUNGGU!** Jangan commit `.env` ke Git!

File `.env` seharusnya sudah ada di `.gitignore`. Jika tidak:
```bash
# Cek .gitignore
cat .gitignore | grep .env

# Jika tidak ada, tambahkan:
echo .env >> .gitignore
```

### 2. Copy Variables ke Railway
Setelah `.env` lokal sudah benar, copy semua variables ke Railway:

1. Railway Dashboard → Project → Settings → Variables
2. Klik **Raw Editor**
3. Copy semua variables dari file `RAILWAY_ENV_VARIABLES.txt`
4. Paste ke Railway
5. **GANTI** variabel yang bertanda `[ISI_DENGAN_...]`
6. Klik **Save**

### 3. Deploy!
```bash
# Windows
deploy.bat

# Linux/Mac
./deploy.sh
```

---

## 📞 Need Help?

### Dokumentasi Lengkap
- **Supabase Setup:** `SUPABASE_SETUP.md`
- **Deployment Guide:** `RAILWAY_DEPLOYMENT.md`
- **Checklist:** `DEPLOYMENT_CHECKLIST.md`
- **Quick Reference:** `QUICK_REFERENCE.md`

### Support
- **Supabase Discord:** https://discord.supabase.com
- **Railway Discord:** https://discord.gg/railway

---

## ✅ Summary

### Yang Sudah Benar:
- ✅ `APP_DEBUG=false`
- ✅ `APP_ENV=production`
- ✅ Database Railway credentials
- ✅ Session domain
- ✅ Google redirect URL
- ✅ Gmail SMTP
- ✅ Midtrans keys (sandbox)

### Yang Perlu Dilakukan:
1. ❌ Isi `SUPABASE_KEY`
2. ❌ Isi `SUPABASE_SERVICE_KEY`
3. ⚠️ Generate/verify S3 Access Keys
4. ❌ Buat bucket `produk` di Supabase
5. ❌ Set bucket `produk` public
6. ❌ Test upload lokal
7. ⚠️ Add Google OAuth redirect URIs

---

**🎯 Prioritas:**
1. **Supabase Keys** (paling penting!)
2. **S3 Access Keys** (paling penting!)
3. **Bucket `produk`** (paling penting!)
4. **Test upload** (untuk verifikasi)
5. **Google OAuth** (untuk login)

**Setelah semua selesai, Anda siap deploy! 🚀**
