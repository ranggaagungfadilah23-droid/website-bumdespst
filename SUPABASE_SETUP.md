# 🗄️ Panduan Setup Supabase - BUMDes Putra Samudra Patimban

## 📋 Daftar Isi
1. [Mendapatkan Supabase Keys](#mendapatkan-supabase-keys)
2. [Setup Storage Bucket](#setup-storage-bucket)
3. [Setup S3 Access Keys](#setup-s3-access-keys)
4. [Konfigurasi CORS](#konfigurasi-cors)
5. [Testing Upload](#testing-upload)

---

## 🔑 Mendapatkan Supabase Keys

### Step 1: Login ke Supabase Dashboard
1. Buka https://app.supabase.com
2. Login dengan akun Anda
3. Pilih project: **twbvqgjedeapqszljzox**

### Step 2: Dapatkan API Keys
1. Klik **Settings** (icon gear) di sidebar kiri
2. Klik **API** di menu Settings
3. Scroll ke bagian **Project API keys**

### Step 3: Copy Keys
Anda akan melihat 2 keys:

#### 1. `anon` `public` Key
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```
- Copy key ini
- Paste ke `.env` sebagai `SUPABASE_KEY`

#### 2. `service_role` `secret` Key
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```
- ⚠️ **PENTING:** Key ini sangat sensitif!
- Copy key ini
- Paste ke `.env` sebagai `SUPABASE_SERVICE_KEY`
- **JANGAN PERNAH** commit ke Git atau share ke publik!

### Step 4: Update .env File

Buka file `.env` dan update:

```env
SUPABASE_URL=https://twbvqgjedeapqszljzox.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...    # Paste anon key di sini
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...    # Paste service_role key di sini
```

---

## 📦 Setup Storage Bucket

### Step 1: Buka Storage
1. Di Supabase Dashboard
2. Klik **Storage** di sidebar kiri
3. Klik **Create a new bucket**

### Step 2: Buat Bucket "produk"
1. **Name:** `produk`
2. **Public bucket:** ✅ **CENTANG INI** (penting!)
3. **File size limit:** `52428800` (50 MB)
4. **Allowed MIME types:** `image/*` (atau kosongkan untuk semua tipe)
5. Klik **Create bucket**

### Step 3: Verifikasi Bucket
- Bucket `produk` sekarang muncul di list
- Icon 🌐 menandakan bucket public
- Jika tidak ada icon 🌐, bucket masih private

### Step 4: Set Bucket Public (Jika Belum)
1. Klik bucket `produk`
2. Klik **Settings** (icon gear)
3. Toggle **Public bucket** menjadi ON
4. Klik **Save**

---

## 🔐 Setup S3 Access Keys

Supabase Storage kompatibel dengan S3 API. Untuk menggunakan Laravel dengan Supabase Storage, kita perlu S3 Access Keys.

### Step 1: Generate S3 Access Keys
1. Di Supabase Dashboard
2. Klik **Settings** → **Storage**
3. Scroll ke bagian **S3 Access Keys**
4. Klik **Create S3 Access Key**

### Step 2: Isi Form
1. **Description:** `Laravel BUMDes Production`
2. **Permissions:** 
   - ✅ Read
   - ✅ Write
   - ✅ Delete
3. Klik **Create**

### Step 3: Copy Keys
Setelah dibuat, Anda akan melihat:

```
Access Key ID: ac519ee93adfb4babacea0e10592fde9
Secret Access Key: eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
```

⚠️ **PENTING:** 
- Secret Access Key **HANYA DITAMPILKAN SEKALI**
- Copy dan simpan dengan aman
- Jika hilang, harus generate ulang

### Step 4: Update .env File

```env
AWS_ACCESS_KEY_ID=ac519ee93adfb4babacea0e10592fde9
AWS_SECRET_ACCESS_KEY=eb8f3df855d134bffbdc98c10c35b1fb2f7a605df18f950fbbfcd829d55db9e9
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=produk
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/s3
AWS_URL=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/object/public
```

---

## 🌐 Konfigurasi CORS

Jika aplikasi Anda mengakses Supabase Storage dari browser (upload langsung dari frontend), Anda perlu konfigurasi CORS.

### Step 1: Buka CORS Settings
1. Supabase Dashboard → **Storage** → **Settings**
2. Scroll ke **CORS Configuration**

### Step 2: Add Allowed Origins
Tambahkan domain aplikasi Anda:

```
https://www.bumdesputrasamudra.my.id
https://bumdesputrasamudra.my.id
http://localhost:8000
```

### Step 3: Save
Klik **Save** untuk apply perubahan.

---

## 🧪 Testing Upload

### Method 1: Via Artisan Command

Buat command untuk test upload:

```bash
php artisan make:command TestSupabaseUpload
```

Edit `app/Console/Commands/TestSupabaseUpload.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestSupabaseUpload extends Command
{
    protected $signature = 'test:supabase-upload';
    protected $description = 'Test upload file ke Supabase Storage';

    public function handle()
    {
        $this->info('Testing Supabase Storage upload...');

        try {
            // Create test file
            $testContent = 'Test file from Laravel - ' . now();
            $filename = 'test-' . time() . '.txt';

            // Upload to Supabase
            $uploaded = Storage::disk('public')->put($filename, $testContent);

            if ($uploaded) {
                $this->info('✅ Upload successful!');
                $this->info('File: ' . $filename);
                
                // Get URL
                $url = Storage::disk('public')->url($filename);
                $this->info('URL: ' . $url);

                // Test if file exists
                if (Storage::disk('public')->exists($filename)) {
                    $this->info('✅ File exists in storage');
                }

                // Delete test file
                Storage::disk('public')->delete($filename);
                $this->info('✅ Test file deleted');

                return 0;
            } else {
                $this->error('❌ Upload failed');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
```

Jalankan test:

```bash
php artisan test:supabase-upload
```

**Expected Output:**
```
Testing Supabase Storage upload...
✅ Upload successful!
File: test-1234567890.txt
URL: https://twbvqgjedeapqszljzox.supabase.co/storage/v1/object/public/produk/test-1234567890.txt
✅ File exists in storage
✅ Test file deleted
```

### Method 2: Via Tinker

```bash
php artisan tinker
```

```php
// Test upload
Storage::disk('public')->put('test.txt', 'Hello Supabase!');

// Get URL
Storage::disk('public')->url('test.txt');

// Check if exists
Storage::disk('public')->exists('test.txt');

// Delete
Storage::disk('public')->delete('test.txt');
```

### Method 3: Via Web Interface

1. Login sebagai Mitra
2. Buka halaman tambah produk
3. Upload gambar produk
4. Submit form
5. Check di Supabase Dashboard → Storage → produk
6. File harus muncul di bucket

---

## 🐛 Troubleshooting

### Error: "Access Denied"

**Penyebab:**
- S3 Access Keys salah
- Permissions tidak cukup

**Solusi:**
1. Regenerate S3 Access Keys
2. Pastikan permissions Read, Write, Delete dicentang
3. Update `.env` dengan keys baru
4. Clear config: `php artisan config:clear`

### Error: "Bucket not found"

**Penyebab:**
- Bucket `produk` belum dibuat
- Nama bucket salah di `.env`

**Solusi:**
1. Buat bucket `produk` di Supabase Dashboard
2. Pastikan `AWS_BUCKET=produk` di `.env`
3. Clear config: `php artisan config:clear`

### Error: "CORS policy"

**Penyebab:**
- Domain tidak ada di CORS allowed origins

**Solusi:**
1. Tambahkan domain di Supabase Storage CORS settings
2. Pastikan format: `https://www.bumdesputrasamudra.my.id` (tanpa trailing slash)

### Error: "File not accessible"

**Penyebab:**
- Bucket masih private

**Solusi:**
1. Set bucket `produk` menjadi public
2. Supabase Dashboard → Storage → produk → Settings
3. Toggle **Public bucket** ON

### Error: "Invalid endpoint"

**Penyebab:**
- `AWS_ENDPOINT` salah

**Solusi:**
Pastikan endpoint benar:
```env
AWS_ENDPOINT=https://twbvqgjedeapqszljzox.supabase.co/storage/v1/s3
```

**BUKAN:**
```env
AWS_ENDPOINT=https://twbvqgjedeapqszljzox.supabase.co/storage/v1
```

---

## 📊 Monitoring Storage Usage

### Check Storage Size
1. Supabase Dashboard → **Settings** → **Usage**
2. Lihat **Storage** usage
3. Free tier: 1 GB storage

### Check Files
1. Supabase Dashboard → **Storage** → **produk**
2. Browse semua files yang ter-upload
3. Download, delete, atau view file

### Storage Policies

Untuk keamanan lebih, Anda bisa set Row Level Security (RLS) policies:

```sql
-- Allow public read
CREATE POLICY "Public Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'produk' );

-- Allow authenticated users to upload
CREATE POLICY "Authenticated Upload"
ON storage.objects FOR INSERT
WITH CHECK ( 
  bucket_id = 'produk' 
  AND auth.role() = 'authenticated' 
);

-- Allow users to delete their own files
CREATE POLICY "User Delete Own Files"
ON storage.objects FOR DELETE
USING ( 
  bucket_id = 'produk' 
  AND auth.uid() = owner 
);
```

---

## ✅ Checklist Setup Supabase

- [ ] Supabase project sudah dibuat
- [ ] `SUPABASE_KEY` (anon key) sudah di-copy ke `.env`
- [ ] `SUPABASE_SERVICE_KEY` (service_role key) sudah di-copy ke `.env`
- [ ] Bucket `produk` sudah dibuat
- [ ] Bucket `produk` sudah di-set public
- [ ] S3 Access Keys sudah di-generate
- [ ] `AWS_ACCESS_KEY_ID` sudah di-copy ke `.env`
- [ ] `AWS_SECRET_ACCESS_KEY` sudah di-copy ke `.env`
- [ ] CORS sudah dikonfigurasi (jika perlu)
- [ ] Test upload berhasil
- [ ] File accessible via URL public

---

## 📞 Support

**Supabase Documentation:**
- Storage: https://supabase.com/docs/guides/storage
- S3 API: https://supabase.com/docs/guides/storage/s3/authentication

**Supabase Community:**
- Discord: https://discord.supabase.com
- GitHub: https://github.com/supabase/supabase

---

**🎉 Setup Supabase Complete!**

Sekarang aplikasi Anda sudah bisa upload file ke Supabase Storage! 🚀
